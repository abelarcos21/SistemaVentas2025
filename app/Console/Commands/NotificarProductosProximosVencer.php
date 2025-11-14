<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class NotificarProductosProximosVencer extends Command
{
    protected $signature = 'productos:notificar-caducidad';
    protected $description = 'Notifica productos próximos a vencer';

    public function handle()
    {
        $this->info('Verificando productos próximos a vencer...');

        // Productos próximos a vencer en 7 días
        $urgentes = Producto::proximosAVencer(7)->count();

        // Productos vencidos
        $vencidos = Producto::vencidos()->count();

        if ($urgentes > 0 || $vencidos > 0) {
            $this->warn("⚠️  ALERTA:");
            $this->warn("   - {$urgentes} productos vencen en 7 días");
            $this->warn("   - {$vencidos} productos ya vencidos");

            // Aquí puedes enviar notificaciones por email, Slack, etc.
            Log::warning("Productos por vencer: {$urgentes} | Vencidos: {$vencidos}");
        } else {
            $this->info('✓ No hay productos en alerta de caducidad');
        }

        return 0;
    }
}
