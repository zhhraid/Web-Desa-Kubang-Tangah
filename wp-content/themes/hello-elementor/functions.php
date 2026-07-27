<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.4.7' );
define( 'EHP_THEME_SLUG', 'hello-elementor' );

define( 'HELLO_THEME_PATH', get_template_directory() );
define( 'HELLO_THEME_URL', get_template_directory_uri() );
define( 'HELLO_THEME_ASSETS_PATH', HELLO_THEME_PATH . '/assets/' );
define( 'HELLO_THEME_ASSETS_URL', HELLO_THEME_URL . '/assets/' );
define( 'HELLO_THEME_SCRIPTS_PATH', HELLO_THEME_ASSETS_PATH . 'js/' );
define( 'HELLO_THEME_SCRIPTS_URL', HELLO_THEME_ASSETS_URL . 'js/' );
define( 'HELLO_THEME_STYLE_PATH', HELLO_THEME_ASSETS_PATH . 'css/' );
define( 'HELLO_THEME_STYLE_URL', HELLO_THEME_ASSETS_URL . 'css/' );
define( 'HELLO_THEME_IMAGES_PATH', HELLO_THEME_ASSETS_PATH . 'images/' );
define( 'HELLO_THEME_IMAGES_URL', HELLO_THEME_ASSETS_URL . 'images/' );

/**
 * Public-facing village URLs. The old page slugs remain as internal template
 * targets so the existing WordPress pages do not need to be renamed in the DB.
 *
 * @return array<string,string>
 */
function hello_elementor_village_page_aliases() {
	return [
		'profil-desa'        => 'profil_desa',
		'pemerintahan-desa'  => 'pemerintahan_desa',
		'statistik-desa'     => 'data-infografis',
		'informasi-desa'     => 'data',
	];
}

/**
 * Build public URL for custom village pages.
 *
 * @param string $alias Public alias without leading slash.
 * @return string
 */
function hello_elementor_village_page_url( $alias ) {
	return home_url( '/' . trim( $alias, '/' ) . '/' );
}

/**
 * Convert internal village page slugs to their public aliases.
 *
 * @param string $internal_slug Existing WordPress page slug.
 * @return string|null
 */
function hello_elementor_village_alias_for_internal_slug( $internal_slug ) {
	$aliases = array_flip( hello_elementor_village_page_aliases() );

	return $aliases[ $internal_slug ] ?? null;
}

/**
 * Make WordPress-generated permalinks use the public aliases too.
 *
 * @param string $link    Generated page link.
 * @param int    $post_id Page ID.
 * @return string
 */
function hello_elementor_village_alias_page_link( $link, $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		return $link;
	}

	$alias = hello_elementor_village_alias_for_internal_slug( $post->post_name );

	return $alias ? hello_elementor_village_page_url( $alias ) : $link;
}
add_filter( 'page_link', 'hello_elementor_village_alias_page_link', 10, 2 );

/**
 * Return request path relative to the WordPress home path.
 *
 * @param string|null $url Full URL or null for current request.
 * @return string
 */
function hello_elementor_village_relative_request_path( $url = null ) {
	$path      = null === $url ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) : $url;
	$path      = (string) wp_parse_url( $path, PHP_URL_PATH );
	$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$path      = trim( $path, '/' );
	$home_path = trim( $home_path, '/' );

	if ( '' !== $home_path && 0 === strpos( $path . '/', $home_path . '/' ) ) {
		$path = trim( substr( $path, strlen( $home_path ) ), '/' );
	}

	return $path;
}

/**
 * Map clean public aliases to existing WordPress page slugs.
 *
 * @param array<string,mixed> $query_vars Parsed query variables.
 * @return array<string,mixed>
 */
function hello_elementor_village_alias_request( $query_vars ) {
	$aliases = hello_elementor_village_page_aliases();
	$path    = hello_elementor_village_relative_request_path();

	if ( isset( $aliases[ $path ] ) ) {
		$query_vars = [
			'pagename' => $aliases[ $path ],
		];
	}

	return $query_vars;
}
add_filter( 'request', 'hello_elementor_village_alias_request' );

/**
 * Prevent WordPress from canonicalizing pretty aliases back to old DB slugs.
 *
 * @param string|false $redirect_url Requested canonical redirect URL.
 * @param string       $requested_url Original requested URL.
 * @return string|false
 */
