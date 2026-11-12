<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Book;
use App\Models\Movie;
use App\Services\AuthService;
use App\Services\JsonStorage;
use App\Services\LoanService;
use App\Services\MediaRepository;

final class AppController
{
    private JsonStorage $storage;
    private MediaRepository $media;
    private AuthService $auth;
    private LoanService $loans;

    public function __construct()
    {
        $this->storage = new JsonStorage(DATA_PATH);
        $this->seed();

        $this->media = new MediaRepository($this->storage);
        $this->auth = new AuthService($this->storage);
        $this->loans = new LoanService($this->storage, $this->media);
    }

    public function handle(): void
    {
        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        try {
            switch ($action) {
                case 'login':
                    $user = $this->auth->login(
                        $this->post('email'),
                        $this->post('password')
                    );

                    if (!$user) {
                        throw new \DomainException('Email atau password salah.');
                    }

                    session_regenerate_id(true);
                    $_SESSION['user'] = $user->toArray();
                    $this->redirect();

                case 'register':
                    $user = $this->auth->register(
                        $this->post('name'),
                        $this->post('email'),
                        $this->post('password')
                    );

                    $_SESSION['user'] = $user->toArray();
                    $this->redirect();

                case 'logout':
                    $_SESSION = [];
                    session_destroy();
                    header('Location: index.php');
                    exit;

                case 'save_media':
                    $this->requireAdmin();

                    $id = trim($_POST['id'] ?? '') ?: uniqid('MED');
                    $type = $this->post('type');

                    if (!in_array($type, ['Buku', 'Film'], true)) {
                        throw new \DomainException('Jenis media tidak valid.');
                    }

                    $class = $type === 'Film' ? Movie::class : Book::class;

                    $stock = max(0, (int)($_POST['stock'] ?? 0));
                    $imageUrl = trim($_POST['image_url'] ?? '');

                    if ($imageUrl !== '' &&
                        !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        throw new \DomainException('URL gambar tidak valid.');
                    }

                    $item = new $class(
                        $id,
                        $this->post('title'),
                        $this->post('creator'),
                        $this->post('category'),
                        $stock,
                        trim($_POST['description'] ?? ''),
                        $imageUrl
                    );

                    $this->media->save($item);

                    $this->flash('Media berhasil disimpan.');
                    $this->redirect('catalog');

                case 'delete_media':
                    $this->requireAdmin();

                    $id = $this->post('id');

                    foreach ($this->loans->all() as $loan) {
                        if (
                            $loan->getMediaId() === $id &&
                            $loan->getStatus() === 'Dipinjam'
                        ) {
                            throw new \DomainException(
                                'Media masih memiliki transaksi aktif.'
                            );
                        }
                    }

                    $this->media->delete($id);

                    $this->flash('Media berhasil dihapus.');
                    $this->redirect('catalog');

                case 'borrow':
                    $user = $this->requireLogin();

                    $this->loans->borrow(
                        $user['id'],
                        $this->post('media_id')
                    );

                    $this->flash(
                        'Peminjaman berhasil. Batas pengembalian 7 hari.'
                    );

                    $this->redirect('transactions');

                case 'return':
                    $user = $this->requireLogin();

                    $this->loans->returnLoan(
                        $this->post('loan_id'),
                        $user['id'],
                        $user['role'] === 'admin'
                    );

                    $this->flash('Media berhasil dikembalikan.');
                    $this->redirect('transactions');
            }
        } catch (\Throwable $e) {
            $this->flash($e->getMessage(), 'error');
            $this->redirect($_GET['page'] ?? 'home');
        }

        $this->render();
    }

