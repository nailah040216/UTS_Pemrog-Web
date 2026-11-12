<?php
declare(strict_types=1);
namespace App\Models;

final class Loan
{
    public function __construct(
        private string $id,
        private string $userId,
        private string $mediaId,
        private string $mediaTitle,
        private string $borrowedAt,
        private string $dueAt,
        private string $status = 'Dipinjam'
    ) {}
    public function getId(): string { return $this->id; }
    public function getUserId(): string { return $this->userId; }
    public function getMediaId(): string { return $this->mediaId; }
    public function getStatus(): string { return $this->status; }
    public function returnItem(): void { $this->status = 'Dikembalikan'; }
    public function toArray(): array
    {
        return ['id'=>$this->id, 'user_id'=>$this->userId, 'media_id'=>$this->mediaId,
            'media_title'=>$this->mediaTitle, 'borrowed_at'=>$this->borrowedAt,
            'due_at'=>$this->dueAt, 'status'=>$this->status];
    }
    public static function fromArray(array $d): Loan
    {
        return new self((string)$d['id'], (string)$d['user_id'], (string)$d['media_id'],
            (string)$d['media_title'], (string)$d['borrowed_at'], (string)$d['due_at'],
            (string)$d['status']);
    }
}
