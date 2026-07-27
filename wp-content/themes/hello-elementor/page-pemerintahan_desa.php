<?php
/**
 * Government page for Desa Kubang Tangah.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$upload_dir  = wp_get_upload_dir();
$uploads_url = trailingslashit( $upload_dir['baseurl'] );
$january_url = $uploads_url . '2026/01/';
$february_url = $uploads_url . '2026/02/';
$regulation_url = $uploads_url . '2026/07/peraturan-desa/';

$leaders = [
	[
		'name'        => 'Rice, S.H., N.L.P.',
		'role'        => 'Kepala Desa',
		'image'       => $january_url . 'RICE-772x1024.jpg',
	],
	[
		'name'        => 'Medra Deswarmy, A.Md.',
		'role'        => 'Sekretaris Desa',
		'image'       => $january_url . 'MEDRA-DESWARMY-A.Md-Sekretaris-Desa-scaled-e1768184039649-807x1024.png',
	],
];

$staff = [
	[
		'name'  => 'Lia Rahmadeni',
		'role'  => 'Kasi Pemerintahan',
		'image' => $january_url . 'LIA-RAHMADENI-Kasi-Pemerintahan-200x300.png',
	],
	[
		'name'  => 'Nining Resque, A.Md.Keb.',
		'role'  => 'Kasi Kesejahteraan',
		'image' => $january_url . 'NINING-RESQUE-Amd.Keb-Kasi-Kesejahteraan-200x300.png',
	],
	[
		'name'  => 'Desrina',
		'role'  => 'Kasi Pelayanan',
		'image' => $january_url . 'DESRINA-Kasi-Pelayanan-200x300.png',
	],
	[
		'name'  => 'Sesti Novita Amelia, S.KM.',
		'role'  => 'Kaur TU & Umum',
		'image' => $january_url . 'SESTI-NOVITA-AMELIA-S.KM-KAUR-TUUMUM-200x300.png',
	],
	[
		'name'  => 'Novita Sari, A.Md.',
		'role'  => 'Kaur Keuangan',
		'image' => $january_url . 'NOVITA-SARIA.Md-KAUR-KEUANGAN-200x300.png',
	],
	[
		'name'  => 'Intan Sri Findo, S.Pd.',
		'role'  => 'Kaur Perencanaan',
		'image' => $january_url . 'INTAN-SRI-FINDO-S.Pd-Kaur-Perencanaan-200x300.png',
	],
	[
		'name'  => 'Yoga Alvaredotama',
		'role'  => 'P.A. Pemerintahan',
		'image' => $january_url . '2-scaled-e1768189018351-262x300.png',
	],
	[
		'name'  => 'Iskan Putansel',
		'role'  => 'P.A. Kesejahteraan',
		'image' => $january_url . 'Iskan-Putansel-200x300.png',
	],
	[
		'name'  => 'Muhammad Isra, A.Md.T.',
		'role'  => 'P.A. Pemerintahan',
		'image' => $january_url . '3-scaled-e1768189125663-262x300.png',
	],
	[
		'name'  => 'Ginsa Nabila Putri, S.Pt.',
		'role'  => 'P.A. TU & Umum',
		'image' => $january_url . 'ginsa-nabila-petri-scaled-e1768203269547-225x300.png',
	],
	[
		'name'  => 'Silva Sri Wulandari, A.Ma.',
		'role'  => 'P.A. Keuangan',
		'image' => $january_url . 'SILVA-SRI-WULANDARI-Petugas-Administrasi-Keuangan-200x300.png',
	],
	[
		'name'  => 'Supriadi',
		'role'  => 'P.A. Perencanaan',
		'image' => $january_url . '6-scaled-e1768190721523-241x300.png',
	],
];

$dusun_heads = [
	[
		'name'  => 'Zulhendra',
		'role'  => 'Kepala Dusun Batu Tajam',
		'image' => $january_url . 'ZULHENDRA-Kepala-Dusun-Batu-Tajam-scaled-e1768191210481-208x300.png',
	],
	[
		'name'  => 'Fatnalia',
		'role'  => 'Kepala Dusun Luak Mani',
		'image' => $january_url . '4-scaled-e1768192878588-251x300.png',
	],
	[
		'name'  => 'Rita Desmawanti, S.Pd.',
		'role'  => 'Kepala Dusun Polak Datar',
		'image' => $january_url . 'RITA-DESMAWANTI-Kepala-Dusun-Polak-Datar-200x300.png',
	],
	[
		'name'  => 'Avik Syahrin Tanaz',
		'role'  => 'Kepala Dusun Guguak Pauh',
		'image' => $january_url . '5-scaled-e1768192227316-255x300.png',
	],
	[
		'name'  => 'Ilwandi',
		'role'  => 'Kepala Dusun Sionsek',
		'image' => $january_url . 'ILWANDI-Kepala-Dusun-Sionsek-scaled-e1768193073473-209x300.png',
	],
];

$organization_documents = [
	[
		'title'     => 'Pemerintah Desa Kubang Tangah',
		'thumbnail' => $january_url . 'IMG_20260112_121729-300x187.jpg',
		'image'     => $january_url . 'IMG_20260112_121729-1024x637.jpg',
	],
	[
		'title'     => 'Pengelola Informasi dan Dokumentasi (PPID)',
		'thumbnail' => $january_url . 'IMG_20260112_121459_1-300x195.jpg',
		'image'     => $january_url . 'IMG_20260112_121459_1-1024x665.jpg',
	],
	[
		'title'     => 'Lembaga Pemberdayaan Masyarakat, Arsip 2017-2022',
		'thumbnail' => $january_url . 'IMG_20260112_121339_1-300x193.jpg',
		'image'     => $january_url . 'IMG_20260112_121339_1-1024x659.jpg',
	],
	[
		'title'     => 'Perlindungan Masyarakat (Linmas)',
		'thumbnail' => $january_url . 'IMG_20260112_121412_1-300x188.jpg',
		'image'     => $january_url . 'IMG_20260112_121412_1-1024x643.jpg',
	],
	[
		'title'     => 'Badan Permusyawaratan Desa (BPD)',
		'thumbnail' => $january_url . 'IMG_20260112_121319_1-300x204.jpg',
		'image'     => $january_url . 'IMG_20260112_121319_1-1024x696.jpg',
	],
	[
		'title'     => 'Karang Taruna Gema Perdana',
		'thumbnail' => $january_url . 'IMG_20260112_121404_1-300x190.jpg',
		'image'     => $january_url . 'IMG_20260112_121404_1-1024x648.jpg',
	],
	[
		'title'     => 'Desa Tangguh Bencana (Destana)',
		'thumbnail' => $january_url . 'IMG_20260112_121218_1-300x132.jpg',
		'image'     => $january_url . 'IMG_20260112_121218_1-1024x450.jpg',
	],
];

$rkpdes_documents = [
	[
		'year'  => '2024',
		'title' => 'RKPDes 2024',
		'image' => $january_url . 'info-rkpdes-2024.png',
	],
	[
		'year'  => '2025',
		'title' => 'RKPDes 2025',
		'image' => $january_url . 'info-rkpdes-2025.png',
	],
	[
		'year'  => '2026',
		'title' => 'RKPDes 2026',
		'image' => $january_url . 'rkpdes-info-2026.png',
	],
];

$apbdes_documents = [
	[
		'year'  => '2025',
		'title' => 'APBDes 2025',
		'image' => $february_url . 'APBDes-2025.png',
	],
	[
		'year'  => '2025',
		'title' => 'Perubahan APBDes 2025',
		'image' => $february_url . 'per-APBDes-2025.png',
	],
	[
		'year'  => '2025',
		'title' => 'Realisasi APBDes 2025',
		'image' => $february_url . 'realisasi-APBDes-2025.png',
	],
];

$village_regulations = [
	[
		'group' => 'Peraturan Desa Kubang Tangah Tahun 2025',
		'items' => [
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 1 Tahun 2025',
				'subtitle' => 'Logo Desa Kubang Tangah Kecamatan Lembah Segar Kota Sawahlunto',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 1 Tahun 2025 - LOGO DESA KUBANG TANGAH KECAMATAN LEMBAH SEGAR KOTA SAWAHLUNTO.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 2 Tahun 2025',
				'subtitle' => 'Perubahan atas Peraturan Desa Nomor 2 Tahun 2024 tentang Rencana Kerja Pemerintah Desa Tahun 2025',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 2 Tahun 2025 - PERUBAHAN ATAS PERATURAN DESA NOMOR 2 TAHUN 2024 TENTANG RENCANA KERJA PEMERINTAH DESA TAHUN 2025.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 3 Tahun 2025',
				'subtitle' => 'Perubahan atas Peraturan Desa Nomor 4 Tahun 2024 tentang Anggaran Pendapatan dan Belanja Desa Tahun Anggaran 2025',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 3 Tahun 2025 - PERUBAHAN ATAS PERATURAN DESA NOMOR 4 TAHUN 2024 TENTANG ANGGARAN PENDAPATAN DAN BELANJA DESA TAHUN ANGGARAN 2025.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 4 Tahun 2025',
				'subtitle' => 'Perubahan atas Peraturan Desa Nomor 1 Tahun 2020 tentang Rencana Pembangunan Jangka Menengah Desa RPJM Desa Tahun 2019 - 2027',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 4 Tahun 2025 - PERUBAHAN ATAS PERATURAN DESA NOMOR 1 TAHUN 2020 TENTANG RENCANA PEMBANGUNAN JANGKA MENENGAH DESA RPJM DESA TAHUN 2019 - 2027.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 5 Tahun 2025',
				'subtitle' => 'Rencana Kerja Pemerintahan Desa Tahun 2026',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 5 Tahun 2025 - RENCANA KERJA PEMERINTAHAN DESA TAHUN 2026.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 6 Tahun 2025',
				'subtitle' => 'Perubahan Kedua atas Peraturan Desa Nomor 4 Tahun 2024 tentang Anggaran Pendapatan dan Belanja Desa Tahun 2025',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 6 Tahun 2025 - PERUBAHAN KEDUA ATAS PERATURAN DESA NOMOR 4 TAHUN 2024 TENTANG ANGGARAN PENDAPATAN DAN BELANJA DESA TAHUN 2025.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 7 Tahun 2025',
				'subtitle' => 'Perubahan atas Peraturan Desa Nomor 5 Tahun 2017 tentang Pendirian Badan Usaha Milik Desa Usaha Maju',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 7 Tahun 2025 - PERUBAHAN ATAS PERATURAN DESA NOMOR 5 TAHUN 2017 TENTANG PENDIRIAN BADAN USAHA MILIK DESA USAHA MAJU.pdf',
			],
			[
				'title' => 'Peraturan Desa Kubang Tangah Nomor 8 Tahun 2025',
				'subtitle' => 'Penyertaan Modal Pemerintah Desa Kubang Tangah pada Badan Usaha Milik Desa Usaha Maju',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 8 Tahun 2025 - PENYERTAAN MODAL PEMERINTAH DESA KUBANG TANGAH PADA BADAN USAHA MILIK DESA USAHA MAJU.pdf',
			],
		],
	],
	[
		'group' => 'Peraturan Kepala Desa Kubang Tangah Tahun 2025',
		'items' => [
			[
				'title' => 'Peraturan Kepala Desa Kubang Tangah Nomor 1 Tahun 2025',
				'subtitle' => 'Perubahan atas Peraturan Kepala Desa Nomor 5 Tahun 2024 tentang Penjabaran Anggaran Pendapatan dan Belanja Desa Tahun Anggaran 2025',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 1 Tahun 2025 - PERUBAHAN ATAS PERATURAN KEPALA DESA NOMOR 5 TAHUN 2024 TENTANG PENJABARAN ANGGARAN PENDAPATAN DAN BELANJA DESA TAHUN ANGGARAN 2025.pdf',
			],
			[
				'title' => 'Peraturan Kepala Desa Kubang Tangah Nomor 2 Tahun 2025',
				'subtitle' => 'Perubahan Kedua atas Peraturan Kepala Desa Nomor 5 Tahun 2024 tentang Penjabaran Anggaran Pendapatan dan Belanja Desa Tahun Anggaran 2025',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 2 Tahun 2025 - PERUBAHAN KEDUA ATAS PERATURAN KEPALA DESA NOMOR 5 TAHUN 2024 TENTANG PENJABARAN ANGGARAN PENDAPATAN DAN BELANJA DESA TAHUN ANGGARAN 2025.pdf',
			],
			[
				'title' => 'Peraturan Kepala Desa Kubang Tangah Nomor 3 Tahun 2025',
				'subtitle' => 'Anggaran Rumah Tangga Badan Usaha Milik Desa Usaha Maju Kubang Tangah',
				'file' => $regulation_url . 'Peraturan Desa Kubang Tangah Nomor 3 Tahun 2025 - ANGGARAN RUMAH TANGGA BADAN USAHA MILIK DESA USAHA MAJU KUBANG TANGAH.pdf',
			],
		],
	],
];
?>

<main id="content" class="village-government">
	<section class="village-government__hero" aria-labelledby="government-title">
		<img src="<?php echo esc_url( $january_url . 'fotobersama-e1768798550506.jpg' ); ?>" alt="Aparatur Pemerintah Desa Kubang Tangah berfoto bersama" fetchpriority="high">
		<div class="village-government__hero-shade" aria-hidden="true"></div>
		<div class="village-government__inner village-government__hero-content">
			<p class="village-government__eyebrow">Pemerintahan Desa</p>
			<h1 id="government-title">Pemerintahan Desa<br>Kubang Tangah</h1>
			<p>Aparatur desa yang bekerja untuk menghadirkan pelayanan publik yang tertib, terbuka, dan dekat dengan masyarakat.</p>
			<dl class="village-government__hero-facts">
				<div>
					<dt>Aparatur Aktif</dt>
					<dd><strong data-government-count="19">19</strong> orang</dd>
				</div>
				<div>
					<dt>Wilayah Pelayanan</dt>
					<dd><strong data-government-count="5">5</strong> dusun</dd>
				</div>
			</dl>
		</div>
	</section>

	<nav class="village-government__jump-nav" aria-label="Bagian halaman pemerintahan" data-government-tabs>
		<div class="village-government__inner">
			<a class="is-active" href="#pemerintah-desa" data-government-tab role="tab" aria-selected="true" aria-controls="pemerintah-desa">Pemerintah Desa</a>
			<a href="#struktur-organisasi" data-government-tab role="tab" aria-selected="false" aria-controls="struktur-organisasi">Struktur Organisasi</a>
			<a href="#anggaran-desa" data-government-tab role="tab" aria-selected="false" aria-controls="anggaran-desa">Anggaran Desa</a>
			<a href="#peraturan-desa" data-government-tab role="tab" aria-selected="false" aria-controls="peraturan-desa">Peraturan dan Regulasi Desa</a>
		</div>
	</nav>

	<section id="pemerintah-desa" class="village-government__section village-government__apparatus" data-government-section role="tabpanel">
		<div class="village-government__inner village-government__content-layout village-government__content-layout--full">
			<div class="village-government__people-content">
				<section class="village-government__leadership" aria-labelledby="leadership-title">
					<header class="village-government__section-heading">
						<p class="village-government__kicker">Pimpinan Desa</p>
						<h2 id="leadership-title">Kepala Desa dan Sekretaris Desa</h2>
						<p>Pimpinan penyelenggaraan pemerintahan dan koordinasi administrasi Desa Kubang Tangah.</p>
					</header>

					<div class="village-government__leaders">
						<?php foreach ( $leaders as $leader ) : ?>
							<article class="village-government__leader-card">
								<figure>
									<img src="<?php echo esc_url( $leader['image'] ); ?>" alt="<?php echo esc_attr( $leader['name'] ); ?>, <?php echo esc_attr( $leader['role'] ); ?>" loading="lazy">
								</figure>
								<div>
									<span><?php echo esc_html( $leader['role'] ); ?></span>
									<h3><?php echo esc_html( $leader['name'] ); ?></h3>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<section id="perangkat-desa" class="village-government__people-section" aria-labelledby="staff-title">
					<header class="village-government__subheading">
						<div>
							<p class="village-government__kicker">Pelaksana Pemerintahan</p>
							<h2 id="staff-title">Perangkat Desa</h2>
						</div>
						<span>12 perangkat</span>
					</header>

					<div class="village-government__people-grid">
						<?php foreach ( $staff as $person ) : ?>
							<article class="village-government__person-card">
								<figure><img src="<?php echo esc_url( $person['image'] ); ?>" alt="Foto <?php echo esc_attr( $person['name'] ); ?>" loading="lazy"></figure>
								<div>
									<h3><?php echo esc_html( $person['name'] ); ?></h3>
									<p><?php echo esc_html( $person['role'] ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<section id="kepala-dusun" class="village-government__people-section" aria-labelledby="dusun-head-title">
					<header class="village-government__subheading">
						<div>
							<p class="village-government__kicker">Pelayanan Wilayah</p>
							<h2 id="dusun-head-title">Kepala Dusun</h2>
						</div>
						<span>5 dusun</span>
					</header>

					<div class="village-government__people-grid village-government__people-grid--dusun">
						<?php foreach ( $dusun_heads as $person ) : ?>
							<article class="village-government__person-card">
								<figure><img src="<?php echo esc_url( $person['image'] ); ?>" alt="Foto <?php echo esc_attr( $person['name'] ); ?>" loading="lazy"></figure>
								<div>
									<h3><?php echo esc_html( $person['name'] ); ?></h3>
									<p><?php echo esc_html( $person['role'] ); ?></p>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			</div>
		</div>
	</section>

	<section id="struktur-organisasi" class="village-government__section village-government__organization" data-government-section role="tabpanel" hidden>
		<div class="village-government__inner">
			<header class="village-government__section-heading village-government__section-heading--row">
				<div>
					<p class="village-government__kicker">Dokumen Organisasi</p>
					<h2>Struktur Organisasi Desa</h2>
					<p>Susunan pemerintahan dan lembaga kemasyarakatan yang tersimpan dalam arsip Desa Kubang Tangah.</p>
				</div>
				<div class="village-government__carousel-controls">
					<button type="button" data-organization-previous aria-label="Geser dokumen ke kiri" title="Sebelumnya">&larr;</button>
					<button type="button" data-organization-next aria-label="Geser dokumen ke kanan" title="Berikutnya">&rarr;</button>
				</div>
			</header>

			<div class="village-government__organization-track" data-organization-track>
				<?php foreach ( $organization_documents as $index => $document ) : ?>
					<article class="village-government__organization-card">
						<a
							href="<?php echo esc_url( $document['image'] ); ?>"
							data-government-preview
							data-preview-title="<?php echo esc_attr( $document['title'] ); ?>"
							target="_blank"
							rel="noopener noreferrer"
						>
							<img src="<?php echo esc_url( $document['thumbnail'] ); ?>" alt="<?php echo esc_attr( $document['title'] ); ?>" loading="lazy">
							<span aria-hidden="true">&nearr;</span>
						</a>
						<div>
							<small><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></small>
							<h3><?php echo esc_html( $document['title'] ); ?></h3>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="anggaran-desa" class="village-government__section village-government__budget" data-government-section role="tabpanel" hidden>
		<div class="village-government__inner">
			<header class="village-government__section-heading village-government__section-heading--row">
				<div>
					<p class="village-government__kicker">Transparansi Anggaran</p>
					<h2>Anggaran Desa</h2>
					<p>Dokumen perencanaan dan anggaran desa ditampilkan sebagai ringkasan visual yang dapat dibuka pada ukuran penuh.</p>
				</div>
			</header>

			<div class="village-government__budget-grid">
				<section class="village-government__budget-panel" aria-labelledby="rkpdes-title">
					<header>
						<div>
							<h3 id="rkpdes-title">Rencana Kerja Pemerintah Desa (RKPDes)</h3>
							<p>Dokumen perencanaan pembangunan desa tahun berjalan.</p>
						</div>
						<label>
							<span>Pilih tahun</span>
							<select data-budget-filter="rkpdes">
								<option value="all">Semua</option>
								<option value="2024">2024</option>
								<option value="2025">2025</option>
								<option value="2026">2026</option>
							</select>
						</label>
					</header>
					<div class="village-government__budget-list">
						<?php foreach ( $rkpdes_documents as $document ) : ?>
							<article class="village-government__budget-card" data-budget-item="rkpdes" data-budget-year="<?php echo esc_attr( $document['year'] ); ?>">
								<a
									href="<?php echo esc_url( $document['image'] ); ?>"
									data-government-preview
									data-preview-title="<?php echo esc_attr( $document['title'] ); ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<img src="<?php echo esc_url( $document['image'] ); ?>" alt="<?php echo esc_attr( $document['title'] ); ?>" loading="lazy">
								</a>
								<div>
									<span><?php echo esc_html( $document['year'] ); ?></span>
									<h4><?php echo esc_html( $document['title'] ); ?></h4>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="village-government__budget-panel" aria-labelledby="apbdes-title">
					<header>
						<div>
							<h3 id="apbdes-title">Anggaran Pendapatan dan Belanja Desa (APBDes)</h3>
							<p>Alokasi dan realisasi anggaran desa.</p>
						</div>
						<label>
							<span>Pilih tahun</span>
							<select data-budget-filter="apbdes">
								<option value="all">Semua</option>
								<option value="2025">2025</option>
							</select>
						</label>
					</header>
					<div class="village-government__budget-list">
						<?php foreach ( $apbdes_documents as $document ) : ?>
							<article class="village-government__budget-card" data-budget-item="apbdes" data-budget-year="<?php echo esc_attr( $document['year'] ); ?>">
								<a
									href="<?php echo esc_url( $document['image'] ); ?>"
									data-government-preview
									data-preview-title="<?php echo esc_attr( $document['title'] ); ?>"
									target="_blank"
									rel="noopener noreferrer"
								>
									<img src="<?php echo esc_url( $document['image'] ); ?>" alt="<?php echo esc_attr( $document['title'] ); ?>" loading="lazy">
								</a>
								<div>
									<span><?php echo esc_html( $document['year'] ); ?></span>
									<h4><?php echo esc_html( $document['title'] ); ?></h4>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			</div>
		</div>
	</section>

	<section id="peraturan-desa" class="village-government__section village-government__regulations" data-government-section role="tabpanel" hidden>
		<div class="village-government__inner">
			<header class="village-government__section-heading">
				<p class="village-government__kicker">Arsip Hukum Desa</p>
				<h2>Peraturan dan Regulasi Desa</h2>
				<p>Dokumen hukum yang berlaku di Desa Kubang Tangah. Pilih dokumen untuk melihat detail arsip yang tersedia.</p>
			</header>

			<div class="village-government__law-groups">
				<?php foreach ( $village_regulations as $group_index => $group ) : ?>
					<section class="village-government__law-group">
						<header>
							<div>
								<span><?php echo esc_html( str_pad( (string) ( $group_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<h3><?php echo esc_html( $group['group'] ); ?></h3>
							</div>
							<strong><?php echo esc_html( number_format_i18n( count( $group['items'] ) ) ); ?> dokumen</strong>
						</header>
						<div class="village-government__law-list">
							<?php foreach ( $group['items'] as $item_index => $item ) : ?>
								<article
									class="village-government__law-item"
								>
									<span><?php echo esc_html( str_pad( (string) ( $item_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<div>
										<strong><?php echo esc_html( $item['title'] ); ?></strong>
										<p><?php echo esc_html( $item['subtitle'] ); ?></p>
									</div>
									<a href="<?php echo esc_url( $item['file'] ); ?>" target="_blank" rel="noopener noreferrer">Buka PDF</a>
								</article>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<dialog class="village-government__preview-dialog" data-government-dialog aria-label="Pratinjau dokumen">
		<header>
			<div>
				<p>Pratinjau Dokumen</p>
				<h2 data-government-dialog-title>Dokumen Pemerintahan Desa</h2>
			</div>
			<button type="button" data-government-dialog-close aria-label="Tutup pratinjau" title="Tutup">&times;</button>
		</header>
		<figure><img data-government-dialog-image alt=""></figure>
		<footer>
			<p>Gunakan gambar asli untuk membaca dokumen pada ukuran penuh.</p>
			<a data-government-dialog-open href="#" target="_blank" rel="noopener noreferrer">Buka Gambar Asli</a>
		</footer>
	</dialog>
</main>

<?php
get_footer();
