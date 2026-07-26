<?php
/**
 * Single village news article.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$upload_dir      = wp_get_upload_dir();
	$uploads_url     = trailingslashit( $upload_dir['baseurl'] );
	$image_overrides = [
		1884 => $uploads_url . '2026/01/senam-UNAND-3-1024x461.jpg',
		1944 => $uploads_url . '2026/01/senam-UNAND-1.jpg',
		1920 => $uploads_url . '2026/01/dana-BUMN-1.jpg',
	];
	$fallback_image  = $uploads_url . '2026/01/senam-UNAND-3-scaled.jpg';
	$article_image   = isset( $image_overrides[ get_the_ID() ] ) ? $image_overrides[ get_the_ID() ] : hello_elementor_village_news_image( get_the_ID(), $fallback_image );
	$article_title   = hello_elementor_village_news_clean_text( get_the_title() );
	$article_topic   = hello_elementor_village_news_topic( get_the_ID() );
	$article_url     = get_permalink();
	$whatsapp_url    = function_exists( 'hello_elementor_village_whatsapp_url' ) ? hello_elementor_village_whatsapp_url() : 'https://api.whatsapp.com/send?phone=6285271664112';
	$article_content = get_the_content();
	$article_content = str_replace(
		[
			'https://desakubangtangah.id/wp-content/uploads/',
			'http://desakubangtangah.id/wp-content/uploads/',
		],
		$uploads_url,
		$article_content
	);
	$article_content = hello_elementor_village_news_clean_encoding( $article_content );
	$related_query   = new WP_Query(
		[
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'post__not_in'        => [ get_the_ID() ],
			'ignore_sticky_posts' => true,
		]
	);
	?>
	<main id="content" class="village-news village-news-article">
		<section class="village-news-article__hero" aria-labelledby="article-title">
			<img src="<?php echo esc_url( $article_image ); ?>" alt="<?php echo esc_attr( $article_title ); ?>" fetchpriority="high">
			<div class="village-news-article__hero-shade" aria-hidden="true"></div>
			<div class="village-news__inner village-news-article__hero-content">
				<a href="<?php echo esc_url( home_url( '/berita/' ) ); ?>">&larr; Kembali ke Berita</a>
				<p><span><?php echo esc_html( $article_topic ); ?></span><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( wp_date( 'd F Y', get_post_timestamp() ) ); ?></time></p>
				<h1 id="article-title"><?php echo esc_html( $article_title ); ?></h1>
			</div>
		</section>

		<section class="village-news-article__layout">
			<div class="village-news__inner">
				<article class="village-news-article__main">
					<div class="village-news-article__lead">
						<p><?php echo esc_html( hello_elementor_village_news_excerpt( get_the_ID() ) ); ?></p>
					</div>
					<div class="village-news-article__body">
						<?php echo wp_kses_post( $article_content ); ?>
					</div>
					<footer class="village-news-article__share">
						<div>
							<span>Bagikan artikel</span>
							<strong>Bantu informasi desa menjangkau lebih banyak warga.</strong>
						</div>
						<nav aria-label="Bagikan artikel">
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( rawurlencode( $article_url ) ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
							<a href="https://wa.me/?text=<?php echo esc_attr( rawurlencode( $article_title . ' ' . $article_url ) ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
						</nav>
					</footer>
				</article>

				<aside class="village-news-article__aside">
					<p class="village-news__kicker">Informasi Artikel</p>
					<dl>
						<div><dt>Diterbitkan</dt><dd><?php echo esc_html( wp_date( 'd M Y', get_post_timestamp() ) ); ?></dd></div>
						<div><dt>Topik</dt><dd><?php echo esc_html( $article_topic ); ?></dd></div>
						<div><dt>Sumber</dt><dd>Pemerintah Desa Kubang Tangah</dd></div>
					</dl>
					<a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">Chat Admin via WhatsApp</a>
				</aside>
			</div>
		</section>

		<?php if ( $related_query->have_posts() ) : ?>
			<section class="village-news-article__related">
				<div class="village-news__inner">
					<header class="village-news__section-heading">
						<div>
							<p class="village-news__kicker">Baca Berikutnya</p>
							<h2>Berita Lainnya</h2>
						</div>
						<a href="<?php echo esc_url( home_url( '/berita/' ) ); ?>">Lihat Semua Berita</a>
					</header>
					<div class="village-news__grid">
						<?php while ( $related_query->have_posts() ) : ?>
							<?php
							$related_query->the_post();
							$related_title = hello_elementor_village_news_clean_text( get_the_title() );
							$related_topic = hello_elementor_village_news_topic( get_the_ID() );
							$related_image = isset( $image_overrides[ get_the_ID() ] ) ? $image_overrides[ get_the_ID() ] : hello_elementor_village_news_image( get_the_ID(), $fallback_image );
							?>
							<article class="village-news__card">
								<a class="village-news__card-media" href="<?php the_permalink(); ?>">
									<img src="<?php echo esc_url( $related_image ); ?>" alt="<?php echo esc_attr( $related_title ); ?>" loading="lazy">
									<span><?php echo esc_html( $related_topic ); ?></span>
									<time><?php echo esc_html( wp_date( 'd M Y', get_post_timestamp() ) ); ?></time>
								</a>
								<div>
									<h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( $related_title ); ?></a></h3>
									<p><?php echo esc_html( hello_elementor_village_news_excerpt( get_the_ID() ) ); ?></p>
									<a class="village-news__card-link" href="<?php the_permalink(); ?>">Baca selengkapnya <span aria-hidden="true">&rarr;</span></a>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</main>
	<?php
	wp_reset_postdata();
endwhile;

get_footer();
