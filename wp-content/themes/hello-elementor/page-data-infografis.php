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
$sources           = (array) ( $meta['sources'] ?? [] );
$product_data      = (array) ( $infographic_data['produkUnggulan'] ?? [] );
$product_summary   = (array) ( $product_data['summary'] ?? [] );
$format_number     = static function ( $value, $decimals = 0 ) {
	return number_format( (float) $value, $decimals, ',', '.' );
};
$format_percent    = static function ( $value ) use ( $format_number ) {
	return $format_number( $value, 2 ) . '%';
};
$format_rupiah     = static function ( $value ) use ( $format_number ) {
	return 'Rp ' . $format_number( (float) $value / 1000000, 2 ) . ' jt';
};

$sections = [
	'sebaranPenduduk' => [
		'number'      => '01',
		'nav_label'   => 'Penduduk',
		'label'       => 'Penduduk',
		'title'       => 'Sebaran Penduduk Menurut Jenis Kelamin',
		'description' => 'Komposisi laki-laki dan perempuan, baik seluruh desa maupun per dusun yang dipilih.',
		'source'      => 'Dukcapil Kota Sawahlunto dan Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'jiwa',
		'type'        => 'pie',
	],
	'umurPiramida'    => [
		'number'      => '02',
		'nav_label'   => 'Usia',
		'label'       => 'Demografi',
		'title'       => 'Piramida Umur dan Usia Produktif',
		'description' => 'Struktur umur laki-laki dan perempuan, termasuk penduduk usia produktif 15-64 tahun.',
		'source'      => 'Dukcapil Kota Sawahlunto dan Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'jiwa',
		'type'        => 'pyramid',
	],
	'pendidikan'      => [
		'number'      => '03',
		'nav_label'   => 'Pendidikan',
		'label'       => 'Pendidikan',
		'title'       => 'Pendidikan Terakhir Penduduk',
		'description' => 'Tingkat pendidikan warga untuk membaca kebutuhan layanan pendidikan dan pengembangan SDM.',
		'source'      => 'Dukcapil Kota Sawahlunto dan Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'penduduk',
		'type'        => 'bars',
	],
	'pekerjaan'       => [
		'number'      => '04',
		'nav_label'   => 'Rinci Kerja',
		'label'       => 'Pekerjaan',
		'title'       => 'Rincian Pekerjaan Penduduk',
		'description' => 'Status pekerjaan rinci, termasuk pelajar, belum bekerja, petani, karyawan, wiraswasta, dan lainnya.',
		'source'      => 'Dukcapil Kota Sawahlunto dan Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'penduduk',
		'type'        => 'lollipop',
	],
	'pernikahan'      => [
		'number'      => '05',
		'nav_label'   => 'Pernikahan',
		'label'       => 'Kondisi Sosial',
		'title'       => 'Status Pernikahan',
		'description' => 'Proporsi status perkawinan penduduk dalam bentuk ringkas dan mudah dibandingkan.',
		'source'      => 'Dukcapil Kota Sawahlunto dan Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'penduduk',
		'type'        => 'donut',
	],
	'desil'           => [
		'number'      => '06',
		'nav_label'   => 'Desil',
		'label'       => 'Kesejahteraan',
		'title'       => 'Kelompok Desil Kesejahteraan',
		'description' => 'Distribusi keluarga menurut kelompok desil yang tercatat dalam data desa.',
		'source'      => 'SIKS-NG Kemensos dan data administrasi keluarga Desa Kubang Tangah.',
		'unit'        => 'keluarga',
		'type'        => 'columns',
	],
	'bantuan'         => [
		'number'      => '07',
		'nav_label'   => 'Bantuan',
		'label'       => 'Bantuan Sosial',
		'title'       => 'Penerima Bantuan Sosial',
		'description' => 'Perbandingan keluarga penerima bantuan dan keluarga yang tidak menerima bantuan pada data desa.',
		'source'      => 'SIKS-NG Kemensos dan data administrasi bantuan Desa Kubang Tangah.',
		'unit'        => 'keluarga',
		'type'        => 'proportion',
	],
	'produkUnggulan'  => [
		'number'      => '08',
		'nav_label'   => 'Produk',
		'label'       => 'Ekonomi Kreatif',
		'title'       => 'Produk Unggulan',
		'description' => 'Produk utama desa menurut jumlah unit usaha, persebaran dusun, dan kapasitas produksi per jenis produk.',
		'source'      => 'Pendataan ekonomi kreatif Desa Kubang Tangah dan program Desa Cantik.',
		'unit'        => 'unit usaha',
		'type'        => 'creative',
	],
	'kondisiRumah'    => [
		'number'      => '09',
		'nav_label'   => 'Rumah',
		'label'       => 'Hunian',
		'title'       => 'Kondisi Rumah Penduduk',
		'description' => 'Kondisi atap, lantai, dan dinding sebagai gambaran kualitas hunian warga.',
		'source'      => 'Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'rumah',
		'type'        => 'housing',
	],
	'aset'            => [
		'number'      => '10',
		'nav_label'   => 'Aset',
		'label'       => 'Kepemilikan',
		'title'       => 'Kepemilikan Aset per Dusun',
		'description' => 'Kepemilikan motor, mobil, dan TV pada masing-masing dusun.',
		'source'      => 'Data Administrasi Kependudukan Desa Kubang Tangah.',
		'unit'        => 'catatan aset',
		'type'        => 'assets',
	],
	'fasilitas'       => [
		'number'      => '11',
		'nav_label'   => 'Fasilitas',
		'label'       => 'Layanan Umum',
		'title'       => 'Fasilitas Umum Desa',
		'description' => 'Puskesmas pembantu, sekolah, lapangan bola, masjid, dan surau yang tercatat di desa.',
		'source'      => 'Profil Desa Kubang Tangah dan pendataan fasilitas umum Desa Cantik.',
		'unit'        => 'fasilitas',
		'type'        => 'bars',
	],
];

