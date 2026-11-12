<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/src/bootstrap.php';

use App\Controllers\AppController;

$controller = new AppController();
$controller->handle();
