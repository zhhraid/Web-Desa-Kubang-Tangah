<?php
/**
 * Interactive gallery page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$elementor_data = json_decode( get_post_meta( get_the_ID(), '_elementor_data', true ), true );
$raw_galleries  = [];

$collect_galleries = static function ( array $nodes ) use ( &$collect_galleries, &$raw_galleries ) {
	foreach ( $nodes as $node ) {
		if ( 'image-gallery' === ( $node['widgetType'] ?? '' ) ) {
			$raw_galleries[] = array_values(
				array_filter(
					array_map(
						static fn( $image ) => (int) ( $image['id'] ?? 0 ),
						$node['settings']['wp_gallery'] ?? []
					)
				)
			);
		}

		if ( ! empty( $node['elements'] ) ) {
			$collect_galleries( $node['elements'] );
		}
	}
};

$collect_galleries( is_array( $elementor_data ) ? $elementor_data : [] );

$gallery_years = [];
foreach ( [ 2024, 2025, 2026 ] as $index => $year ) {
	if ( ! empty( $raw_galleries[ $index ] ) ) {
		$gallery_years[ (string) $year ] = $raw_galleries[ $index ];
	}
}

if ( ! $gallery_years ) {
	$gallery_years['2026'] = [ 2519, 2520, 2521, 2522, 2523, 2524 ];
}

krsort( $gallery_years, SORT_NUMERIC );

$all_image_ids = array_values( array_unique( array_merge( ...array_values( $gallery_years ) ) ) );
if ( $all_image_ids ) {
	get_posts(
		[
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'post__in'               => $all_image_ids,
			'posts_per_page'         => -1,
			'orderby'                => 'post__in',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		]
	);
}

$hero_image_ids = [];
foreach ( $gallery_years as $image_ids ) {
	$hero_image_ids = array_merge( $hero_image_ids, array_slice( $image_ids, 0, 2 ) );
}
$hero_image_ids = array_slice( array_values( array_unique( $hero_image_ids ) ), 0, 6 );
?>

<main id="content" class="village-gallery">
	<section class="village-gallery__hero" aria-labelledby="gallery-title">
		<div class="village-gallery__hero-mosaic" aria-hidden="true">
			<?php foreach ( $hero_image_ids as $index => $image_id ) : ?>
				<figure class="village-gallery__hero-tile village-gallery__hero-tile--<?php echo esc_attr( (string) ( $index + 1 ) ); ?>">
					<?php
					echo wp_get_attachment_image(
						$image_id,
						'large',
						false,
						[
							'alt'           => '',
							'loading'       => 'eager',
							'fetchpriority' => 0 === $index ? 'high' : 'auto',
						]
					);
					?>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="village-gallery__hero-shade" aria-hidden="true"></div>
		<div class="village-gallery__inner village-gallery__hero-content">
			<p class="village-gallery__eyebrow">Dokumentasi Desa Kubang Tangah</p>
			<h1 id="gallery-title">Galeri Kegiatan Desa</h1>
			<p>Rekam kegiatan pemerintahan, pelayanan, kolaborasi, dan kebersamaan masyarakat Desa Kubang Tangah.</p>
			<a href="#koleksi-galeri">Jelajahi Galeri <span aria-hidden="true">&darr;</span></a>
		</div>
	</section>

	<section id="koleksi-galeri" class="village-gallery__collection" data-gallery-root>
		<div class="village-gallery__inner">
			<header class="village-gallery__collection-head">
				<label class="village-gallery__year-picker">
					<span>Arsip Tahun</span>
					<span class="village-gallery__select-shell">
						<select data-gallery-year-select aria-label="Pilih tahun galeri">
							<option value="">Pilih tahun</option>
							<?php foreach ( $gallery_years as $year => $image_ids ) : ?>
								<option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
							<?php endforeach; ?>
						</select>
						<span aria-hidden="true"></span>
					</span>
				</label>
			</header>

			<div class="village-gallery__years" data-gallery-years>
				<?php foreach ( $gallery_years as $year => $image_ids ) : ?>
					<section class="village-gallery__year" data-gallery-year-section="<?php echo esc_attr( $year ); ?>" aria-labelledby="gallery-year-<?php echo esc_attr( $year ); ?>">
						<header class="village-gallery__year-head">
							<div>
								<p>Dokumentasi Tahun</p>
								<h2 id="gallery-year-<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></h2>
								<span><?php echo esc_html( number_format_i18n( count( $image_ids ) ) ); ?> foto</span>
							</div>
						</header>

						<div class="village-gallery__track" data-gallery-track tabindex="0">
							<?php foreach ( $image_ids as $image_index => $image_id ) : ?>
								<?php
								$full_image = wp_get_attachment_image_url( $image_id, 'full' );
								if ( ! $full_image ) {
									continue;
								}
								$alt_text = sprintf(
									'Dokumentasi kegiatan Desa Kubang Tangah tahun %1$s, foto %2$d',
									$year,
									$image_index + 1
								);
								?>
								<button
									class="village-gallery__photo"
									type="button"
									data-gallery-open
									data-gallery-full="<?php echo esc_url( $full_image ); ?>"
									data-gallery-alt="<?php echo esc_attr( $alt_text ); ?>"
									data-gallery-year="<?php echo esc_attr( $year ); ?>"
									data-gallery-index="<?php echo esc_attr( (string) $image_index ); ?>"
									aria-label="Buka <?php echo esc_attr( $alt_text ); ?>"
								>
									<?php
									echo wp_get_attachment_image(
										$image_id,
										'medium_large',
										false,
										[
											'alt'       => $alt_text,
											'draggable' => 'false',
											'loading'   => 'lazy',
										]
									);
									?>
									<span class="village-gallery__photo-shade" aria-hidden="true"></span>
									<span class="village-gallery__photo-year"><?php echo esc_html( $year ); ?></span>
									<span class="village-gallery__photo-action">Lihat foto <span aria-hidden="true">&rarr;</span></span>
								</button>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="village-gallery__closing" aria-label="Catatan galeri">
		<div class="village-gallery__inner">
			<p>Dokumentasi menjadi catatan perjalanan desa, dari pelayanan sehari-hari hingga kebersamaan masyarakat.</p>
		</div>
	</section>

	<dialog class="village-gallery__lightbox" data-gallery-lightbox aria-label="Pratinjau foto galeri">
		<button class="village-gallery__lightbox-backdrop" type="button" data-gallery-close aria-label="Tutup foto"></button>
		<div class="village-gallery__lightbox-panel">
			<button class="village-gallery__lightbox-close" type="button" data-gallery-close aria-label="Tutup foto">&times;</button>
			<button class="village-gallery__lightbox-nav village-gallery__lightbox-nav--prev" type="button" data-lightbox-prev aria-label="Foto sebelumnya">&larr;</button>
			<figure>
				<img alt="" data-lightbox-image>
				<figcaption>
					<strong data-lightbox-year></strong>
					<span data-lightbox-counter></span>
				</figcaption>
			</figure>
			<button class="village-gallery__lightbox-nav village-gallery__lightbox-nav--next" type="button" data-lightbox-next aria-label="Foto berikutnya">&rarr;</button>
		</div>
	</dialog>
</main>

<?php get_footer(); ?>
