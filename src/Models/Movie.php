<?php
declare(strict_types=1);
namespace App\Models;

final class Movie extends Media
{
    public function getType(): string { return 'Film'; }
}
