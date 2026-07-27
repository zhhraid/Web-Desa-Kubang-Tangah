<?php
/**
 * Statistics and infographics page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$data_path         = get_template_directory() . '/assets/data/village-infographics.json';
$data_url          = get_template_directory_uri() . '/assets/data/village-infographics.json?ver=' . filemtime( $data_path );
$infographic_data  = json_decode( (string) file_get_contents( $data_path ), true );
$infographic_data  = is_array( $infographic_data ) ? $infographic_data : [];
$meta              = (array) ( $infographic_data['meta'] ?? [] );
$population        = (int) ( $meta['population'] ?? 1477 );
$families          = (int) ( $meta['families'] ?? 490 );
$area_km2          = (float) ( $meta['areaKm2'] ?? 20.15 );
$dusun_names       = (array) ( $meta['dusun'] ?? [] );
$sources           = (array) ( $meta['sources'] ?? [] );
$age_pyramid       = (array) ( $infographic_data['umurPiramida'] ?? [] );
$productive        = (array) ( $age_pyramid['productive'] ?? [] );
$gender_data       = (array) ( $infographic_data['sebaranPenduduk'] ?? [] );
$job_compact       = (array) ( $infographic_data['pekerjaanRingkas'] ?? [] );
$marriage_data     = (array) ( $infographic_data['pernikahan'] ?? [] );
$product_data      = (array) ( $infographic_data['produkUnggulan'] ?? [] );
$product_summary   = (array) ( $product_data['summary'] ?? [] );
$facilities        = (array) ( $infographic_data['fasilitas'] ?? [] );
$assets            = (array) ( $infographic_data['aset'] ?? [] );
$age_categories    = (array) ( $age_pyramid['categories'] ?? [] );
$age_male          = (array) ( $age_pyramid['male'] ?? [] );
$age_female        = (array) ( $age_pyramid['female'] ?? [] );
$max_age_value     = max( 1, max( array_merge( $age_male, $age_female, [ 1 ] ) ) );
$format_number     = static function ( $value, $decimals = 0 ) {
	return number_format( (float) $value, $decimals, ',', '.' );
};
$format_percent    = static function ( $value ) use ( $format_number ) {
	return $format_number( $value, 2 ) . '%';
};
$format_rupiah     = static function ( $value ) use ( $format_number ) {
	return 'Rp ' . $format_number( (float) $value / 1000000, 2 ) . ' jt';
};
$build_conic       = static function ( $values, $colors ) {
	$total  = array_sum( array_map( 'floatval', $values ) );
	$offset = 0;
	$stops  = [];

	foreach ( $values as $index => $value ) {
		$start   = $offset;
		$offset += $total > 0 ? ( (float) $value / $total ) * 100 : 0;
		$stops[] = ( $colors[ $index % count( $colors ) ] ?? '#247348' ) . ' ' . round( $start, 2 ) . '% ' . round( $offset, 2 ) . '%';
	}

	return 'conic-gradient(' . implode( ',', $stops ) . ')';
};

$dusun_totals  = [];
$largest_dusun = '';
$largest_total = 0;
foreach ( $dusun_names as $dusun_name ) {
	$dusun_total = array_sum( (array) ( $infographic_data['usia']['byDusun'][ $dusun_name ] ?? [] ) );
	$dusun_totals[ $dusun_name ] = $dusun_total;
	if ( $dusun_total > $largest_total ) {
		$largest_dusun = $dusun_name;
		$largest_total = $dusun_total;
	}
}

$sections = [
	'sebaranPenduduk' => [
		'number'      => '01',
		'nav_label'   => 'Sebaran',
		'label'       => 'Demografi',
		'title'       => 'Sebaran Data Penduduk',
		'description' => 'Perbandingan penduduk laki-laki dan perempuan dalam pie chart dua dimensi.',
		'unit'        => 'jiwa',
		'type'        => 'pie',
	],
	'umurPiramida'    => [
		'number'      => '02',
		'nav_label'   => 'Umur',
		'label'       => 'Kelompok Umur',
		'title'       => 'Kelompok Umur Desa',
		'description' => 'Piramida umur menunjukkan struktur penduduk menurut kelompok usia dan jenis kelamin.',
		'unit'        => 'jiwa',
		'type'        => 'pyramid',
	],
	'pendidikan'      => [
		'number'      => '03',
		'nav_label'   => 'Pendidikan',
		'label'       => 'Sumber Daya Manusia',
		'title'       => 'Pendidikan',
		'description' => 'Sebaran pendidikan terakhir penduduk sebagai gambaran kapasitas sumber daya manusia.',
		'unit'        => 'penduduk',
		'type'        => 'bars',
	],
	'pekerjaan'       => [
		'number'      => '04',
		'nav_label'   => 'Pekerjaan',
		'label'       => 'Aktivitas Utama',
		'title'       => 'Pekerjaan',
		'description' => 'Komposisi aktivitas dan pekerjaan utama masyarakat di wilayah Kubang Tangah.',
		'unit'        => 'penduduk',
		'type'        => 'lollipop',
	],
	'pernikahan'      => [
		'number'      => '05',
		'nav_label'   => 'Pernikahan',
		'label'       => 'Kondisi Sosial',
		'title'       => 'Status Pernikahan',
		'description' => 'Proporsi status perkawinan penduduk dalam bentuk ringkas dan mudah dibandingkan.',
		'unit'        => 'penduduk',
		'type'        => 'donut',
	],
	'desil'           => [
		'number'      => '06',
		'nav_label'   => 'Desil',
		'label'       => 'Kesejahteraan Keluarga',
		'title'       => 'Distribusi Desil',
		'description' => 'Sebaran keluarga berdasarkan kelompok desil untuk mendukung perencanaan program yang tepat.',
		'unit'        => 'keluarga',
		'type'        => 'columns',
	],
	'bantuan'         => [
		'number'      => '07',
		'nav_label'   => 'Bantuan',
		'label'       => 'Perlindungan Sosial',
		'title'       => 'Pengelompokan Bantuan',
		'description' => 'Cakupan penerimaan bantuan keluarga menurut jenis program yang tercatat dalam data desa.',
		'unit'        => 'keluarga',
		'type'        => 'proportion',
	],
	'produkUnggulan'  => [
		'number'      => '08',
		'nav_label'   => 'Produk',
		'label'       => 'Ekonomi Kreatif',
		'title'       => 'Produk Unggulan',
		'description' => 'Potret pelaku ekonomi kreatif, subsektor, dan persebaran produk unggulan per dusun.',
		'unit'        => 'pelaku',
		'type'        => 'creative',
	],
];

$summary_cards = [
	[ 'label' => 'Penduduk', 'value' => $population, 'suffix' => 'Jiwa' ],
	[ 'label' => 'Keluarga', 'value' => $families, 'suffix' => 'KK' ],
	[ 'label' => 'Wilayah', 'value' => count( $dusun_names ), 'suffix' => 'Dusun' ],
	[ 'label' => 'Luas Wilayah', 'value' => $area_km2, 'suffix' => 'km2', 'decimals' => 2 ],
];

$product_cards = [
	[ 'label' => 'Pelaku Ekraf', 'value' => (int) ( $product_summary['totalActors'] ?? 0 ), 'suffix' => 'pelaku' ],
	[ 'label' => 'Nilai Produksi', 'value' => $format_rupiah( (float) ( $product_summary['totalProductionValue'] ?? 0 ) ), 'suffix' => 'per tahun', 'text' => true ],
	[ 'label' => 'Produk Unggulan', 'value' => (int) ( $product_summary['productCount'] ?? 0 ), 'suffix' => 'jenis produk' ],
	[ 'label' => 'Usaha Perorangan', 'value' => $format_percent( (float) ( $product_summary['individualShare'] ?? 0 ) ), 'suffix' => 'dari pelaku', 'text' => true ],
];

$poster_colors       = [ '#1d7a4a', '#2e9691', '#d5a33b', '#d86650', '#5179ad', '#8a61a3', '#79a855' ];
$gender_gradient     = $build_conic( (array) ( $gender_data['total'] ?? [] ), [ '#2374bd', '#f25289' ] );
$job_gradient        = $build_conic( (array) ( $job_compact['total'] ?? [] ), [ '#2d7df0', '#ff8b2a', '#ffc727', '#854ee7' ] );
$marriage_gradient   = $build_conic( (array) ( $marriage_data['total'] ?? [] ), [ '#f3bd17', '#f184a4', '#e73c33', '#58a93d' ] );
$subsector_gradient  = $build_conic( (array) ( $product_data['subsectors']['total'] ?? [] ), [ '#ffc20f', '#9c6a27' ] );
$asset_categories    = (array) ( $assets['categories'] ?? [] );
$asset_total         = (array) ( $assets['total'] ?? [] );
$facility_categories = (array) ( $facilities['categories'] ?? [] );
$facility_values     = (array) ( $facilities['total'] ?? [] );
?>

<main id="content" class="site-main village-home village-infographics-page" data-infographics-page data-source="<?php echo esc_url( $data_url ); ?>">
	<header id="ringkasan" class="village-infographics-page__intro" data-nav-section="all">
		<div class="village-infographics-page__lead">
			<p class="village-home__section-label">Desa Kubang Tangah</p>
			<h1>Statistik Desa</h1>
			<p>Informasi kependudukan, sosial, bantuan, dan potensi ekonomi kreatif disajikan dalam grafik interaktif dan infografik yang mudah dipahami.</p>

			<div class="village-infographics-page__view-tabs" role="tablist" aria-label="Pilih tampilan statistik">
				<button class="is-active" type="button" role="tab" aria-selected="true" data-stat-view="grafik">Grafik</button>
				<button type="button" role="tab" aria-selected="false" data-stat-view="infografik">Infografik</button>
			</div>

			<div class="village-infographics-page__sources" aria-label="Sumber data">
				<strong>Sumber data</strong>
				<?php foreach ( $sources as $source ) : ?>
					<span><?php echo esc_html( $source ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="village-home__data-summary" aria-label="Ringkasan statistik desa">
			<?php foreach ( $summary_cards as $card ) : ?>
				<div>
					<small><?php echo esc_html( $card['label'] ); ?></small>
					<strong data-summary-number="<?php echo esc_attr( $card['value'] ); ?>" <?php echo ! empty( $card['decimals'] ) ? 'data-summary-decimals="' . esc_attr( $card['decimals'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $format_number( $card['value'], $card['decimals'] ?? 0 ) ); ?>
					</strong>
					<span><?php echo esc_html( $card['suffix'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>

		<section class="village-infographics-page__product-spotlight" aria-labelledby="product-spotlight-title">
			<div>
				<p>Produk Unggulan</p>
				<h2 id="product-spotlight-title">Potret Ekonomi Kreatif Desa</h2>
				<p>Produk tenun songket, kerupuk ubi, makanan-minuman, dan kue menjadi bagian dari penggerak ekonomi warga Kubang Tangah.</p>
			</div>
			<div class="village-infographics-page__product-cards">
				<?php foreach ( $product_cards as $card ) : ?>
					<div>
						<small><?php echo esc_html( $card['label'] ); ?></small>
						<strong>
							<?php
							echo ! empty( $card['text'] )
								? esc_html( $card['value'] )
								: esc_html( $format_number( $card['value'] ) );
							?>
						</strong>
						<span><?php echo esc_html( $card['suffix'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="village-infographics-page__product-strip" aria-label="Pelaku ekonomi kreatif per dusun">
				<?php
				$product_dusun_labels = (array) ( $product_data['byDusun']['categories'] ?? [] );
				$product_dusun_values = (array) ( $product_data['byDusun']['total'] ?? [] );
				$product_dusun_max    = max( 1, max( array_map( 'intval', $product_dusun_values ?: [ 1 ] ) ) );
				foreach ( $product_dusun_labels as $index => $dusun_label ) :
					$value = (int) ( $product_dusun_values[ $index ] ?? 0 );
					?>
					<div>
						<span><?php echo esc_html( $dusun_label ); ?></span>
						<i style="--bar-size: <?php echo esc_attr( round( ( $value / $product_dusun_max ) * 100, 2 ) ); ?>%;"></i>
						<strong><?php echo esc_html( $format_number( $value ) ); ?> pelaku</strong>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</header>

	<div class="village-infographics-page__controls" data-graph-controls>
		<nav class="village-infographics-page__jumps" aria-label="Pilih bagian grafik">
			<a href="#ringkasan" class="is-active" data-jump="all">Semua</a>
			<?php foreach ( $sections as $key => $section ) : ?>
				<a href="#<?php echo esc_attr( $key ); ?>" data-jump="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $section['nav_label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="village-infographics-page__actions">
			<span class="village-infographics-page__period">Data 2026</span>
			<label class="village-home__dusun-filter">
				<span>Wilayah</span>
				<select data-region aria-label="Pilih dusun untuk grafik statistik">
					<option value="all">Semua Dusun</option>
				</select>
			</label>
		</div>
	</div>

	<div class="village-infographics-page__panel is-active" data-stat-panel="grafik">
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
	</div>

	<div class="village-infographics-page__panel" data-stat-panel="infografik" hidden>
		<section class="village-infographics-page__poster-grid" aria-label="Infografik statistik desa">
			<article class="village-infographics-page__poster village-infographics-page__poster--age">
				<header>
					<span>Infografik 01</span>
					<h2>Usia Produktif Dominasi Penduduk Desa Kubang Tangah 2026</h2>
				</header>
				<div class="village-infographics-page__poster-pyramid" aria-label="Piramida umur">
					<?php foreach ( array_reverse( $age_categories, true ) as $index => $age_label ) : ?>
						<?php
						$male_value   = (int) ( $age_male[ $index ] ?? 0 );
						$female_value = (int) ( $age_female[ $index ] ?? 0 );
						?>
						<div>
							<i style="--bar-size: <?php echo esc_attr( round( ( $female_value / $max_age_value ) * 100, 2 ) ); ?>%;"><span><?php echo esc_html( $format_number( $female_value ) ); ?></span></i>
							<strong><?php echo esc_html( $age_label ); ?></strong>
							<i style="--bar-size: <?php echo esc_attr( round( ( $male_value / $max_age_value ) * 100, 2 ) ); ?>%;"><span><?php echo esc_html( $format_number( $male_value ) ); ?></span></i>
						</div>
					<?php endforeach; ?>
				</div>
				<footer>
					<div><strong><?php echo esc_html( $format_number( $population ) ); ?></strong><span>Total penduduk</span></div>
					<div><strong><?php echo esc_html( $format_percent( (float) ( $productive['share'] ?? 0 ) ) ); ?></strong><span>usia produktif 15-64 tahun</span></div>
					<p>Usia produktif sebanyak <?php echo esc_html( $format_number( $productive['total'] ?? 0 ) ); ?> orang, terdiri dari <?php echo esc_html( $format_number( $productive['male'] ?? 0 ) ); ?> laki-laki dan <?php echo esc_html( $format_number( $productive['female'] ?? 0 ) ); ?> perempuan.</p>
				</footer>
			</article>

			<article class="village-infographics-page__poster village-infographics-page__poster--work">
				<header>
					<span>Infografik 02</span>
					<h2>Keadaan Pekerjaan Penduduk Desa Kubang Tangah 2026</h2>
				</header>
				<div class="village-infographics-page__poster-split">
					<div class="village-infographics-page__poster-donut" style="--poster-gradient: <?php echo esc_attr( $job_gradient ); ?>;">
						<strong><?php echo esc_html( $format_number( array_sum( (array) ( $job_compact['total'] ?? [] ) ) ) ); ?></strong>
						<span>penduduk</span>
					</div>
					<div class="village-infographics-page__poster-list">
						<?php foreach ( (array) ( $job_compact['categories'] ?? [] ) as $index => $category ) : ?>
							<?php $value = (int) ( $job_compact['total'][ $index ] ?? 0 ); ?>
							<p><i style="background: <?php echo esc_attr( $poster_colors[ $index % count( $poster_colors ) ] ); ?>;"></i><span><?php echo esc_html( $category ); ?></span><strong><?php echo esc_html( $format_number( $value ) ); ?></strong></p>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="village-infographics-page__poster-bars">
					<?php foreach ( (array) ( $job_compact['byDusun'] ?? [] ) as $dusun_label => $values ) : ?>
						<?php $total = array_sum( (array) $values ); ?>
						<div><span><?php echo esc_html( $dusun_label ); ?></span><i style="--bar-size: <?php echo esc_attr( round( ( $total / max( 1, $largest_total ) ) * 100, 2 ) ); ?>%;"></i><strong><?php echo esc_html( $format_number( $total ) ); ?></strong></div>
					<?php endforeach; ?>
				</div>
			</article>

			<article class="village-infographics-page__poster village-infographics-page__poster--marriage">
				<header>
					<span>Infografik 03</span>
					<h2>Status Pernikahan Penduduk Desa Kubang Tangah Tahun 2026</h2>
				</header>
				<div class="village-infographics-page__poster-split">
					<div class="village-infographics-page__poster-donut" style="--poster-gradient: <?php echo esc_attr( $marriage_gradient ); ?>;">
						<strong><?php echo esc_html( $format_number( array_sum( (array) ( $marriage_data['total'] ?? [] ) ) ) ); ?></strong>
						<span>jiwa</span>
					</div>
					<div class="village-infographics-page__poster-list">
						<?php foreach ( (array) ( $marriage_data['categories'] ?? [] ) as $index => $category ) : ?>
							<?php $value = (int) ( $marriage_data['total'][ $index ] ?? 0 ); ?>
							<p><i style="background: <?php echo esc_attr( $poster_colors[ $index % count( $poster_colors ) ] ); ?>;"></i><span><?php echo esc_html( $category ); ?></span><strong><?php echo esc_html( $format_number( $value ) ); ?></strong></p>
						<?php endforeach; ?>
					</div>
				</div>
				<footer>
					<p>Struktur status perkawinan didominasi kelompok belum kawin dan kawin, sehingga dapat menjadi dasar penyusunan program pembangunan dan pelayanan sosial desa.</p>
				</footer>
			</article>

			<article class="village-infographics-page__poster village-infographics-page__poster--product">
				<header>
					<span>Infografik 04</span>
					<h2>Potret Ekonomi Kreatif Desa Kubang Tangah 2026</h2>
				</header>
				<div class="village-infographics-page__poster-metrics">
					<div><strong><?php echo esc_html( $format_number( $product_summary['totalActors'] ?? 0 ) ); ?></strong><span>Pelaku ekonomi kreatif</span></div>
					<div><strong><?php echo esc_html( $format_percent( $product_summary['individualShare'] ?? 0 ) ); ?></strong><span>usaha perorangan</span></div>
					<div><strong><?php echo esc_html( $format_rupiah( $product_summary['averageProductionValue'] ?? 0 ) ); ?></strong><span>rata-rata nilai produksi/tahun</span></div>
				</div>
				<div class="village-infographics-page__poster-split">
					<div class="village-infographics-page__poster-donut" style="--poster-gradient: <?php echo esc_attr( $subsector_gradient ); ?>;">
						<strong><?php echo esc_html( $format_number( $product_summary['subsectorCount'] ?? 0 ) ); ?></strong>
						<span>subsektor</span>
					</div>
					<div class="village-infographics-page__poster-bars">
						<?php foreach ( $product_dusun_labels as $index => $dusun_label ) : ?>
							<?php $value = (int) ( $product_dusun_values[ $index ] ?? 0 ); ?>
							<div><span><?php echo esc_html( $dusun_label ); ?></span><i style="--bar-size: <?php echo esc_attr( round( ( $value / $product_dusun_max ) * 100, 2 ) ); ?>%;"></i><strong><?php echo esc_html( $format_number( $value ) ); ?></strong></div>
						<?php endforeach; ?>
					</div>
				</div>
			</article>

			<article class="village-infographics-page__poster village-infographics-page__poster--facility">
				<header>
					<span>Infografik 05</span>
					<h2>Rumah, Aset, dan Fasilitas Penduduk</h2>
				</header>
				<div class="village-infographics-page__poster-metrics">
					<div><strong><?php echo esc_html( $format_number( $facilities['houses'] ?? 366 ) ); ?></strong><span>rumah</span></div>
					<?php foreach ( $facility_categories as $index => $category ) : ?>
						<div><strong><?php echo esc_html( $format_number( $facility_values[ $index ] ?? 0 ) ); ?></strong><span><?php echo esc_html( str_replace( 'Fasilitas ', '', $category ) ); ?></span></div>
					<?php endforeach; ?>
				</div>
				<div class="village-infographics-page__poster-assets">
					<?php foreach ( $asset_categories as $index => $category ) : ?>
						<div><span><?php echo esc_html( $category ); ?></span><strong><?php echo esc_html( $format_number( $asset_total[ $index ] ?? 0 ) ); ?></strong></div>
					<?php endforeach; ?>
				</div>
			</article>
		</section>
	</div>
</main>

<?php
get_footer();
