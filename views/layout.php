<?php
use App\Models\Media;

/** @var ?array $user */
/** @var Media[] $items */

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediaHub — Library Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-mark">M</span> MediaHub
    </a>

    <nav aria-label="Navigasi utama">
        <a href="index.php">Beranda</a>
        <a href="index.php?page=catalog">Katalog</a>

        <?php if ($user): ?>
            <a href="index.php?page=transactions">Transaksi</a>
        <?php endif; ?>

        <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="index.php?page=manage">Kelola Media</a>
        <?php endif; ?>
    </nav>

    <div class="nav-user">
        <?php if ($user): ?>
            <span class="user-name"><?= e($user['name']) ?></span>

            <form method="post">
                <input type="hidden" name="action" value="logout">
                <button class="btn btn-outline btn-small">Keluar</button>
            </form>
        <?php else: ?>
            <a class="btn btn-outline btn-small" href="index.php?page=login">
                Masuk
            </a>

            <a class="btn btn-primary btn-small" href="index.php?page=register">
                Daftar
            </a>
        <?php endif; ?>
    </div>
</header>

<main class="container">

<?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>" role="status">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>


<?php if ($page === 'login' || $page === 'register'): ?>

    <section class="auth-wrap">
        <div class="auth-card">

            <span class="eyebrow">SELAMAT DATANG DI MEDIAHUB</span>

            <h1>
                <?= $page === 'login' ? 'Masuk ke akunmu' : 'Buat akun baru' ?>
            </h1>

            <p class="muted">
                Kelola koleksi dan transaksi media dalam satu tempat.
            </p>

            <form method="post" class="form-grid" data-validate>

                <input type="hidden" name="action" value="<?= $page ?>">

                <?php if ($page === 'register'): ?>
                    <label>
                        Nama lengkap
                        <input name="name" required minlength="3"
                            placeholder="Nama kamu">
                    </label>
                <?php endif; ?>

                <label>
                    Email
                    <input type="email" name="email" required
                        placeholder="nama@email.com">
                </label>

                <label>
                    Password
                    <input type="password" name="password" required
                        minlength="<?= $page === 'register' ? '6' : '1' ?>"
                        placeholder="Masukkan password">
                </label>

                <button class="btn btn-primary btn-full">
                    <?= $page === 'login' ? 'Masuk' : 'Daftar sekarang' ?>
                </button>

            </form>

            <p class="muted center">
                <?php if ($page === 'login'): ?>
                    Belum punya akun?
                    <a href="index.php?page=register">Daftar</a>
                <?php else: ?>
                    Sudah punya akun?
                    <a href="index.php?page=login">Masuk</a>
                <?php endif; ?>
            </p>

            <?php if ($page === 'login'): ?>
                <div class="demo-box">
                    <b>Akun demo</b><br>
                    Admin: admin@mediahub.test / admin123<br>
                    Member: member@mediahub.test / member123
                </div>
            <?php endif; ?>

        </div>
    </section>