$infographic_asset_url  = get_template_directory_uri() . '/assets/images/infographics/';
$infographic_asset_path = get_template_directory() . '/assets/images/infographics/';
$infographic_candidates = [
	[
		'title' => 'Statistik Pendidikan',
		'file'  => 'infografis-pendidikan.jpeg',
		'alt'   => 'Infografis statistik pendidikan Desa Kubang Tangah',
	],
	[
		'title' => 'Desil Kesejahteraan',
		'file'  => 'infografis-desil.jpeg',
		'alt'   => 'Infografis desil kesejahteraan kepala keluarga Desa Kubang Tangah',
	],
	[
		'title' => 'Rumah dan Fasilitas Penduduk',
		'file'  => 'infografis-rumah-fasilitas.jpeg',
		'alt'   => 'Infografis keadaan rumah dan fasilitas penduduk Desa Kubang Tangah',
	],
	[
		'title' => 'Potret Ekonomi Kreatif',
		'file'  => 'infografis-ekonomi-kreatif.jpeg',
		'alt'   => 'Infografis potret ekonomi kreatif Desa Kubang Tangah',
	],
	[
		'title' => 'Status Pernikahan Penduduk',
		'file'  => 'infografis-pernikahan.jpeg',
		'alt'   => 'Infografis status pernikahan penduduk Desa Kubang Tangah',
	],
	[
		'title' => 'Usia Produktif Penduduk',
		'file'  => 'infografis-usia-produktif.jpeg',
		'alt'   => 'Infografis usia produktif penduduk Desa Kubang Tangah',
	],
	[
		'title' => 'Pekerjaan Penduduk',
		'file'  => 'infografis-pekerjaan.jpeg',
		'alt'   => 'Infografis keadaan pekerjaan penduduk Desa Kubang Tangah',
	],
];
$infographic_posters    = [];

foreach ( $infographic_candidates as $candidate ) {
	if ( ! file_exists( $infographic_asset_path . $candidate['file'] ) ) {
		continue;
	}

	$candidate['image']  = $infographic_asset_url . $candidate['file'];
	$infographic_posters[] = $candidate;
}

