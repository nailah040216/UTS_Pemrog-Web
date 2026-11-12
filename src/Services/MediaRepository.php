<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Media;

final class MediaRepository
{
    public function __construct(private JsonStorage $storage) {}
    /** @return Media[] */
    public function all(): array
    {
        return array_map(fn(array $row): Media => Media::fromArray($row),
            $this->storage->read('media.json'));
    }
    public function find(string $id): ?Media
    {
        foreach ($this->all() as $media) if ($media->getId() === $id) return $media;
        return null;
    }
    public function save(Media $media): void
    {
        $rows = $this->storage->read('media.json');
        $found = false;
        foreach ($rows as $i => $row) {
            if (($row['id'] ?? '') === $media->getId()) { $rows[$i] = $media->toArray(); $found = true; break; }
        }
        if (!$found) $rows[] = $media->toArray();
        $this->storage->write('media.json', $rows);
    }
    public function delete(string $id): void
    {
        $rows = array_values(array_filter($this->storage->read('media.json'),
            fn(array $r): bool => ($r['id'] ?? '') !== $id));
        $this->storage->write('media.json', $rows);
    }
}
