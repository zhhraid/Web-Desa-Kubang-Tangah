<?php
/**
 * The template for displaying header.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$site_name = get_bloginfo( 'name' );
$logo_url  = content_url( 'uploads/2026/01/logokt-100x100.png' );
$whatsapp_url = function_exists( 'hello_elementor_village_whatsapp_url' ) ? hello_elementor_village_whatsapp_url() : 'https://api.whatsapp.com/send?phone=6285271664112';
$menu_items = [
	[ 'label' => 'Beranda', 'path' => '/', 'current' => is_front_page() ],
	[ 'label' => 'Profil', 'path' => '/profil_desa/', 'current' => is_page( 'profil_desa' ) ],
	[ 'label' => 'Pemerintahan', 'path' => '/pemerintahan_desa/', 'current' => is_page( 'pemerintahan_desa' ) ],
	[ 'label' => 'Berita', 'path' => '/berita/', 'current' => is_page( 'berita' ) || is_singular( 'post' ) ],
	[ 'label' => 'Statistik Desa', 'path' => '/data-infografis/', 'current' => is_page( 'data-infografis' ) ],
	[ 'label' => 'Informasi', 'path' => '/data/', 'current' => is_page( 'data' ) ],
	[ 'label' => 'Galeri', 'path' => '/galeri/', 'current' => is_page( 'galeri' ) ],
	[ 'label' => 'Kontak', 'url' => $whatsapp_url, 'current' => false, 'cta' => true, 'target' => '_blank', 'rel' => 'noopener noreferrer', 'aria_label' => 'Kontak WhatsApp Pak Rice' ],
];
?>

<header id="site-header" class="site-header village-header">
	<div class="village-header__inner">
		<a class="village-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $site_name ); ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="" width="52" height="52">
			<span>
				<strong><?php echo esc_html( $site_name ); ?></strong>
				<small>Website Resmi Pemerintahan Desa</small>
			</span>
		</a>

		<div class="village-header__right">
			<input class="village-header__toggle" type="checkbox" id="village-header-menu-toggle" aria-label="Buka menu">
			<label class="village-header__toggle-button" for="village-header-menu-toggle" aria-hidden="true">
				<span></span>
			</label>

			<nav class="village-header__nav" aria-label="<?php echo esc_attr__( 'Main menu', 'hello-elementor' ); ?>">
				<ul>
					<?php foreach ( $menu_items as $item ) : ?>
						<?php
						$link_classes = [];
						$link_url     = isset( $item['url'] ) ? $item['url'] : home_url( $item['path'] );
						if ( ! empty( $item['cta'] ) ) {
							$link_classes[] = 'village-header__nav-cta';
						}
						if ( $item['current'] ) {
							$link_classes[] = 'is-current';
						}
						?>
						<li>
							<a
								class="<?php echo esc_attr( implode( ' ', $link_classes ) ); ?>"
								href="<?php echo esc_url( $link_url ); ?>"
								<?php echo ! empty( $item['target'] ) ? 'target="' . esc_attr( $item['target'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo ! empty( $item['rel'] ) ? 'rel="' . esc_attr( $item['rel'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo ! empty( $item['aria_label'] ) ? 'aria-label="' . esc_attr( $item['aria_label'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo $item['current'] ? 'aria-current="page"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							><?php echo esc_html( $item['label'] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
	</div>
</header>