<?php elseif ($page === 'catalog' || $page === 'home'): ?>

    <?php if ($page === 'home'): ?>

        <section class="hero">

            <div class="hero-copy">
                <span class="eyebrow">MEDIA & LIBRARY MANAGEMENT</span>

                <h1>Semua koleksi,<br><em>satu ruang.</em></h1>

                <p>
                    Temukan buku dan film favoritmu, lalu lakukan
                    peminjaman dengan proses yang sederhana.
                </p>

                <div class="hero-actions">
                    <a class="btn btn-primary" href="index.php?page=catalog">
                        Jelajahi katalog →
                    </a>

                    <?php if (!$user): ?>
                        <a class="btn btn-quiet"
                            href="index.php?page=register">
                            Buat akun
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hero-art" aria-label="Ilustrasi tumpukan buku">
                <div class="sun"></div>
                <div class="book book-one">READ<br>MORE</div>
                <div class="book book-two">IDEAS</div>
                <div class="book book-three">CREATE</div>
                <div class="plant">✳</div>
            </div>

        </section>

        <section class="stats">
            <article>
                <span>Total koleksi</span>
                <strong><?= $stats['media'] ?></strong>
                <small>Judul tersedia</small>
            </article>

            <article>
                <span>Stok tersedia</span>
                <strong><?= $stats['stock'] ?></strong>
                <small>Item siap dipinjam</small>
            </article>

            <article>
                <span>Transaksi aktif</span>
                <strong><?= $stats['loans'] ?></strong>
                <small>Sedang dipinjam</small>
            </article>
        </section>

    <?php endif; ?>


    <section class="section-head">
        <div>
            <span class="eyebrow">JELAJAHI</span>

            <h2>
                <?= $page === 'home' ? 'Koleksi pilihan' : 'Katalog media' ?>
            </h2>

            <p class="muted">
                Cari koleksi berdasarkan judul, kreator, atau kategori.
            </p>
        </div>

        <?php if ($page === 'home'): ?>
            <a class="text-link" href="index.php?page=catalog">
                Lihat semua →
            </a>
        <?php endif; ?>
    </section>


    <form class="filter-bar" method="get">

        <input type="hidden" name="page" value="catalog">

        <label class="searchbox">
            <span>⌕</span>

            <input
                type="search"
                name="q"
                value="<?= e($_GET['q'] ?? '') ?>"
                placeholder="Cari judul atau kreator..."
            >
        </label>

        <select name="type" aria-label="Filter jenis media">
            <option value="Semua" <?= $type === 'Semua' ? 'selected' : '' ?>>
                Semua
            </option>

            <option value="Buku" <?= $type === 'Buku' ? 'selected' : '' ?>>
                Buku
            </option>

            <option value="Film" <?= $type === 'Film' ? 'selected' : '' ?>>
                Film
            </option>
        </select>

        <button class="btn btn-dark" type="submit">Cari</button>

    </form>


    <div class="media-grid">

        <?php
        $displayItems = $page === 'home'
            ? array_slice($items, 0, 4)
            : $items;
        ?>

        <?php foreach ($displayItems as $m): ?>

            <article class="media-card">

                <div class="cover <?= $m->getType() === 'Film' ? 'cover-film' : '' ?>">

                    <?php if ($m->getImageUrl() !== ''): ?>

                        <img
                            src="<?= e($m->getImageUrl()) ?>"
                            alt="Sampul <?= e($m->getTitle()) ?>"
                            loading="lazy"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                        >

                    <?php endif; ?>

                    <div class="cover-fallback"
                        <?= $m->getImageUrl() !== '' ? 'style="display:none"' : '' ?>>

                        <strong>
                            <?= e(strtoupper(substr($m->getTitle(), 0, 1))) ?>
                        </strong>

                        <small><?= e($m->getCategory()) ?></small>
                    </div>

                    <span><?= e($m->getType()) ?></span>

                </div>


                <div class="media-info">

                    <div class="card-top">
                        <span class="tag"><?= e($m->getType()) ?></span>

                        <span class="stock">
                            <?= $m->getStock() > 0
                                ? 'Stok ' . $m->getStock()
                                : 'Habis' ?>
                        </span>
                    </div>

                    <h3><?= e($m->getTitle()) ?></h3>

                    <p><?= e($m->getCreator()) ?></p>

                    <?php if ($m->getDescription() !== ''): ?>
                        <p class="media-description">
                            <?= e($m->getDescription()) ?>
                        </p>
                    <?php endif; ?>

                    <div class="card-bottom">

                        <?php if ($user && $m->getStock() > 0): ?>

                            <form method="post"
                                onsubmit="return confirm('Pinjam media ini?')">

                                <input type="hidden" name="action" value="borrow">

                                <input type="hidden" name="media_id"
                                    value="<?= e($m->getId()) ?>">

                                <button class="btn btn-primary btn-small">
                                    Pinjam
                                </button>

                            </form>

                        <?php elseif (!$user): ?>

                            <a class="btn btn-outline btn-small"
                                href="index.php?page=login">
                                Login untuk pinjam
                            </a>

                        <?php else: ?>

                            <button class="btn btn-disabled btn-small" disabled>
                                Stok habis
                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            </article>

        <?php endforeach; ?>


        <?php if (!$displayItems): ?>
            <p class="empty">
                Tidak ada media yang cocok dengan pencarian.
            </p>
        <?php endif; ?>

    </div>


