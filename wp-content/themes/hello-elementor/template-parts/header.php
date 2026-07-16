<?php
/**
 * The template for displaying header.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$site_name = get_bloginfo( 'name' );
$logo_url  = content_url( 'uploads/2026/01/logokt-100x100.png' );
?>

<header id="site-header" class="site-header village-header">
	<div class="village-header__inner">
		<a class="village-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $site_name ); ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="" width="52" height="52">
			<span>
				<strong><?php echo esc_html( $site_name ); ?></strong>
				<small>Website Resmi Pemerintahan Desa</small>
			</span>
		</a>

		<input class="village-header__toggle" type="checkbox" id="village-header-menu-toggle" aria-label="Buka menu">
		<label class="village-header__toggle-button" for="village-header-menu-toggle" aria-hidden="true">
			<span></span>
		</label>

		<nav class="village-header__nav" aria-label="<?php echo esc_attr__( 'Main menu', 'hello-elementor' ); ?>">
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Beranda</a></li>
				<li><a href="<?php echo esc_url( home_url( '/profil_desa/' ) ); ?>">Profil</a></li>
				<li><a href="<?php echo esc_url( home_url( '/pemerintahan_desa/' ) ); ?>">Pemerintahan</a></li>
				<li><a href="<?php echo esc_url( home_url( '/data/' ) ); ?>">Informasi</a></li>
				<li><a href="<?php echo esc_url( home_url( '/galeri/' ) ); ?>">Galeri</a></li>
				<li><a class="village-header__nav-cta" href="<?php echo esc_url( home_url( '/kontak/' ) ); ?>">Kontak</a></li>
			</ul>
		</nav>
	</div>
</header>
