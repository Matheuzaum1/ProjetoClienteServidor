<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Projeto Cliente/Servidor EP1');
})->purpose('Display an inspiring message');