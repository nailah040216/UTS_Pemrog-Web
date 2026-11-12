<?php
declare(strict_types=1);
namespace App\Models;

final class Book extends Media
{
    public function getType(): string { return 'Buku'; }
}
