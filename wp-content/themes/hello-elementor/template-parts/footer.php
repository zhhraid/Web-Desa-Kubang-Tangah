<?php
/**
 * The template for displaying footer.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$footer_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-2',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
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
			<?php if ( $footer_nav_menu ) : ?>
				<?php
				// PHPCS - escaped by WordPress with "wp_nav_menu".
				echo $footer_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			<?php else : ?>
				<ul>
					<?php
					wp_list_pages( [
						'title_li' => '',
						'include' => '1370,5,6,1546,1514,7,8',
					] );
					?>
				</ul>
			<?php endif; ?>
		</nav>

		<section class="village-footer__contact" aria-label="Kontak Desa Kubang Tangah">
			<h2>Kontak</h2>
			<p>
				<span>Alamat</span>
				Dusun Luak Mani No.Desa, Kubang Tangah, Kec. Lembah Segar, Kota Sawahlunto, Sumatera Barat 27421
			</p>
			<p>
				<span>Email</span>
				<a href="mailto:desakubangtangah@gmail.com">desakubangtangah@gmail.com</a>
			</p>
			<p>
				<span>Nomor Kontak</span>
				<a href="tel:+6285271664112">0852-7166-4112</a>
			</p>
		</section>
	</div>

	<div class="village-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Desa Kubang Tangah. Semua hak dilindungi.</p>
	</div>
</footer>