function hello_elementor_village_keep_alias_urls( $redirect_url, $requested_url ) {
	$aliases = hello_elementor_village_page_aliases();
	$path    = hello_elementor_village_relative_request_path( $requested_url );

	if ( isset( $aliases[ $path ] ) ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'hello_elementor_village_keep_alias_urls', 10, 2 );

/**
 * Redirect old internal page slugs to the public aliases.
 *
 * @return void
 */
function hello_elementor_village_redirect_old_page_slugs() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$path         = hello_elementor_village_relative_request_path();
	$alias_by_old = array_flip( hello_elementor_village_page_aliases() );

	if ( ! isset( $alias_by_old[ $path ] ) ) {
		return;
	}

	$target = hello_elementor_village_page_url( $alias_by_old[ $path ] );
	$query  = (string) wp_unslash( $_SERVER['QUERY_STRING'] ?? '' );

	if ( '' !== $query ) {
		$target .= '?' . $query;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'hello_elementor_village_redirect_old_page_slugs', 1 );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
					'navigation-widgets',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'assets/css/editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_village_whatsapp_url' ) ) {
	/**
	 * Build the official WhatsApp contact URL for Desa Kubang Tangah.
	 *
	 * @return string
	 */
	function hello_elementor_village_whatsapp_url() {
		return 'https://api.whatsapp.com/send?' . http_build_query(
			[
				'phone' => '6285271664112',
				'text'  => 'Halo Pak Rice, saya ingin bertanya tentang Desa Kubang Tangah.',
			],
			'',
			'&',
			PHP_QUERY_RFC3986
		);
	}
}