$product_categories = (array) ( $product_data['products']['categories'] ?? [] );
$product_totals     = (array) ( $product_data['products']['total'] ?? [] );
$product_values     = (array) ( $product_data['products']['value'] ?? [] );
$product_highlights = [];
foreach ( $product_categories as $index => $product_category ) {
	$product_highlights[] = [
		'label'  => $product_category,
		'actors' => (int) ( $product_totals[ $index ] ?? 0 ),
		'value'  => (float) ( $product_values[ $index ] ?? 0 ),
	];
}
usort(
	$product_highlights,
	static function ( $first, $second ) {
		return $second['actors'] <=> $first['actors'];
	}
);

$product_dusun_labels = (array) ( $product_data['byDusun']['categories'] ?? [] );
$product_dusun_values = (array) ( $product_data['byDusun']['total'] ?? [] );
$product_dusun_items  = [];
foreach ( $product_dusun_labels as $index => $dusun_label ) {
	$product_dusun_items[] = [
		'label' => $dusun_label,
		'value' => (int) ( $product_dusun_values[ $index ] ?? 0 ),
	];
}
usort(
	$product_dusun_items,
	static function ( $first, $second ) {
		return $second['value'] <=> $first['value'];
	}
);
$product_dusun_max = max( 1, max( array_column( $product_dusun_items ?: [ [ 'value' => 1 ] ], 'value' ) ) );
?>

