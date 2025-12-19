<?php

require 'bootstrap.php';

use App\Services\Handler;

$handler = new Handler();
$handler->handle('generuj-obsah');
