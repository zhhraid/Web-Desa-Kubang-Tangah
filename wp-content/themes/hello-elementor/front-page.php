<?php
/**
 * Custom front page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$stats_data_path = get_template_directory() . '/assets/data/village-infographics.json';
$stats_data      = file_exists( $stats_data_path ) ? json_decode( (string) file_get_contents( $stats_data_path ), true ) : [];
$stats_meta      = is_array( $stats_data ) ? (array) ( $stats_data['meta'] ?? [] ) : [];
$home_population = (int) ( $stats_meta['population'] ?? 1477 );
$home_families   = (int) ( $stats_meta['families'] ?? 490 );
$home_dusun      = count( (array) ( $stats_meta['dusun'] ?? [] ) ) ?: 5;

$asset_base = content_url( 'uploads/' );
$hero_image = $asset_base . '2026/01/pemandangan-scaled.jpg';
$team_image = $asset_base . '2026/01/fotobersama-e1768798550506.jpg';
$fallback_news_image = $asset_base . '2026/01/senam-UNAND-3-scaled.jpg';
$gallery_items = [
	[
		'image' => $asset_base . '2026/01/IMG_20240304_110323.jpg',
		'year'  => '2026',
		'alt'   => 'Dokumentasi musyawarah Desa Kubang Tangah',
	],
	[
		'image' => $asset_base . '2026/01/IMG_20240220_130851.jpg',
		'year'  => '2026',
		'alt'   => 'Dokumentasi rapat aparatur Desa Kubang Tangah',
	],
	[
		'image' => $asset_base . '2026/01/IMG_20240904_180018.jpg',
		'year'  => '2026',
		'alt'   => 'Dokumentasi kegiatan masyarakat Desa Kubang Tangah',
	],
	[
		'image' => $asset_base . '2026/01/senam-UNAND-3-1024x461.jpg',
		'year'  => '2026',
		'alt'   => 'Dokumentasi senam bersama Desa Kubang Tangah',
	],
	[
		'image' => $asset_base . '2026/01/dana-BUMN-1.jpg',
		'year'  => '2026',
		'alt'   => 'Dokumentasi kegiatan penyaluran modal desa',
	],
	[
		'image' => $asset_base . '2025/10/Desain-tanpa-judul-7.png',
		'year'  => '2025',
		'alt'   => 'Dokumentasi perangkat Desa Kubang Tangah',
	],
];

$image_overrides = [
	1884 => $asset_base . '2026/01/senam-UNAND-3-1024x461.jpg',
	1944 => $asset_base . '2026/01/senam-UNAND-1.jpg',
	1920 => $asset_base . '2026/01/dana-BUMN-1.jpg',
];
$latest_posts = get_posts(
	[
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'numberposts'         => 3,
		'ignore_sticky_posts' => true,
	]
);
?>

<main id="content" class="site-main village-home">
	<section class="village-home__hero" style="--hero-image: url('<?php echo esc_url( $hero_image ); ?>');">
		<div class="village-home__hero-inner">
			<div class="village-home__hero-copy">
				<p class="village-home__eyebrow">Selamat Datang di</p>
				<h1><span>Desa</span><span>Kubang Tangah</span></h1>
				<p>
					Kubang Tangah adalah kelurahan di Kecamatan Lembah Segar, Kota Sawahlunto, Sumatera Barat, Indonesia.
					Website ini menjadi ruang informasi publik, layanan, dan kabar kegiatan masyarakat.
				</p>
				<div class="village-home__hero-actions">
					<a href="<?php echo esc_url( hello_elementor_village_page_url( 'profil-desa' ) ); ?>">Profil Desa</a>
				</div>
			</div>

			<aside class="village-home__stats" aria-label="Statistik Desa Kubang Tangah">
				<div class="village-home__stats-head">
					<p>Statistik Desa</p>
					<span>Data ringkas wilayah Kubang Tangah</span>
				</div>
				<div class="village-home__stats-grid">
					<div class="village-home__stat-card">
						<span class="village-home__stat-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3Zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5Z"/></svg>
						</span>
						<strong><span class="village-home__stat-number" data-count="<?php echo esc_attr( $home_population ); ?>">0</span></strong>
						<span>Jumlah Penduduk</span>
					</div>
					<div class="village-home__stat-card">
						<span class="village-home__stat-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8h5Z"/></svg>
						</span>
						<strong><span class="village-home__stat-number" data-count="<?php echo esc_attr( $home_families ); ?>">0</span></strong>
						<span>Jumlah Keluarga</span>
					</div>
					<div class="village-home__stat-card">
						<span class="village-home__stat-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M4 11V5l5-2 6 2 5-2v14l-5 2-6-2-5 2V11Zm2-4.65v9.7l2-.8V5.55l-2 .8Zm4-.68v9.7l4 1.34v-9.7l-4-1.34Zm6 1.28v9.7l2-.8v-9.7l-2 .8Z"/></svg>
						</span>
						<strong><span class="village-home__stat-number" data-count="<?php echo esc_attr( $home_dusun ); ?>">0</span></strong>
						<span>Banyak Dusun</span>
					</div>
					<div class="village-home__stat-card">
						<span class="village-home__stat-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V5l-8-3Zm0 2.15 6 2.25V11c0 4.33-2.69 8.22-6 9.86C8.69 19.22 6 15.33 6 11V6.4l6-2.25Zm0 3.85a3 3 0 0 0-3 3c0 2.25 3 5.5 3 5.5s3-3.25 3-5.5a3 3 0 0 0-3-3Zm0 4.2A1.2 1.2 0 1 1 12 9.8a1.2 1.2 0 0 1 0 2.4Z"/></svg>
						</span>
						<strong><span class="village-home__stat-number" data-count="20.15" data-decimals="2">0</span> km<sup>2</sup></strong>
						<span>Luas Wilayah</span>
					</div>
				</div>
				<a class="village-home__stats-link" href="<?php echo esc_url( hello_elementor_village_page_url( 'statistik-desa' ) ); ?>">Lihat Selengkapnya</a>
			</aside>
		</div>
	</section>

	<section class="village-home__about section-pad">
		<div class="village-home__about-visual">
			<div class="village-home__about-head">
				<p class="village-home__section-label">Sekilas Tentang</p>
				<h2>Desa Kubang Tangah</h2>
			</div>
			<div class="village-home__media-card">
				<img src="<?php echo esc_url( $team_image ); ?>" alt="Perangkat Desa Kubang Tangah">
			</div>
		</div>
		<div class="village-home__section-copy">
			<p>
				Desa Kubang Tangah berada di Kecamatan Lembah Segar, Kota Sawahlunto. Wilayah ini terdiri dari 5 dusun
				dengan kehidupan masyarakat yang dekat dengan pelayanan pemerintahan, gotong royong, dan penguatan potensi lokal.
			</p>
			<div class="village-home__pill-grid">
				<span>Pelayanan Publik</span>
				<span>Gotong Royong</span>
				<span>Potensi Lokal</span>
				<span>Informasi Terbuka</span>
			</div>
		</div>
	</section>

	<section class="village-home__news section-pad">
		<div class="village-home__section-head">
			<div>
				<p class="village-home__section-label">Berita</p>
				<h2>Berita Desa Terbaru</h2>
			</div>
			<a href="<?php echo esc_url( home_url( '/berita/' ) ); ?>">Lihat Semua Berita</a>
		</div>
		<div class="village-home__news-grid">
			<?php foreach ( $latest_posts as $post ) : ?>
				<?php
				setup_postdata( $post );
				$post_id    = get_the_ID();
				$post_image = isset( $image_overrides[ $post_id ] ) ? $image_overrides[ $post_id ] : hello_elementor_village_news_image( $post_id, $fallback_news_image );
				?>
				<article>
					<a class="village-home__news-media" href="<?php the_permalink(); ?>">
						<img src="<?php echo esc_url( $post_image ); ?>" alt="<?php echo esc_attr( hello_elementor_village_news_clean_text( get_the_title() ) ); ?>">
					</a>
					<div>
						<span>Berita</span>
						<h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( hello_elementor_village_news_clean_text( get_the_title() ) ); ?></a></h3>
						<p><?php echo esc_html( hello_elementor_village_news_excerpt( $post_id ) ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>

	<section class="village-home__gallery section-pad">
		<div class="village-home__section-head">
			<div>
				<p class="village-home__section-label">Dokumentasi</p>
				<h2>Galeri Kegiatan Desa</h2>
			</div>
			<a href="<?php echo esc_url( home_url( '/galeri/' ) ); ?>">Lihat Galeri</a>
		</div>
		<div class="village-home__gallery-shell" data-home-gallery>
			<button class="village-home__gallery-button" type="button" data-gallery-prev aria-label="Geser galeri ke kiri">&larr;</button>
			<div class="village-home__gallery-row" data-gallery-track>
				<?php foreach ( array_merge( $gallery_items, $gallery_items ) as $index => $item ) : ?>
					<a class="village-home__gallery-photo" href="<?php echo esc_url( home_url( '/galeri/' ) ); ?>" <?php echo $index >= count( $gallery_items ) ? 'aria-hidden="true" tabindex="-1"' : ''; ?>>
						<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['alt'] ); ?>">
						<span class="village-home__gallery-shade" aria-hidden="true"></span>
						<span class="village-home__gallery-year"><?php echo esc_html( $item['year'] ); ?></span>
						<span class="village-home__gallery-action">Lihat foto <span aria-hidden="true">&rarr;</span></span>
					</a>
				<?php endforeach; ?>
			</div>
			<button class="village-home__gallery-button" type="button" data-gallery-next aria-label="Geser galeri ke kanan">&rarr;</button>
		</div>
	</section>

	<section class="village-home__notice section-pad">
		<div>
			<p class="village-home__section-label">Pengumuman</p>
			<h2>Informasi Pelayanan dan Kegiatan</h2>
			<p>
				Pantau halaman informasi untuk pembaruan layanan administrasi, kegiatan dusun, serta pengumuman resmi dari Pemerintah Desa Kubang Tangah.
			</p>
		</div>
		<a href="<?php echo esc_url( hello_elementor_village_page_url( 'informasi-desa' ) ); ?>">Buka Informasi Desa</a>
	</section>
</main>

<?php
get_footer();