<main id="content" class="site-main village-home village-infographics-page" data-infographics-page data-source="<?php echo esc_url( $data_url ); ?>">
	<header id="ringkasan" class="village-infographics-page__intro" data-nav-section="all">
		<div class="village-infographics-page__lead">
			<p class="village-home__section-label">Desa Kubang Tangah</p>
			<h1>Statistik Desa</h1>
			<p>Statistik Desa Kubang Tangah mencakup struktur penduduk, pekerjaan, pernikahan, ekonomi kreatif, kondisi rumah, aset, dan fasilitas umum.</p>

			<div class="village-infographics-page__sources" aria-label="Sumber data">
				<strong>Sumber data</strong>
				<?php foreach ( $sources as $source ) : ?>
					<span><?php echo esc_html( $source ); ?></span>
				<?php endforeach; ?>
			</div>

		</div>

		<section class="village-infographics-page__product-spotlight" aria-labelledby="product-spotlight-title">
			<div class="village-infographics-page__product-copy">
				<p>Produk Unggulan</p>
				<h2 id="product-spotlight-title">Tenun Songket, Kerupuk Ubi, Makanan & Minuman, dan Kue</h2>
				<p>Empat produk ini menjadi wajah ekonomi kreatif warga dengan 43 unit usaha dan nilai produksi tahunan yang tercatat.</p>
				<div class="village-infographics-page__product-metrics" aria-label="Ringkasan ekonomi kreatif">
					<div><strong><?php echo esc_html( $format_number( $product_summary['totalActors'] ?? 0 ) ); ?></strong><span>unit usaha ekraf</span></div>
					<div><strong><?php echo esc_html( $format_percent( $product_summary['individualShare'] ?? 0 ) ); ?></strong><span>usaha perorangan</span></div>
					<div><strong><?php echo esc_html( $format_rupiah( $product_summary['averageProductionValue'] ?? 0 ) ); ?></strong><span>rata-rata produksi/tahun</span></div>
				</div>
			</div>
			<div class="village-infographics-page__product-cards">
				<?php foreach ( $product_highlights as $product ) : ?>
					<div>
						<small><?php echo esc_html( $product['label'] ); ?></small>
						<strong>
							<?php echo esc_html( $format_number( $product['actors'] ) ); ?>
						</strong>
						<span>unit usaha - <?php echo esc_html( $format_rupiah( $product['value'] ) ); ?>/tahun</span>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="village-infographics-page__product-strip" aria-label="Unit usaha ekonomi kreatif per dusun">
				<?php foreach ( $product_dusun_items as $dusun_item ) : ?>
					<div>
						<span><?php echo esc_html( $dusun_item['label'] ); ?></span>
						<i style="--bar-size: <?php echo esc_attr( round( ( $dusun_item['value'] / $product_dusun_max ) * 100, 2 ) ); ?>%;"></i>
						<strong><?php echo esc_html( $format_number( $dusun_item['value'] ) ); ?> unit usaha</strong>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</header>

	<div class="village-infographics-page__toolbar">
		<div class="village-infographics-page__controls" data-graph-controls>
			<div class="village-infographics-page__actions">
				<span class="village-infographics-page__period">Data Desa</span>
				<label class="village-home__dusun-filter">
					<span>Wilayah</span>
					<div class="village-infographics-page__select" data-region-picker>
						<button type="button" data-region-toggle aria-expanded="false" aria-haspopup="listbox">Semua Dusun</button>
						<div class="village-infographics-page__select-menu" data-region-menu role="listbox" hidden>
							<button type="button" class="is-active" data-region-option="all" role="option" aria-selected="true">Semua Dusun</button>
						</div>
						<select class="village-infographics-page__native-select" data-region aria-label="Pilih dusun untuk grafik statistik" tabindex="-1">
							<option value="all">Semua Dusun</option>
						</select>
					</div>
				</label>
			</div>
		</div>

		<div class="village-infographics-page__view-tabs" role="tablist" aria-label="Tampilan statistik desa">
			<button type="button" class="is-active" data-stat-view="grafik" role="tab" aria-selected="true" aria-controls="statistik-grafik">Grafik</button>
			<button type="button" data-stat-view="infografis" role="tab" aria-selected="false" aria-controls="statistik-infografis">Infografis</button>
		</div>
	</div>

	<div id="statistik-grafik" class="village-infographics-page__panel is-active" data-stat-panel="grafik" role="tabpanel">
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
					<p class="village-infographics-page__section-source"><span>Sumber:</span> <?php echo esc_html( $section['source'] ); ?></p>
				</section>
			<?php endforeach; ?>
		</div>
	</div>

	<div id="statistik-infografis" class="village-infographics-page__panel" data-stat-panel="infografis" role="tabpanel" hidden>
		<section class="village-infographics-page__poster-carousel" aria-labelledby="infografis-title">
			<header class="village-infographics-page__poster-head">
				<div>
					<p class="village-home__section-label">Infografis Desa</p>
					<h2 id="infografis-title">Poster Statistik Kubang Tangah</h2>
					<p>Infografis asli ditampilkan sebagai kartu geser. Klik gambar untuk melihat poster penuh.</p>
				</div>
			</header>

			<div class="village-infographics-page__poster-track" data-infographic-track>
				<?php foreach ( $infographic_posters as $index => $poster ) : ?>
					<article class="village-infographics-page__poster-card">
						<figure>
							<button
								type="button"
								class="village-infographics-page__poster-open"
								data-infographic-open
								data-full-image="<?php echo esc_url( $poster['image'] ); ?>"
								data-full-title="<?php echo esc_attr( $poster['title'] ); ?>"
								data-full-alt="<?php echo esc_attr( $poster['alt'] ); ?>"
								aria-label="<?php echo esc_attr( 'Lihat penuh ' . $poster['title'] ); ?>"
							>
								<img src="<?php echo esc_url( $poster['image'] ); ?>" alt="<?php echo esc_attr( $poster['alt'] ); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>">
							</button>
						</figure>
						<footer>
							<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<strong><?php echo esc_html( $poster['title'] ); ?></strong>
						</footer>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<dialog class="village-infographics-page__lightbox" data-infographic-dialog aria-label="Pratinjau infografis">
			<button type="button" class="village-infographics-page__lightbox-close" data-infographic-close aria-label="Tutup pratinjau">&times;</button>
			<figure>
				<img src="" alt="" data-infographic-dialog-image>
				<figcaption data-infographic-dialog-title></figcaption>
			</figure>
		</dialog>
	</div>
</main>

<?php
get_footer();
