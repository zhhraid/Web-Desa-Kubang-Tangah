<?php
/**
 * Data and infographics page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$data_path = get_template_directory() . '/assets/data/village-infographics.json';
$data_url  = get_template_directory_uri() . '/assets/data/village-infographics.json?ver=' . filemtime( $data_path );
$infographic_data = json_decode( (string) file_get_contents( $data_path ), true );
$infographic_data = is_array( $infographic_data ) ? $infographic_data : [];
$population       = (int) ( $infographic_data['meta']['population'] ?? 1477 );
$families         = (int) ( $infographic_data['meta']['families'] ?? 490 );
$dusun_names      = (array) ( $infographic_data['meta']['dusun'] ?? [] );
$age_categories   = (array) ( $infographic_data['usia']['categories'] ?? [] );
$age_totals       = (array) ( $infographic_data['usia']['total'] ?? [] );
$dusun_totals     = [];
$largest_dusun    = '';
$largest_total    = 0;

foreach ( $dusun_names as $dusun_name ) {
	$dusun_total = array_sum( (array) ( $infographic_data['usia']['byDusun'][ $dusun_name ] ?? [] ) );
	$dusun_totals[ $dusun_name ] = $dusun_total;
	if ( $dusun_total > $largest_total ) {
		$largest_dusun = $dusun_name;
		$largest_total = $dusun_total;
	}
}

$largest_dusun_value = max( 1, $largest_total );
$dominant_age_total  = $age_totals ? max( $age_totals ) : 0;
$dominant_age_index  = array_search( $dominant_age_total, $age_totals, true );
$dominant_age        = false !== $dominant_age_index ? ( $age_categories[ $dominant_age_index ] ?? '' ) : '';
$average_family_size = $families > 0 ? $population / $families : 0;
$largest_dusun_share = $population > 0 ? ( $largest_total / $population ) * 100 : 0;
$format_number       = static function ( $value, $decimals = 0 ) {
	return number_format( (float) $value, $decimals, ',', '.' );
};
$sections  = [
	'usia' => [
		'number'      => '01',
		'nav_label'   => 'Usia',
		'label'       => 'Komposisi Penduduk',
		'title'       => 'Usia Per Dusun',
		'description' => 'Perbandingan kelompok usia untuk melihat komposisi generasi pada setiap dusun.',
		'unit'        => 'penduduk',
		'type'        => 'stacked',
	],
	'pendidikan' => [
		'number'      => '02',
		'nav_label'   => 'Pendidikan',
		'label'       => 'Jenjang Pendidikan',
		'title'       => 'Pendidikan Per Dusun',
		'description' => 'Sebaran pendidikan terakhir penduduk sebagai gambaran kapasitas sumber daya manusia.',
		'unit'        => 'penduduk',
		'type'        => 'bars',
	],
	'pekerjaan' => [
		'number'      => '03',
		'nav_label'   => 'Pekerjaan',
		'label'       => 'Aktivitas Utama',
		'title'       => 'Pekerjaan Per Dusun',
		'description' => 'Komposisi aktivitas dan pekerjaan utama masyarakat di wilayah Kubang Tangah.',
		'unit'        => 'penduduk',
		'type'        => 'lollipop',
	],
	'pernikahan' => [
		'number'      => '04',
		'nav_label'   => 'Status Pernikahan',
		'label'       => 'Kondisi Kependudukan',
		'title'       => 'Status Pernikahan',
		'description' => 'Proporsi status perkawinan penduduk dalam bentuk yang ringkas dan mudah dibandingkan.',
		'unit'        => 'penduduk',
		'type'        => 'donut',
	],
	'desil' => [
		'number'      => '05',
		'nav_label'   => 'Desil',
		'label'       => 'Kesejahteraan Keluarga',
		'title'       => 'Distribusi Desil',
		'description' => 'Sebaran keluarga berdasarkan kelompok desil untuk mendukung perencanaan program yang tepat.',
		'unit'        => 'keluarga',
		'type'        => 'columns',
	],
	'bantuan' => [
		'number'      => '06',
		'nav_label'   => 'Bantuan',
		'label'       => 'Perlindungan Sosial',
		'title'       => 'Pengelompokan Bantuan',
		'description' => 'Cakupan penerimaan bantuan keluarga menurut jenis program yang tercatat dalam data desa.',
		'unit'        => 'keluarga',
		'type'        => 'proportion',
	],
];
?>

<main id="content" class="site-main village-home village-infographics-page" data-infographics-page data-source="<?php echo esc_url( $data_url ); ?>">
	<header id="ringkasan" class="village-infographics-page__intro" data-nav-section="all">
		<div class="village-infographics-page__lead">
			<p class="village-home__section-label">Desa Kubang Tangah</p>
			<h1>Data dan Infografis</h1>
			<p>Informasi kependudukan dan sosial dalam visualisasi interaktif untuk membantu masyarakat memahami kondisi desa secara lebih mudah.</p>
			<span class="village-infographics-page__updated">Pembaruan data 8 Juli 2026</span>
			<div class="village-infographics-page__key-insights" aria-label="Sorotan data desa">
				<div>
					<small>Dusun terpadat</small>
					<strong><?php echo esc_html( $largest_dusun ); ?></strong>
					<span><?php echo esc_html( $format_number( $largest_total ) ); ?> jiwa</span>
				</div>
				<div>
					<small>Usia dominan</small>
					<strong><?php echo esc_html( $dominant_age ); ?></strong>
					<span><?php echo esc_html( $format_number( $dominant_age_total ) ); ?> jiwa</span>
				</div>
				<div>
					<small>Rata-rata keluarga</small>
					<strong><?php echo esc_html( $format_number( $average_family_size, 2 ) ); ?></strong>
					<span>jiwa per keluarga</span>
				</div>
			</div>
		</div>
		<div class="village-home__data-summary" aria-label="Ringkasan data desa">
			<div><small>Data Penduduk</small><strong data-summary-number="<?php echo esc_attr( $population ); ?>"><?php echo esc_html( $format_number( $population ) ); ?></strong><span>Jiwa</span></div>
			<div><small>Data Keluarga</small><strong data-summary-number="<?php echo esc_attr( $families ); ?>"><?php echo esc_html( $format_number( $families ) ); ?></strong><span>Keluarga</span></div>
			<div><small>Wilayah</small><strong data-summary-number="<?php echo esc_attr( count( $dusun_names ) ); ?>"><?php echo esc_html( $format_number( count( $dusun_names ) ) ); ?></strong><span>Dusun</span></div>
			<div><small>Luas Wilayah</small><strong data-summary-number="20.15" data-summary-decimals="2">20,15</strong><span>km<sup>2</sup></span></div>
		</div>

		<section class="village-infographics-page__overview" data-overview aria-labelledby="overview-title">
			<header>
				<p>Distribusi Wilayah</p>
				<h2 id="overview-title">Sebaran Penduduk per Dusun</h2>
				<p><strong><?php echo esc_html( $largest_dusun ); ?></strong> mencatat jumlah penduduk terbesar, yaitu <?php echo esc_html( $format_number( $largest_dusun_share, 1 ) ); ?>% dari total penduduk desa.</p>
				<span><strong><?php echo esc_html( $format_number( $population ) ); ?></strong> jiwa tercatat</span>
			</header>
			<div class="village-infographics-page__overview-plot" aria-label="Perbandingan jumlah penduduk pada lima dusun">
				<?php foreach ( $dusun_totals as $dusun_name => $dusun_total ) : ?>
					<?php $bar_size = ( $dusun_total / $largest_dusun_value ) * 100; ?>
					<div class="village-infographics-page__overview-item" tabindex="0" aria-label="<?php echo esc_attr( $dusun_name . ', ' . $format_number( $dusun_total ) . ' jiwa' ); ?>">
						<strong><?php echo esc_html( $format_number( $dusun_total ) ); ?></strong>
						<div class="village-infographics-page__overview-track" aria-hidden="true"><span style="--bar-size: <?php echo esc_attr( round( $bar_size, 2 ) ); ?>%;"></span></div>
						<span><?php echo esc_html( $dusun_name ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</header>

	<div class="village-infographics-page__controls">
		<nav class="village-infographics-page__jumps" aria-label="Pilih bagian data">
			<a href="#ringkasan" class="is-active" data-jump="all">Semua</a>
			<?php foreach ( $sections as $key => $section ) : ?>
				<a href="#<?php echo esc_attr( $key ); ?>" data-jump="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $section['nav_label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="village-infographics-page__actions">
			<span class="village-infographics-page__period">Data 2026</span>
			<label class="village-home__dusun-filter">
				<span>Wilayah</span>
				<select data-region aria-label="Pilih dusun untuk semua infografis">
					<option value="all">Semua Dusun</option>
				</select>
			</label>
		</div>
	</div>

	<div class="village-infographics-page__sections">
		<?php foreach ( $sections as $key => $section ) : ?>
			<section id="<?php echo esc_attr( $key ); ?>" class="village-infographics-page__section" data-section="<?php echo esc_attr( $key ); ?>" data-chart-type="<?php echo esc_attr( $section['type'] ); ?>" data-unit="<?php echo esc_attr( $section['unit'] ); ?>">
				<header class="village-infographics-page__section-head">
					<span><?php echo esc_html( $section['number'] ); ?></span>
					<div>
						<p><?php echo esc_html( $section['label'] ); ?></p>
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<p><?php echo esc_html( $section['description'] ); ?></p>
					</div>
					<strong data-total>Memuat data...</strong>
				</header>

				<div class="village-infographics-page__visual">
					<div class="village-infographics-page__chart-wrap">
						<div class="village-home__chart village-home__chart--<?php echo esc_attr( $section['type'] ); ?>" data-chart aria-live="polite"></div>
						<div class="village-home__chart-legend" data-legend></div>
					</div>
					<aside class="village-home__data-insight" aria-live="polite">
						<span>Sorotan Data</span>
						<strong data-insight-value>-</strong>
						<h3 data-insight-title>Menyiapkan ringkasan</h3>
						<p data-insight-copy>Data sedang dimuat.</p>
					</aside>
				</div>
			</section>
		<?php endforeach; ?>
	</div>

	<p class="village-infographics-page__source">Sumber: DESA CANTIK, pembaruan 8 Juli 2026. Data ditampilkan dalam bentuk agregat; filter dusun menggunakan data yang memiliki pasangan wilayah.</p>
</main>

<?php
get_footer();