if ( ! function_exists( 'hello_elementor_village_redirect_contact_page' ) ) {
	/**
	 * Send the old contact page directly to the village WhatsApp contact.
	 *
	 * @return void
	 */
	function hello_elementor_village_redirect_contact_page() {
		if ( ! is_page( 'kontak' ) ) {
			return;
		}

		wp_redirect( hello_elementor_village_whatsapp_url(), 302 ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}
}
add_action( 'template_redirect', 'hello_elementor_village_redirect_contact_page' );

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				HELLO_THEME_STYLE_URL . 'reset.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				HELLO_THEME_STYLE_URL . 'theme.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				HELLO_THEME_STYLE_URL . 'header-footer.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);

			wp_enqueue_style(
				'hello-elementor-village-header',
				HELLO_THEME_STYLE_URL . 'village-header.css',
				[ 'hello-elementor-header-footer' ],
				filemtime( HELLO_THEME_STYLE_PATH . 'village-header.css' )
			);

			wp_enqueue_style(
				'hello-elementor-village-footer',
				HELLO_THEME_STYLE_URL . 'village-footer.css',
				[ 'hello-elementor-header-footer' ],
				filemtime( HELLO_THEME_STYLE_PATH . 'village-footer.css' )
			);

			if ( is_front_page() || is_page( 'data-infografis' ) ) {
				wp_enqueue_style(
					'hello-elementor-village-home',
					HELLO_THEME_STYLE_URL . 'village-home.css',
					[ 'hello-elementor-village-header', 'hello-elementor-village-footer' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-home.css' )
				);
			}

			if ( is_front_page() ) {
				wp_enqueue_script(
					'hello-elementor-village-home',
					HELLO_THEME_SCRIPTS_URL . 'village-home.js',
					[],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-home.js' ),
					true
				);
			}

			if ( is_page( 'data-infografis' ) ) {
				wp_enqueue_style(
					'hello-elementor-village-infographics',
					HELLO_THEME_STYLE_URL . 'village-infographics.css',
					[ 'hello-elementor-village-home' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-infographics.css' )
				);

				wp_enqueue_script(
					'hello-elementor-village-infographics',
					HELLO_THEME_SCRIPTS_URL . 'village-infographics.js',
					[],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-infographics.js' ),
					true
				);
			}

			if ( is_page( 'profil_desa' ) ) {
				$leaflet_path = get_template_directory() . '/assets/vendor/leaflet/';

				wp_enqueue_style(
					'hello-elementor-leaflet',
					get_template_directory_uri() . '/assets/vendor/leaflet/leaflet.css',
					[],
					filemtime( $leaflet_path . 'leaflet.css' )
				);

				wp_enqueue_style(
					'hello-elementor-village-profile',
					HELLO_THEME_STYLE_URL . 'village-profile.css',
					[ 'hello-elementor-village-header', 'hello-elementor-village-footer', 'hello-elementor-leaflet' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-profile.css' )
				);

				wp_enqueue_script(
					'hello-elementor-leaflet',
					get_template_directory_uri() . '/assets/vendor/leaflet/leaflet.js',
					[],
					filemtime( $leaflet_path . 'leaflet.js' ),
					true
				);

				wp_enqueue_script(
					'hello-elementor-village-profile',
					HELLO_THEME_SCRIPTS_URL . 'village-profile.js',
					[ 'hello-elementor-leaflet' ],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-profile.js' ),
					true
				);
			}

			if ( is_page( 'pemerintahan_desa' ) ) {
				wp_enqueue_style(
					'hello-elementor-village-government',
					HELLO_THEME_STYLE_URL . 'village-government.css',
					[ 'hello-elementor-village-header', 'hello-elementor-village-footer' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-government.css' )
				);

				wp_enqueue_script(
					'hello-elementor-village-government',
					HELLO_THEME_SCRIPTS_URL . 'village-government.js',
					[],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-government.js' ),
					true
				);
			}

			if ( is_page( 'berita' ) || is_singular( 'post' ) ) {
				wp_enqueue_style(
					'hello-elementor-village-news',
					HELLO_THEME_STYLE_URL . 'village-news.css',
					[ 'hello-elementor-village-header', 'hello-elementor-village-footer' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-news.css' )
				);

				wp_enqueue_script(
					'hello-elementor-village-news',
					HELLO_THEME_SCRIPTS_URL . 'village-news.js',
					[],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-news.js' ),
					true
				);
			}

			if ( is_page( 'galeri' ) ) {
				wp_enqueue_style(
					'hello-elementor-village-gallery',
					HELLO_THEME_STYLE_URL . 'village-gallery.css',
					[ 'hello-elementor-village-header', 'hello-elementor-village-footer' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-gallery.css' )
				);

				wp_enqueue_script(
					'hello-elementor-village-gallery',
					HELLO_THEME_SCRIPTS_URL . 'village-gallery.js',
					[],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-gallery.js' ),
					true
				);
			}

			if ( is_page( 'data' ) ) {
				wp_enqueue_style(
					'hello-elementor-village-information',
					HELLO_THEME_STYLE_URL . 'village-information.css',
					[ 'hello-elementor-village-header', 'hello-elementor-village-footer' ],
					filemtime( HELLO_THEME_STYLE_PATH . 'village-information.css' )
				);

				wp_enqueue_script(
					'hello-elementor-village-information',
					HELLO_THEME_SCRIPTS_URL . 'village-information.js',
					[],
					filemtime( HELLO_THEME_SCRIPTS_PATH . 'village-information.js' ),
					true
				);
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

/**
 * Add stable classes for custom village pages that use dedicated templates.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function hello_elementor_village_body_classes( $classes ) {
	if ( is_page( 'profil_desa' ) ) {
		$classes[] = 'village-profile-page';
	}

	return $classes;
}
add_filter( 'body_class', 'hello_elementor_village_body_classes' );

/**
 * Custom village templates do not use Elementor widgets, so the frontend
 * runtime is unnecessary and can otherwise emit a missing configuration error.
 */
function hello_elementor_village_profile_dequeue_unused_scripts() {
	if ( ! is_page( [ 'profil_desa', 'pemerintahan_desa', 'data-infografis', 'berita', 'galeri', 'data' ] ) && ! is_singular( 'post' ) ) {
		return;
	}

	$unused_scripts = [
		'eael-general',
		'elementor-frontend',
		'elementor-frontend-modules',
		'elementor-webpack-runtime',
	];

	foreach ( $unused_scripts as $script ) {
		wp_dequeue_script( $script );
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_village_profile_dequeue_unused_scripts', 1000 );

/**
 * Repair encoding artifacts retained in imported village news content.
 *
 * @param string $value Source value.
 * @return string
 */
function hello_elementor_village_news_clean_encoding( $value ) {
	return str_replace(
		[
			"\xC6\x92??",
			"\xC2\xB6\xC3\xBF",
			"\xC3\xA2\xE2\x82\xAC\xE2\x80\x9C",
			"\xC3\xA2\xE2\x82\xAC\xE2\x80\x9D",
			"\xC3\xA2\xE2\x82\xAC\xE2\x84\xA2",
			"\xC3\xA2\xE2\x82\xAC\xC5\x93",
			"\xC3\x82",
		],
		[ '-', ' ', '-', '-', "'", '"', '' ],
		(string) $value
	);
}

/**
 * Return a clean plain-text value for village news cards.
 *
 * @param string $value Source value.
 * @return string
 */
function hello_elementor_village_news_clean_text( $value ) {
	$value = hello_elementor_village_news_clean_encoding( html_entity_decode( wp_strip_all_tags( $value ), ENT_QUOTES, 'UTF-8' ) );
	$value = preg_replace( '/\s+/u', ' ', $value );

	return trim( (string) $value );
}

/**
 * Apply repaired titles to imported village posts and their metadata.
 *
 * @param string $title   Post title.
 * @param int    $post_id Post ID.
 * @return string
 */
function hello_elementor_village_news_clean_post_title( $title, $post_id = 0 ) {
	if ( $post_id && 'post' === get_post_type( $post_id ) ) {
		return hello_elementor_village_news_clean_text( $title );
	}

	return $title;
}
add_filter( 'the_title', 'hello_elementor_village_news_clean_post_title', 20, 2 );

/**
 * Repair imported characters in browser and SEO titles for news articles.
 *
 * @param string $value Generated title value.
 * @return string
 */
function hello_elementor_village_news_clean_generated_title( $value ) {
	return is_singular( 'post' ) ? hello_elementor_village_news_clean_encoding( $value ) : $value;
}
add_filter( 'wpseo_title', 'hello_elementor_village_news_clean_generated_title', 20 );
add_filter( 'wpseo_opengraph_title', 'hello_elementor_village_news_clean_generated_title', 20 );
add_filter( 'wpseo_twitter_title', 'hello_elementor_village_news_clean_generated_title', 20 );

/**
 * Resolve a local news image, including posts without a featured image.
 *
 * @param int    $post_id      Post ID.
 * @param string $fallback_url Optional fallback image.
 * @return string
 */
function hello_elementor_village_news_image( $post_id, $fallback_url = '' ) {
	$thumbnail = get_the_post_thumbnail_url( $post_id, 'large' );
	if ( $thumbnail ) {
		return $thumbnail;
	}

	$content = (string) get_post_field( 'post_content', $post_id );
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches ) ) {
		$image_url = html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' );
		$path      = (string) wp_parse_url( $image_url, PHP_URL_PATH );
		$marker    = '/wp-content/uploads/';
		$position  = strpos( $path, $marker );

		if ( false !== $position ) {
			$relative   = ltrim( substr( $path, $position + strlen( $marker ) ), '/' );
			$upload_dir = wp_get_upload_dir();

			return trailingslashit( $upload_dir['baseurl'] ) . $relative;
		}

		return $image_url;
	}

	return $fallback_url;
}

/**
 * Infer a useful public-facing topic when imported posts only use "Blog".
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hello_elementor_village_news_topic( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return 'Berita Desa';
	}

	$searchable = strtolower( hello_elementor_village_news_clean_text( $post->post_title . ' ' . $post->post_content ) );
	if ( false !== strpos( $searchable, 'bumdes' ) || false !== strpos( $searchable, 'penyertaan modal' ) ) {
		return 'Pemerintahan';
	}
	if ( false !== strpos( $searchable, 'senam' ) || false !== strpos( $searchable, 'kesehatan' ) ) {
		return 'Kesehatan';
	}
	if ( false !== strpos( $searchable, 'kkn' ) || false !== strpos( $searchable, 'unand' ) || false !== strpos( $searchable, 'unp' ) ) {
		return 'Kegiatan';
	}

	$categories = get_the_category( $post_id );
	if ( ! empty( $categories ) && 'Blog' !== $categories[0]->name ) {
		return hello_elementor_village_news_clean_text( $categories[0]->name );
	}

	return 'Berita Desa';
}

/**
 * Build a concise excerpt from imported news content.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function hello_elementor_village_news_excerpt( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}

	$source = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
	$text   = hello_elementor_village_news_clean_text( $source );
	$title  = hello_elementor_village_news_clean_text( $post->post_title );

	if ( $title && 0 === strpos( $text, $title ) ) {
		$text = trim( substr( $text, strlen( $title ) ) );
	}

	$months = 'Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember';
	$text   = preg_replace( '/^\s*\d{1,2}\s+(?:' . $months . ')\s+\d{4}(?:,\s*\d{1,2}:\d{2}\s*WIB)?\s*/i', '', $text );

	return wp_trim_words( trim( (string) $text ), 28, '...' );
}

/**
 * Elementor header/footer assignments on imported posts bypass single.php.
 * Keep village news details on the matching custom theme template.
 *
 * @param string $template Selected template path.
 * @return string
 */
function hello_elementor_village_news_single_template( $template ) {
	if ( ! is_singular( 'post' ) ) {
		return $template;
	}

	$custom_template = HELLO_THEME_PATH . '/single.php';

	return file_exists( $custom_template ) ? $custom_template : $template;
}
add_filter( 'template_include', 'hello_elementor_village_news_single_template', 9999 );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

require HELLO_THEME_PATH . '/theme.php';

HelloTheme\Theme::instance();
