<?php
declare(strict_types=1);
namespace App\Models;

final class Admin extends User
{
    public function getRole(): string { return 'admin'; }
}