    private function seed(): void
    {
        if (!$this->storage->read('users.json')) {
            $this->storage->write('users.json', [
                [
                    'id' => 'USR001',
                    'name' => 'Administrator',
                    'email' => 'admin@mediahub.test',
                    'password' => password_hash('admin123', PASSWORD_DEFAULT),
                    'role' => 'admin'
                ],
                [
                    'id' => 'USR002',
                    'name' => 'Cindy Member',
                    'email' => 'member@mediahub.test',
                    'password' => password_hash('member123', PASSWORD_DEFAULT),
                    'role' => 'member'
                ]
            ]);
        }

        if (!$this->storage->read('media.json')) {
            $this->storage->write('media.json', [
                [
                    'id' => 'MED001',
                    'title' => 'Laskar Pelangi',
                    'creator' => 'Andrea Hirata',
                    'category' => 'Novel',
                    'stock' => 5,
                    'description' => 'Novel tentang pendidikan dan perjuangan.',
                    'type' => 'Buku',
                    'image_url' => 'https://covers.openlibrary.org/b/isbn/9789793062792-L.jpg'
                ],
                [
                    'id' => 'MED002',
                    'title' => 'Atomic Habits',
                    'creator' => 'James Clear',
                    'category' => 'Pengembangan Diri',
                    'stock' => 3,
                    'description' => 'Panduan membangun kebiasaan baik.',
                    'type' => 'Buku',
                    'image_url' => 'https://covers.openlibrary.org/b/isbn/9780735211292-L.jpg'
                ],
                [
                    'id' => 'MED003',
                    'title' => 'Clean Code',
                    'creator' => 'Robert C. Martin',
                    'category' => 'Teknologi',
                    'stock' => 2,
                    'description' => 'Prinsip menulis kode yang mudah dirawat.',
                    'type' => 'Buku',
                    'image_url' => 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg'
                ],
                [
                    'id' => 'MED004',
                    'title' => 'The Hobbit',
                    'creator' => 'J.R.R. Tolkien',
                    'category' => 'Fantasi',
                    'stock' => 4,
                    'description' => 'Petualangan Bilbo Baggins.',
                    'type' => 'Buku',
                    'image_url' => 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg'
                ],
                [
                    'id' => 'MED005',
                    'title' => 'The Alchemist',
                    'creator' => 'Paulo Coelho',
                    'category' => 'Fiksi',
                    'stock' => 3,
                    'description' => 'Kisah perjalanan mencari makna hidup.',
                    'type' => 'Buku',
                    'image_url' => 'https://covers.openlibrary.org/b/isbn/9780062315007-L.jpg'
                ],
                [
                    'id' => 'MED006',
                    'title' => 'The Social Network',
                    'creator' => 'David Fincher',
                    'category' => 'Drama',
                    'stock' => 4,
                    'description' => 'Film tentang perjalanan membangun Facebook.',
                    'type' => 'Film',
                    'image_url' => 'https://image.tmdb.org/t/p/w500/n0ybibhJtQ5icDqTp8eRytcIHJx.jpg'
                ],
                [
                    'id' => 'MED007',
                    'title' => 'Inception',
                    'creator' => 'Christopher Nolan',
                    'category' => 'Sci-Fi',
                    'stock' => 5,
                    'description' => 'Pencurian informasi melalui dunia mimpi.',
                    'type' => 'Film',
                    'image_url' => 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg'
                ],
                [
                    'id' => 'MED008',
                    'title' => 'Interstellar',
                    'creator' => 'Christopher Nolan',
                    'category' => 'Sci-Fi',
                    'stock' => 3,
                    'description' => 'Perjalanan mencari tempat tinggal baru bagi manusia.',
                    'type' => 'Film',
                    'image_url' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MPost.jpg'
                ],
                [
                    'id' => 'MED009',
                    'title' => 'The Dark Knight',
                    'creator' => 'Christopher Nolan',
                    'category' => 'Action',
                    'stock' => 4,
                    'description' => 'Batman menghadapi ancaman Joker.',
                    'type' => 'Film',
                    'image_url' => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg'
                ],
                [
                    'id' => 'MED010',
                    'title' => 'Parasite',
                    'creator' => 'Bong Joon-ho',
                    'category' => 'Thriller',
                    'stock' => 2,
                    'description' => 'Kisah dua keluarga dengan latar sosial berbeda.',
                    'type' => 'Film',
                    'image_url' => 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg'
                ]
            ]);
        }

        if (!$this->storage->read('loans.json')) {
            $this->storage->write('loans.json', []);
        }
    }

    private function render(): void
    {
        $user = $_SESSION['user'] ?? null;
        $page = $_GET['page'] ?? 'home';

        $allItems = $this->media->all();
        $items = $allItems;
        $loans = $this->loans->all();

        $q = strtolower(trim($_GET['q'] ?? ''));
        $type = $_GET['type'] ?? 'Semua';

        if (!in_array($type, ['Semua', 'Buku', 'Film'], true)) {
            $type = 'Semua';
        }

        $items = array_values(array_filter(
            $items,
            fn($m) =>
                (!$q || str_contains(
                    strtolower(
                        $m->getTitle() . ' ' .
                        $m->getCreator() . ' ' .
                        $m->getCategory()
                    ),
                    $q
                )) &&
                ($type === 'Semua' || $m->getType() === $type)
        ));

        $myLoans = array_values(array_filter(
            $loans,
            fn($l) =>
                $user &&
                (
                    $user['role'] === 'admin' ||
                    $l->getUserId() === $user['id']
                )
        ));

        $stats = [
            'media' => count($allItems),
            'stock' => array_sum(
                array_map(fn($m) => $m->getStock(), $allItems)
            ),
            'loans' => count(array_filter(
                $loans,
                fn($l) => $l->getStatus() === 'Dipinjam'
            ))
        ];

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require dirname(__DIR__, 2) . '/views/layout.php';
    }

    private function post(string $key): string
    {
        return trim((string)($_POST[$key] ?? ''));
    }

    private function requireLogin(): array
    {
        if (empty($_SESSION['user'])) {
            throw new \DomainException(
                'Silakan login terlebih dahulu.'
            );
        }

        return $_SESSION['user'];
    }

    private function requireAdmin(): array
    {
        $user = $this->requireLogin();

        if ($user['role'] !== 'admin') {
            throw new \DomainException(
                'Akses khusus administrator.'
            );
        }

        return $user;
    }

    private function flash(
        string $message,
        string $type = 'success'
    ): void {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    private function redirect(string $page = 'home'): never
    {
        header('Location: index.php?page=' . urlencode($page));
        exit;
    }
}