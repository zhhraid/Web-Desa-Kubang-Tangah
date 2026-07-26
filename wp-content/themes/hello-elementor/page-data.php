<?php
/**
 * Information and village assistance transparency page.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data_path = get_template_directory() . '/assets/data/village-aid-recipients.json';
$aid_data  = [];

if ( is_readable( $data_path ) ) {
	$decoded = json_decode( (string) file_get_contents( $data_path ), true );
	if ( is_array( $decoded ) ) {
		$aid_data = $decoded;
	}
}

$households = isset( $aid_data['households'] ) && is_array( $aid_data['households'] )
	? array_values( $aid_data['households'] )
	: [];

$households = array_values(
	array_filter(
		$households,
		static function ( $household ) {
			return is_array( $household )
				&& ! empty( $household['name'] )
				&& isset( $household['decile'] )
				&& ! empty( $household['assistance'] );
		}
	)
);

usort(
	$households,
	static function ( $first, $second ) {
		return strnatcasecmp( (string) $first['name'], (string) $second['name'] );
	}
);

$assistance_counts      = [];
$filter_assistance      = [];
$decile_values          = [];
$priority_count         = 0;
$total_recipients       = 0;
$not_assisted_label     = 'Tidak mendapat bantuan';
$hero_image_url         = content_url( 'uploads/2026/01/IMG_20240220_130851.jpg' );

foreach ( $households as $household ) {
	$assistance = trim( (string) $household['assistance'] );
	$decile     = $household['decile'];
	$receives   = isset( $household['receives_aid'] ) ? (bool) $household['receives_aid'] : $assistance !== $not_assisted_label;

	$filter_assistance[ $assistance ] = isset( $filter_assistance[ $assistance ] )
		? $filter_assistance[ $assistance ] + 1
		: 1;
	$decile_values[ (string) $decile ] = $decile;

	if ( is_numeric( $decile ) && (int) $decile >= 1 && (int) $decile <= 5 ) {
		++$priority_count;
	}

	if ( $receives ) {
		++$total_recipients;
		$assistance_counts[ $assistance ] = isset( $assistance_counts[ $assistance ] )
			? $assistance_counts[ $assistance ] + 1
			: 1;
	}
}

arsort( $assistance_counts );
uksort(
	$filter_assistance,
	static function ( $first, $second ) use ( $not_assisted_label ) {
		if ( $first === $not_assisted_label ) {
			return 1;
		}
		if ( $second === $not_assisted_label ) {
			return -1;
		}
		return strnatcasecmp( $first, $second );
	}
);
uksort(
	$decile_values,
	static function ( $first, $second ) {
		if ( is_numeric( $first ) && is_numeric( $second ) ) {
			return (int) $first <=> (int) $second;
		}
		if ( is_numeric( $first ) ) {
			return -1;
		}
		if ( is_numeric( $second ) ) {
			return 1;
		}
		return strnatcasecmp( $first, $second );
	}
);

$total_households = count( $households );
$latest_information = new WP_Query(
	[
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'category_name'       => 'informasi-desa',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	]
);

get_header();
?>

<main class="village-information" data-information-root>
	<section class="village-information__hero" aria-labelledby="information-title">
		<div class="village-information__inner village-information__hero-layout">
			<div class="village-information__intro">
				<p class="village-information__eyebrow">Desa Kubang Tangah</p>
				<h1 id="information-title">Informasi</h1>
				<p class="village-information__lead">
					Pusat informasi publik desa untuk pengumuman, transparansi data, dan kabar layanan masyarakat yang dapat dikembangkan sesuai kebutuhan admin desa.
				</p>
				<a href="#informasi-terbaru">Lihat Informasi Desa <span aria-hidden="true">&darr;</span></a>
			</div>

			<div class="village-information__hero-visual" aria-label="Dokumentasi informasi desa">
				<img src="<?php echo esc_url( $hero_image_url ); ?>" alt="Dokumentasi kegiatan Desa Kubang Tangah">
				<div class="village-information__hero-card">
					<span>Informasi Publik</span>
					<strong>Terbuka, rapi, dan mudah diakses masyarakat.</strong>
				</div>
			</div>
		</div>
	</section>

	<section id="informasi-terbaru" class="village-information__latest" aria-labelledby="latest-information-title">
		<div class="village-information__inner">
			<header class="village-information__section-heading">
				<div>
					<p>Pengumuman Resmi</p>
					<h2 id="latest-information-title">Informasi Desa Terbaru</h2>
				</div>
				<span>Publikasi Admin Desa</span>
			</header>

			<?php if ( $latest_information->have_posts() ) : ?>
				<div class="village-information__latest-grid">
					<?php while ( $latest_information->have_posts() ) : ?>
						<?php $latest_information->the_post(); ?>
						<article class="village-information__latest-item">
							<p><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></p>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<div><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '...' ) ); ?></div>
							<a href="<?php the_permalink(); ?>">Baca informasi <span aria-hidden="true">&rarr;</span></a>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="village-information__empty-latest" role="status">
					<span aria-hidden="true">i</span>
					<div>
						<strong>Belum ada informasi terbaru yang diterbitkan.</strong>
						<p>Silakan kembali secara berkala untuk melihat pembaruan resmi dari Pemerintah Desa Kubang Tangah.</p>
					</div>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>

	<section id="daftar-keluarga" class="village-information__data" aria-labelledby="household-list-title">
		<div class="village-information__inner">
			<header class="village-information__data-heading">
				<div>
					<p>Data Keluarga</p>
					<h2 id="household-list-title">Tabel Transparansi Bantuan</h2>
					<span>Seluruh kepala keluarga ditampilkan berdasarkan desil dan status bantuan.</span>
				</div>
			</header>

			<div class="village-information__toolbar" aria-label="Filter daftar keluarga">
				<label class="village-information__search">
					<span>Cari kepala keluarga</span>
					<input type="search" data-information-search placeholder="Ketik nama kepala keluarga" autocomplete="off">
				</label>

				<label>
					<span>Status bantuan</span>
					<select data-information-assistance>
						<option value="">Semua status</option>
						<?php foreach ( array_keys( $filter_assistance ) as $assistance ) : ?>
							<option value="<?php echo esc_attr( $assistance ); ?>"><?php echo esc_html( $assistance ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>

				<label>
					<span>Desil</span>
					<select data-information-decile>
						<option value="">Semua desil</option>
						<?php foreach ( $decile_values as $decile ) : ?>
							<option value="<?php echo esc_attr( (string) $decile ); ?>">
								<?php echo esc_html( is_numeric( $decile ) ? 'Desil ' . $decile : (string) $decile ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>

				<button type="button" data-information-reset hidden>Reset filter</button>
			</div>

			<div class="village-information__table-meta">
				<label>
					<span>Baris per halaman</span>
					<select data-information-page-size>
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="50">50</option>
					</select>
				</label>
			</div>

			<div class="village-information__table-wrap">
				<table>
					<caption class="screen-reader-text">Tabel transparansi bantuan keluarga Desa Kubang Tangah</caption>
					<thead>
						<tr>
							<th scope="col">No.</th>
							<th scope="col" data-information-sort-heading="name" aria-sort="ascending">
								<button type="button" data-information-sort="name">Nama Kepala Keluarga <span aria-hidden="true"></span></button>
							</th>
							<th scope="col" data-information-sort-heading="decile" aria-sort="none">
								<button type="button" data-information-sort="decile">Desil <span aria-hidden="true"></span></button>
							</th>
							<th scope="col" data-information-sort-heading="assistance" aria-sort="none">
								<button type="button" data-information-sort="assistance">Jenis Bantuan <span aria-hidden="true"></span></button>
							</th>
						</tr>
					</thead>
					<tbody data-information-body>
						<?php foreach ( $households as $index => $household ) : ?>
							<?php
							$name       = trim( (string) $household['name'] );
							$decile     = (string) $household['decile'];
							$assistance = trim( (string) $household['assistance'] );
							$receives   = isset( $household['receives_aid'] ) ? (bool) $household['receives_aid'] : $assistance !== $not_assisted_label;
							?>
							<tr
								data-information-row
								data-name="<?php echo esc_attr( strtolower( $name ) ); ?>"
								data-decile="<?php echo esc_attr( $decile ); ?>"
								data-assistance="<?php echo esc_attr( $assistance ); ?>"
							>
								<td data-label="Nomor" data-information-number><?php echo esc_html( (string) ( $index + 1 ) ); ?></td>
								<td data-label="Nama Kepala Keluarga"><strong><?php echo esc_html( $name ); ?></strong></td>
								<td data-label="Desil"><span class="village-information__decile"><?php echo esc_html( is_numeric( $decile ) ? 'Desil ' . $decile : $decile ); ?></span></td>
								<td data-label="Jenis Bantuan"><span class="village-information__aid <?php echo $receives ? 'is-' . esc_attr( sanitize_title( $assistance ) ) : 'is-no-aid'; ?>"><?php echo esc_html( $assistance ); ?></span></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<div class="village-information__no-results" data-information-empty hidden>
					<strong>Data keluarga tidak ditemukan.</strong>
					<p>Periksa kembali nama atau pilihan filter.</p>
				</div>
			</div>

			<footer class="village-information__pagination">
				<p data-information-range>Menampilkan 1 sampai 10 dari <?php echo esc_html( number_format_i18n( $total_households ) ); ?> keluarga</p>
				<div>
					<button type="button" data-information-previous aria-label="Halaman sebelumnya">&larr;</button>
					<span data-information-page>Halaman 1</span>
					<button type="button" data-information-next aria-label="Halaman berikutnya">&rarr;</button>
				</div>
			</footer>

			<p class="village-information__privacy-note">
				Nomor kartu keluarga dan rincian kondisi rumah tidak ditampilkan pada halaman publik.
			</p>
		</div>
	</section>
</main>

<?php get_footer(); ?>
