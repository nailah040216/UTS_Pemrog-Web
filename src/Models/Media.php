<?php
declare(strict_types=1);

namespace App\Models;

abstract class Media
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected string $creator,
        protected string $category,
        protected int $stock,
        protected string $description = '',
        protected string $imageUrl = ''
    ) {}

    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getCreator(): string { return $this->creator; }
    public function getCategory(): string { return $this->category; }
    public function getStock(): int { return $this->stock; }
    public function getDescription(): string { return $this->description; }
    public function getImageUrl(): string { return $this->imageUrl; }

    public function setStock(int $stock): void
    {
        $this->stock = max(0, $stock);
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'creator' => $this->creator,
            'category' => $this->category,
            'stock' => $this->stock,
            'description' => $this->description,
            'type' => $this->getType(),
            'image_url' => $this->imageUrl
        ];
    }

    public static function fromArray(array $data): Media
    {
        $class = ($data['type'] ?? 'Buku') === 'Film'
            ? Movie::class
            : Book::class;

        return new $class(
            (string) $data['id'],
            (string) $data['title'],
            (string) $data['creator'],
            (string) $data['category'],
            (int) $data['stock'],
            (string) ($data['description'] ?? ''),
            (string) ($data['image_url'] ?? '')
        );
    }
}