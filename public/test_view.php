<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
file_put_contents(__DIR__.'/test_output.html', view('prueba1mario', ['tiempo_transcurrido' => 105])->render());
echo "OK";
