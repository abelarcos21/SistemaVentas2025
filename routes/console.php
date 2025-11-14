<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// 🔹 PROGRAMAR COMANDO DE NOTIFICACIÓN DE CADUCIDAD
Schedule::command('productos:notificar-caducidad')
    ->dailyAt('08:00')
    ->appendOutputTo(storage_path('logs/notificaciones-caducidad.log'));

// 🔹 OPCIONAL: Ejecutar cada hora en horario laboral
Schedule::command('productos:notificar-caducidad')
    ->weekdays()
    ->hourly()
    ->between('8:00', '18:00')
    ->when(function () {
        // Solo ejecutar si hay productos próximos a vencer
        return \App\Models\Producto::proximosAVencer(7)->exists();
    });

// 🔹 OPCIONAL: Desactivar automáticamente productos vencidos
Schedule::call(function () {
    $vencidos = \App\Models\Producto::vencidos()
        ->where('activo', true)
        ->get();

    foreach ($vencidos as $producto) {
        $producto->update(['activo' => false]);
        \Illuminate\Support\Facades\Log::info("Producto desactivado automáticamente: {$producto->nombre} (ID: {$producto->id})");
    }

    if ($vencidos->count() > 0) {
        \Illuminate\Support\Facades\Log::warning("Se desactivaron {$vencidos->count()} productos vencidos automáticamente.");
    }
})
->dailyAt('00:00')
->name('desactivar-productos-vencidos')
->onOneServer();