<?php elseif ($page === 'manage' && ($user['role'] ?? '') === 'admin'): ?>

    <section class="section-head">
        <div>
            <span class="eyebrow">ADMINISTRATOR</span>
            <h2>Kelola katalog</h2>
            <p class="muted">
                Tambahkan, edit, dan hapus koleksi media.
            </p>
        </div>
    </section>


    <div class="manage-layout">

        <form method="post" class="panel form-grid" data-validate>

            <h3>Tambah media</h3>

            <input type="hidden" name="action" value="save_media">

            <label>
                Judul
                <input name="title" required minlength="2">
            </label>

            <label>
                Penulis / Kreator
                <input name="creator" required>
            </label>

            <div class="two-col">

                <label>
                    Jenis
                    <select name="type">
                        <option value="Buku">Buku</option>
                        <option value="Film">Film</option>
                    </select>
                </label>

                <label>
                    Kategori
                    <input name="category" required>
                </label>

            </div>

            <label>
                Stok
                <input type="number" name="stock" min="0" value="1" required>
            </label>

            <label>
                Deskripsi
                <textarea name="description" rows="3"></textarea>
            </label>

            <label>
                URL Gambar Sampul / Poster
                <input
                    type="url"
                    name="image_url"
                    placeholder="https://alamat-gambar.com/poster.jpg"
                >
            </label>

            <button class="btn btn-primary">Simpan media</button>

        </form>


        <div class="panel">

            <h3>Daftar media</h3>

            <?php foreach ($this->media->all() as $m): ?>

                <div class="manage-edit-form">

                    <div class="manage-row">

                        <div>
                            <b><?= e($m->getTitle()) ?></b>

                            <small>
                                <?= e($m->getType()) ?> · Stok
                                <?= e((string)$m->getStock()) ?>
                            </small>
                        </div>

                    </div>


                    <form method="post" class="form-grid" data-validate>

                        <input type="hidden" name="action" value="save_media">

                        <input type="hidden" name="id"
                            value="<?= e($m->getId()) ?>">

                        <label>
                            Judul
                            <input name="title"
                                value="<?= e($m->getTitle()) ?>" required>
                        </label>

                        <label>
                            Penulis / Kreator
                            <input name="creator"
                                value="<?= e($m->getCreator()) ?>" required>
                        </label>

                        <div class="two-col">

                            <label>
                                Jenis
                                <select name="type">
                                    <option value="Buku"
                                        <?= $m->getType() === 'Buku' ? 'selected' : '' ?>>
                                        Buku
                                    </option>

                                    <option value="Film"
                                        <?= $m->getType() === 'Film' ? 'selected' : '' ?>>
                                        Film
                                    </option>
                                </select>
                            </label>

                            <label>
                                Kategori
                                <input name="category"
                                    value="<?= e($m->getCategory()) ?>" required>
                            </label>

                        </div>

                        <label>
                            Stok
                            <input type="number" name="stock"
                                value="<?= e((string)$m->getStock()) ?>"
                                min="0" required>
                        </label>

                        <label>
                            Deskripsi
                            <textarea name="description" rows="2"><?= e($m->getDescription()) ?></textarea>
                        </label>

                        <label>
                            URL Gambar Sampul / Poster
                            <input
                                type="url"
                                name="image_url"
                                value="<?= e($m->getImageUrl()) ?>"
                                placeholder="https://alamat-gambar.com/poster.jpg"
                            >
                        </label>

                        <button type="submit" class="btn btn-primary btn-small">
                            Simpan Perubahan
                        </button>

                    </form>


                    <form method="post"
                        onsubmit="return confirm('Yakin ingin menghapus media ini?')">

                        <input type="hidden" name="action" value="delete_media">

                        <input type="hidden" name="id"
                            value="<?= e($m->getId()) ?>">

                        <button type="submit" class="btn btn-danger btn-small">
                            Hapus Media
                        </button>

                    </form>

                </div>

                <div class="edit-divider"></div>

            <?php endforeach; ?>

        </div>

    </div>


<?php elseif ($page === 'transactions' && $user): ?>

    <section class="section-head">
        <div>
            <span class="eyebrow">RIWAYAT</span>
            <h2>Transaksi peminjaman</h2>

            <p class="muted">
                <?= $user['role'] === 'admin'
                    ? 'Semua transaksi anggota.'
                    : 'Daftar media yang kamu pinjam.' ?>
            </p>
        </div>
    </section>


    <div class="panel table-wrap">

        <table>

            <thead>
                <tr>
                    <th>Media</th>
                    <th>Tanggal pinjam</th>
                    <th>Batas kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($myLoans as $loan): $d = $loan->toArray(); ?>

                    <tr>

                        <td><b><?= e($d['media_title']) ?></b></td>
                        <td><?= e($d['borrowed_at']) ?></td>
                        <td><?= e($d['due_at']) ?></td>

                        <td>
                            <span class="status <?= $loan->getStatus() === 'Dipinjam' ? 'status-active' : 'status-done' ?>">
                                <?= e($loan->getStatus()) ?>
                            </span>
                        </td>

                        <td>

                            <?php if ($loan->getStatus() === 'Dipinjam'): ?>

                                <form method="post">

                                    <input type="hidden" name="action" value="return">

                                    <input type="hidden" name="loan_id"
                                        value="<?= e($loan->getId()) ?>">

                                    <button class="btn btn-outline btn-small">
                                        Kembalikan
                                    </button>

                                </form>

                            <?php else: ?>
                                —
                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if (!$myLoans): ?>
                    <tr>
                        <td colspan="5" class="empty">
                            Belum ada transaksi.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>


<?php else: ?>

    <section class="empty-state">

        <h2>Halaman tidak ditemukan</h2>

        <p>
            Silakan kembali ke beranda atau masuk menggunakan akun yang sesuai.
        </p>

        <a class="btn btn-primary" href="index.php">
            Kembali ke beranda
        </a>

    </section>

<?php endif; ?>

</main>


<footer>
    <span class="brand">
        <span class="brand-mark">M</span> MediaHub
    </span>

    <p>Prototype UTS Pemrograman Web · PHP OOP</p>
</footer>

<script src="assets/js/app.js"></script>

</body>
</html>