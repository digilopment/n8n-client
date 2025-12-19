<?php

require 'bootstrap.php';

use Digilopment\N8NClient\Services\Handler;

$handler = new Handler();
$handler->handle('generuj-obsah');
