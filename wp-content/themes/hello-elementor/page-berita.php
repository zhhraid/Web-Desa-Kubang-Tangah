<?php
/**
 * News and announcements page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$upload_dir    = wp_get_upload_dir();
$uploads_url   = trailingslashit( $upload_dir['baseurl'] );
$hero_image    = $uploads_url . '2026/01/senam-UNAND-3-scaled.jpg';
$facebook_url  = 'https://www.facebook.com/pemdes.kubangtangah';

$image_overrides = [
	1884 => $uploads_url . '2026/01/senam-UNAND-3-1024x461.jpg',
	1944 => $uploads_url . '2026/01/senam-UNAND-1.jpg',
	1920 => $uploads_url . '2026/01/dana-BUMN-1.jpg',
];

$facebook_updates = [
	[
		'tag'   => 'Kegiatan Desa',
		'date'  => '13 Juli 2026',
		'title' => 'Rembug Stunting Desa Kubang Tangah',
		'copy'  => 'Kegiatan rembug stunting bersama perangkat desa, kader, dan masyarakat dalam upaya percepatan penurunan stunting di desa.',
		'image' => $uploads_url . '2026/01/IMG_20240304_110323.jpg',
	],
	[
		'tag'   => 'Kegiatan',
		'date'  => '10 Juli 2026',
		'title' => 'Sosialisasi Layanan Administrasi Desa',
		'copy'  => 'Sosialisasi pelayanan administrasi desa untuk meningkatkan pemahaman masyarakat tentang prosedur dan persyaratan layanan.',
		'image' => $uploads_url . '2026/01/IMG_20240220_130851.jpg',
	],
	[
		'tag'   => 'Pengumuman',
		'date'  => '08 Juli 2026',
		'title' => 'Informasi Jam Pelayanan Desa',
		'copy'  => 'Informasi jam pelayanan Kantor Desa Kubang Tangah untuk memberikan pelayanan terbaik bagi masyarakat.',
		'image' => $uploads_url . '2026/01/dana-BUMN-1.jpg',
	],
];

$prepare_news_item = static function ( $post ) use ( $image_overrides, $hero_image ) {
	$topic = hello_elementor_village_news_topic( $post->ID );

	return [
		'id'      => $post->ID,
		'title'   => hello_elementor_village_news_clean_text( $post->post_title ),
		'excerpt' => hello_elementor_village_news_excerpt( $post->ID ),
		'image'   => isset( $image_overrides[ $post->ID ] ) ? $image_overrides[ $post->ID ] : hello_elementor_village_news_image( $post->ID, $hero_image ),
		'url'     => get_permalink( $post->ID ),
		'date'    => wp_date( 'd M Y', strtotime( $post->post_date ) ),
		'topic'   => $topic,
		'filter'  => sanitize_title( $topic ),
	];
};

$featured_posts = get_posts(
	[
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'numberposts'         => 3,
		'ignore_sticky_posts' => true,
	]
);
$featured_items = array_map( $prepare_news_item, $featured_posts );

$paged          = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$search_term    = isset( $_GET['cari'] ) ? sanitize_text_field( wp_unslash( $_GET['cari'] ) ) : '';
$posts_per_page = 9;
$news_query = new WP_Query(
	[
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $posts_per_page,
		'paged'               => $paged,
		's'                   => $search_term,
		'ignore_sticky_posts' => true,
	]
);

$news_items  = array_map( $prepare_news_item, $news_query->posts );
?>

<main id="content" class="village-news">
	<section class="village-news__hero" aria-labelledby="news-title">
		<img src="<?php echo esc_url( $hero_image ); ?>" alt="Pertemuan Pemerintah Desa Kubang Tangah bersama mahasiswa KKN" fetchpriority="high">
		<div class="village-news__hero-shade" aria-hidden="true"></div>
		<div class="village-news__inner village-news__hero-shell">
			<header class="village-news__hero-intro">
				<div>
					<p class="village-news__eyebrow">Kabar Desa Kubang Tangah</p>
					<h1 id="news-title">Berita Desa</h1>
				</div>
			</header>

			<?php if ( $featured_items ) : ?>
				<div class="village-news__featured-grid" aria-label="Sorotan berita terbaru">
				<?php foreach ( $featured_items as $index => $item ) : ?>
					<article class="village-news__featured-card <?php echo 0 === $index ? 'village-news__featured-card--lead' : ''; ?>">
						<a href="<?php echo esc_url( $item['url'] ); ?>">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>">
							<span class="village-news__featured-shade" aria-hidden="true"></span>
							<div>
								<p><span><?php echo 0 === $index ? 'Sorotan Utama' : 'Berita Desa'; ?></span><time><?php echo esc_html( $item['date'] ); ?></time></p>
								<h2><?php echo esc_html( $item['title'] ); ?></h2>
								<?php if ( 0 === $index ) : ?>
									<p><?php echo esc_html( $item['excerpt'] ); ?></p>
								<?php endif; ?>
								<strong>Baca selengkapnya <span aria-hidden="true">&rarr;</span></strong>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section id="berita-terbaru" class="village-news__archive" data-news-section>
		<div class="village-news__inner">
			<header class="village-news__section-heading">
				<div>
					<p class="village-news__kicker">Publikasi Desa</p>
					<h2><?php echo $search_term ? 'Hasil Pencarian Berita' : 'Semua Berita'; ?></h2>
					<?php if ( $search_term ) : ?>
						<p>Menampilkan berita yang sesuai dengan pencarian &ldquo;<?php echo esc_html( $search_term ); ?>&rdquo;.</p>
					<?php else : ?>
						<p>Seluruh berita ditampilkan dari yang terbaru. Gunakan pencarian atau navigasi halaman untuk menemukan publikasi lama.</p>
					<?php endif; ?>
				</div>
			</header>

			<form class="village-news__toolbar" role="search" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
				<label class="village-news__search">
					<span class="screen-reader-text">Cari berita</span>
					<input type="search" name="cari" value="<?php echo esc_attr( $search_term ); ?>" placeholder="Cari judul atau isi berita..." autocomplete="off">
					<span aria-hidden="true"></span>
				</label>
				<button type="submit">Cari Berita</button>
				<?php if ( $search_term ) : ?>
					<a href="<?php echo esc_url( get_permalink() ); ?>">Bersihkan</a>
				<?php endif; ?>
			</form>

			<?php if ( $news_items ) : ?>
			<div class="village-news__grid">
				<?php foreach ( $news_items as $item ) : ?>
					<article class="village-news__card">
						<a class="village-news__card-media" href="<?php echo esc_url( $item['url'] ); ?>">
							<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy">
							<span>Berita</span>
							<time><?php echo esc_html( $item['date'] ); ?></time>
						</a>
						<div>
							<h3><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a></h3>
							<p><?php echo esc_html( $item['excerpt'] ); ?></p>
							<a class="village-news__card-link" href="<?php echo esc_url( $item['url'] ); ?>">Baca selengkapnya <span aria-hidden="true">&rarr;</span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<?php else : ?>
			<div class="village-news__empty">
				<h3>Berita belum ditemukan</h3>
				<p>Coba gunakan kata pencarian yang berbeda atau <a href="<?php echo esc_url( get_permalink() ); ?>">lihat semua berita</a>.</p>
			</div>
			<?php endif; ?>

			<?php if ( $news_query->max_num_pages > 1 ) : ?>
				<nav class="village-news__pagination" aria-label="Halaman berita">
					<?php
					echo wp_kses_post(
						paginate_links(
							[
								'total'     => $news_query->max_num_pages,
								'current'   => $paged,
								'prev_text' => '&larr; Berita lebih baru',
								'next_text' => 'Berita lebih lama &rarr;',
								'type'      => 'list',
								'add_args'  => $search_term ? [ 'cari' => $search_term ] : false,
							]
						)
					);
					?>
				</nav>
			<?php elseif ( $news_query->found_posts > 0 ) : ?>
				<p class="village-news__archive-complete">Semua berita yang tersedia sudah ditampilkan.</p>
			<?php endif; ?>
		</div>
	</section>

	<section id="facebook-desa" class="village-news__facebook" data-news-section>
		<div class="village-news__inner">
			<header class="village-news__facebook-head">
				<div>
					<span aria-hidden="true">f</span>
					<h2>Update Terbaru dari Facebook</h2>
				</div>
				<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">Lihat semua update <span aria-hidden="true">&rsaquo;</span></a>
			</header>

			<div class="village-news__facebook-grid">
				<?php foreach ( $facebook_updates as $update ) : ?>
					<article class="village-news__facebook-card">
						<a class="village-news__facebook-media" href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">
							<img src="<?php echo esc_url( $update['image'] ); ?>" alt="<?php echo esc_attr( $update['title'] ); ?>" loading="lazy">
							<span><?php echo esc_html( $update['tag'] ); ?></span>
						</a>
						<div class="village-news__facebook-card-body">
							<time><span aria-hidden="true">f</span><?php echo esc_html( $update['date'] ); ?></time>
							<h3><?php echo esc_html( $update['title'] ); ?></h3>
							<p><?php echo esc_html( $update['copy'] ); ?></p>
							<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">Lihat di Facebook <span aria-hidden="true">&nearr;</span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</main>

<?php
wp_reset_postdata();
get_footer();
