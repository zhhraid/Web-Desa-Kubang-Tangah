<?php
/**
 * The template for displaying footer.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$whatsapp_url = function_exists( 'hello_elementor_village_whatsapp_url' ) ? hello_elementor_village_whatsapp_url() : 'https://api.whatsapp.com/send?phone=6285271664112';
$footer_links  = [
	[ 'label' => 'Beranda', 'url' => home_url( '/' ) ],
	[ 'label' => 'Profil', 'url' => hello_elementor_village_page_url( 'profil-desa' ) ],
	[ 'label' => 'Pemerintahan', 'url' => hello_elementor_village_page_url( 'pemerintahan-desa' ) ],
	[ 'label' => 'Berita', 'url' => home_url( '/berita/' ) ],
	[ 'label' => 'Statistik Desa', 'url' => hello_elementor_village_page_url( 'statistik-desa' ) ],
	[ 'label' => 'Informasi', 'url' => hello_elementor_village_page_url( 'informasi-desa' ) ],
	[ 'label' => 'Galeri', 'url' => home_url( '/galeri/' ) ],
];
?>
<footer id="site-footer" class="site-footer village-footer">
	<div class="village-footer__inner">
		<section class="village-footer__brand" aria-label="<?php echo esc_attr__( 'Site information', 'hello-elementor' ); ?>">
			<a class="village-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			</a>
			<p>
				Website resmi Pemerintahan Desa Kubang Tangah untuk informasi publik, layanan masyarakat, dan kabar terbaru desa.
			</p>
			<div class="village-footer__socials" aria-label="Media sosial Desa Kubang Tangah">
				<a href="https://www.facebook.com/pemdes.kubangtangah" target="_blank" rel="noopener noreferrer">Facebook</a>
				<a href="https://www.instagram.com/desakubangtangah/?igsh=MXVqbnptaHMwNXZjZw%3D%3D#" target="_blank" rel="noopener noreferrer">Instagram</a>
				<a href="https://www.tiktok.com/@pemdes.kubang.tan" target="_blank" rel="noopener noreferrer">TikTok</a>
				<a href="https://www.youtube.com/@desakubangtangah3097" target="_blank" rel="noopener noreferrer">YouTube</a>
			</div>
		</section>

		<nav class="village-footer__links" aria-label="<?php echo esc_attr__( 'Footer menu', 'hello-elementor' ); ?>">
			<h2>Tautan Cepat</h2>
			<ul>
				<?php foreach ( $footer_links as $footer_link ) : ?>
					<li>
						<a href="<?php echo esc_url( $footer_link['url'] ); ?>"><?php echo esc_html( $footer_link['label'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<section class="village-footer__contact" aria-label="Kontak Desa Kubang Tangah">
			<h2>Kontak Desa</h2>
			<p>
				<span>Alamat</span>
				Dusun Luak Mani No.Desa, Kubang Tangah, Kec. Lembah Segar, Kota Sawahlunto, Sumatera Barat 27421
			</p>
			<p>
				<span>Email</span>
				<a href="mailto:desakubangtangah@gmail.com">desakubangtangah@gmail.com</a>
			</p>
			<p>
				<span>Layanan WhatsApp</span>
				<a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">0852-7166-4112</a>
			</p>
		</section>
	</div>

	<div class="village-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Desa Kubang Tangah. Semua hak dilindungi.</p>
	</div>
</footer>
