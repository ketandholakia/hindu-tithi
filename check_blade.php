<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$blade = app('view')->getEngineResolver()->resolve('blade')->getCompiler();
echo $blade->compileString(file_get_contents('resources/views/layouts/app.blade.php'));
