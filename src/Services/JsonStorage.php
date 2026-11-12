<?php
declare(strict_types=1);
namespace App\Services;

final class JsonStorage
{
    public function __construct(private string $directory) {
        if (!is_dir($directory)) mkdir($directory, 0775, true);
    }
    public function read(string $file): array
    {
        $path = $this->path($file);
        if (!is_file($path)) return [];
        $content = file_get_contents($path);
        $data = json_decode($content ?: '[]', true);
        return is_array($data) ? $data : [];
    }
    public function write(string $file, array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false || file_put_contents($this->path($file), $json, LOCK_EX) === false) {
            throw new \RuntimeException('Gagal menyimpan data.');
        }
    }
    private function path(string $file): string
    {
        if (!in_array($file, ['users.json','media.json','loans.json'], true)) {
            throw new \InvalidArgumentException('Nama file tidak diizinkan.');
        }
        return rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
    }
}
