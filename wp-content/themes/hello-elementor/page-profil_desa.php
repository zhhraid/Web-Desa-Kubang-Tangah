<?php
/**
 * Profile page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$upload_dir        = wp_get_upload_dir();
$uploads_url       = trailingslashit( $upload_dir['baseurl'] ) . '2026/01/';
$theme_assets_url  = trailingslashit( get_template_directory_uri() ) . 'assets/';
$theme_assets_path = trailingslashit( get_template_directory() ) . 'assets/';
$boundary_path     = $theme_assets_path . 'data/kubang-tangah-boundary.geojson';
$boundary_url      = $theme_assets_url . 'data/kubang-tangah-boundary.geojson?ver=' . filemtime( $boundary_path );
$map_data_path     = $theme_assets_path . 'data/kubang-tangah-map-data.json';
$map_data_url      = $theme_assets_url . 'data/kubang-tangah-map-data.json?ver=' . filemtime( $map_data_path );
$hero_url          = $uploads_url . 'pemandangan-scaled.jpg';
$logo_url          = $uploads_url . 'logokt.png';

$history = [
	[
		'period' => '1956-1990',
		'title'  => 'Perubahan wilayah administrasi',
		'copy'   => 'Berdasarkan Undang-Undang Nomor 8 Tahun 1956 dan Peraturan Pemerintah Nomor 44 Tahun 1990, Desa Kubang Tangah yang semula berada di Kecamatan Sawahlunto, Kabupaten Sawahlunto/Sijunjung masuk ke wilayah Kotamadya Sawahlunto.',
	],
	[
		'period' => 'Sebelum 1993',
		'title'  => 'Dua desa bertetangga',
		'copy'   => 'Wilayah ini terdiri dari Desa Kubang Tangah dengan Batu Tajam, Sawah Rawang, Guguak Pauh, dan Sionsek; serta Desa Kubang Barat dengan Polak Datar, Luak Mani, dan Batu Lombu.',
	],
	[
		'period' => '20 September 1993',
		'title'  => 'Penggabungan desa',
		'copy'   => 'Desa Kubang Tangah dan Desa Kubang Barat bergabung menjadi Desa Kubang Tangah. Bapak Andi Rawan menjadi Kepala Desa pertama setelah penggabungan.',
	],
	[
		'period' => '2 November 2007',
		'title'  => 'Pemekaran dusun',
		'copy'   => 'Pada masa kepemimpinan Kepala Desa Andami, Dusun Guguak Pauh Sionsek dimekarkan menjadi Dusun Guguak Pauh dan Dusun Sionsek sesuai kebutuhan masyarakat.',
	],
	[
		'period' => 'Sekarang',
		'title'  => 'Desa dengan lima dusun',
		'copy'   => 'Kubang Tangah kini terdiri dari Batu Tajam, Luak Mani, Polak Datar, Sionsek, dan Guguak Pauh dalam wilayah Kecamatan Lembah Segar.',
	],
];

$missions = [
	'Meningkatkan pelayanan kepada masyarakat secara cepat, tepat, dan benar.',
	'Mengutamakan musyawarah dan mufakat dalam mengambil keputusan.',
	'Mewujudkan perekonomian dan kesejahteraan masyarakat desa dengan meningkatkan sumber daya manusia serta memanfaatkan sumber daya alam.',
	'Mewujudkan sistem usaha mandiri melalui pengembangan Badan Usaha Milik Desa serta meningkatkan kehidupan desa secara dinamis dalam bidang kebudayaan dan keagamaan.',
];

$dusun = [
	'Batu Tajam',
	'Luak Mani',
	'Polak Datar',
	'Sionsek',
	'Guguak Pauh',
];

$dusun_stats = [
	'Batu Tajam'  => [ 'male' => 261, 'female' => 242, 'total' => 503 ],
	'Luak Mani'   => [ 'male' => 150, 'female' => 143, 'total' => 293 ],
	'Polak Datar' => [ 'male' => 209, 'female' => 204, 'total' => 413 ],
	'Sionsek'     => [ 'male' => 57, 'female' => 67, 'total' => 124 ],
	'Guguak Pauh' => [ 'male' => 77, 'female' => 67, 'total' => 144 ],
];
?>

<main id="content" class="village-profile">
	<section class="village-profile__hero" aria-labelledby="profile-title">
		<img src="<?php echo esc_url( $hero_url ); ?>" alt="Pemandangan kawasan Kota Sawahlunto yang dikelilingi perbukitan" fetchpriority="high">
		<div class="village-profile__hero-shade" aria-hidden="true"></div>
		<div class="village-profile__hero-content">
			<p class="village-profile__eyebrow">Profil Desa</p>
			<h1 id="profile-title"><span>Profil Desa</span> Kubang Tangah</h1>
			<p>Kecamatan Lembah Segar, Kota Sawahlunto, Sumatera Barat</p>
		</div>
	</section>

	<nav class="village-profile__section-nav" aria-label="Bagian profil desa" data-profile-tabs>
		<div>
			<a class="is-active" href="#sejarah" data-profile-tab>Sejarah Desa</a>
			<a href="#logo-desa" data-profile-tab>Logo Desa</a>
			<a href="#visi-misi" data-profile-tab>Visi dan Misi Desa</a>
			<a href="#peta-desa" data-profile-tab>Peta Desa</a>
		</div>
	</nav>

	<section id="sejarah" class="village-profile__section village-profile__history" data-profile-section>
		<div class="village-profile__inner village-profile__history-layout">
			<header class="village-profile__section-intro">
				<p class="village-profile__kicker">Sejarah Desa</p>
				<h2>Perjalanan Desa Kubang Tangah</h2>
				<p>Perkembangan wilayah Kubang Tangah berlangsung melalui perubahan administrasi, penggabungan desa, dan pemekaran dusun hingga membentuk wilayah yang dikenal sekarang.</p>
				<div class="village-profile__history-summary" aria-label="Ringkasan sejarah">
					<strong>1993</strong>
					<span>Tahun penggabungan desa</span>
				</div>
			</header>

			<ol class="village-profile__timeline">
				<?php foreach ( $history as $event ) : ?>
					<li>
						<time><?php echo esc_html( $event['period'] ); ?></time>
						<div>
							<h3><?php echo esc_html( $event['title'] ); ?></h3>
							<p><?php echo esc_html( $event['copy'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<section id="logo-desa" class="village-profile__section village-profile__identity" data-profile-section hidden>
		<div class="village-profile__inner">
			<header class="village-profile__section-heading">
				<p class="village-profile__kicker">Logo Desa</p>
				<h2>Identitas Resmi Desa Kubang Tangah</h2>
				<p>Logo desa menjadi simbol identitas, kebanggaan, kebersamaan, serta semangat kemajuan masyarakat Kubang Tangah.</p>
			</header>

			<div class="village-profile__identity-layout">
				<article class="village-profile__logo-story">
					<figure>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo resmi Desa Kubang Tangah" loading="lazy">
						<figcaption>Logo resmi Desa Kubang Tangah</figcaption>
					</figure>
					<div>
						<p class="village-profile__label">Ditetapkan 16 Januari 2025</p>
						<h3>Simbol identitas dan kebersamaan</h3>
						<p>Logo desa ditetapkan melalui Peraturan Desa Nomor 1 Tahun 2025. Gagasan ini diinisiasi oleh Nining Resque, A.Md.Keb., selaku Kasi Kesejahteraan, melalui perlombaan inovasi logo yang kemudian disosialisasikan dan diresmikan pada masa kepemimpinan Kepala Desa Rice.</p>
						<p>Logo menjadi simbol identitas, kebanggaan, kebersamaan, serta semangat kemajuan dan kesejahteraan masyarakat Kubang Tangah.</p>
					</div>
				</article>
			</div>
		</div>
	</section>

	<section id="visi-misi" class="village-profile__section village-profile__identity village-profile__vision-section" data-profile-section hidden>
		<div class="village-profile__inner">
			<header class="village-profile__section-heading">
				<p class="village-profile__kicker">Visi dan Misi Desa</p>
				<h2>Arah Pelayanan Pemerintah Desa</h2>
				<p>Visi dan misi menjadi pedoman Pemerintah Desa Kubang Tangah dalam pelayanan, pembangunan, pembinaan masyarakat, dan pemberdayaan desa.</p>
			</header>

			<div class="village-profile__direction village-profile__direction--standalone">
				<article class="village-profile__vision-card">
					<div class="village-profile__vision-orbit" aria-hidden="true">
						<span></span>
						<span></span>
						<span></span>
					</div>
					<div class="village-profile__vision-copy">
						<span>Visi Desa</span>
						<p>Hadir Lebih Dekat dan Transparan untuk Masyarakat yang Peduli, Adil, Makmur dan Sejahtera.</p>
					</div>
					<ul class="village-profile__vision-values" aria-label="Nilai utama visi desa">
						<li>Dekat</li>
						<li>Transparan</li>
						<li>Peduli</li>
						<li>Sejahtera</li>
					</ul>
				</article>

				<section aria-labelledby="mission-title">
					<p class="village-profile__label">Misi Desa</p>
					<h3 id="mission-title">Langkah Kerja Pemerintah Desa</h3>
					<ol class="village-profile__missions">
						<?php foreach ( $missions as $mission ) : ?>
							<li><?php echo esc_html( $mission ); ?></li>
						<?php endforeach; ?>
					</ol>
				</section>
			</div>
		</div>
	</section>

	<section id="peta-desa" class="village-profile__section village-profile__geography" data-profile-section hidden>
		<div class="village-profile__inner">
			<header class="village-profile__section-heading">
				<p class="village-profile__kicker">Peta Desa</p>
				<h2>Letak dan Wilayah Administrasi Desa</h2>
				<p>Desa Kubang Tangah berada di Kecamatan Lembah Segar, Kota Sawahlunto, Provinsi Sumatera Barat. Peta berikut menampilkan batas desa, batas dusun, dan titik sarana prasarana berdasarkan peta administrasi desa.</p>
			</header>

			<div class="village-profile__map-layout">
				<div class="village-profile__map-tool">
					<header class="village-profile__map-toolbar">
						<div>
							<p class="village-profile__label">Peta Desa Kubang Tangah</p>
							<h3>Jelajahi Wilayah Desa</h3>
						</div>
						<div class="village-profile__map-type" aria-label="Jenis tampilan peta">
							<button class="is-active" type="button" data-map-type="standard" aria-pressed="true">Standar</button>
							<button type="button" data-map-type="satellite" aria-pressed="false">Satelit</button>
							<button type="button" data-map-type="terrain" aria-pressed="false">Medan</button>
						</div>
					</header>

					<div id="interactive-map-panel" class="village-profile__map-panel is-active" data-map-panel="interactive">
						<div
							id="village-boundary-map"
							class="village-profile__map"
							data-geojson-url="<?php echo esc_url( $boundary_url ); ?>"
							data-map-data-url="<?php echo esc_url( $map_data_url ); ?>"
							aria-label="Peta interaktif batas Desa Kubang Tangah"
						></div>
						<div class="village-profile__map-overlay">
							<div class="village-profile__legend" aria-label="Legenda peta">
								<span><i class="village-profile__legend-area"></i>Batas desa</span>
								<span><i class="village-profile__legend-line"></i>Batas dusun</span>
								<span><i class="village-profile__legend-marker"></i>Sarana prasarana</span>
							</div>
							<button type="button" data-map-focus>Fokus Batas</button>
						</div>
						<p class="village-profile__map-status" data-map-status aria-live="polite">Menyiapkan peta batas desa...</p>
					</div>
				</div>

				<aside class="village-profile__dusun-list village-profile__map-dusun">
					<p class="village-profile__label">Lima Dusun Desa Kubang Tangah</p>
					<h3>Wilayah Administrasi Desa</h3>
					<ol>
						<?php foreach ( $dusun as $index => $dusun_name ) : ?>
							<?php $stats = $dusun_stats[ $dusun_name ]; ?>
							<li>
								<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<strong><?php echo esc_html( $dusun_name ); ?></strong>
								<small><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?> jiwa, <?php echo esc_html( number_format_i18n( $stats['male'] ) ); ?> pria, <?php echo esc_html( number_format_i18n( $stats['female'] ) ); ?> wanita</small>
							</li>
						<?php endforeach; ?>
					</ol>
				</aside>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
