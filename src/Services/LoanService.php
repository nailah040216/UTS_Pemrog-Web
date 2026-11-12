<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Loan;

final class LoanService
{
    public function __construct(private JsonStorage $storage, private MediaRepository $media) {}
    public function all(): array
    {
        return array_map(fn(array $r): Loan => Loan::fromArray($r), $this->storage->read('loans.json'));
    }
    public function borrow(string $userId, string $mediaId): void
    {
        $media = $this->media->find($mediaId);
        if (!$media) throw new \DomainException('Media tidak ditemukan.');
        if ($media->getStock() < 1) throw new \DomainException('Stok media sedang habis.');
        $media->setStock($media->getStock() - 1);
        $this->media->save($media);
        $now = new \DateTimeImmutable();
        $loan = new Loan(uniqid('TRX'), $userId, $mediaId, $media->getTitle(),
            $now->format('Y-m-d'), $now->modify('+7 days')->format('Y-m-d'));
        $rows = $this->storage->read('loans.json');
        $rows[] = $loan->toArray();
        $this->storage->write('loans.json', $rows);
    }
    public function returnLoan(string $loanId, string $userId, bool $isAdmin): void
    {
        $rows = $this->storage->read('loans.json');
        $found = false;
        foreach ($rows as &$row) {
            if (($row['id'] ?? '') === $loanId && ($row['status'] ?? '') === 'Dipinjam'
                && ($isAdmin || ($row['user_id'] ?? '') === $userId)) {
                $row['status'] = 'Dikembalikan';
                $media = $this->media->find((string)$row['media_id']);
                if ($media) { $media->setStock($media->getStock() + 1); $this->media->save($media); }
                $found = true; break;
            }
        }
        unset($row);
        if (!$found) throw new \DomainException('Transaksi tidak ditemukan atau sudah dikembalikan.');
        $this->storage->write('loans.json', $rows);
    }
}
