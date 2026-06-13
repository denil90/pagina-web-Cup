<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\Seguridad\Models\Usuario;

foreach (Usuario::where('rol', 'administrador')->get() as $u) {
    echo "Email: {$u->correo} | Role: {$u->rol}\n";
}
