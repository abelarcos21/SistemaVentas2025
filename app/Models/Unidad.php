<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unidades';

    protected $fillable = [
        'nombre',
        'abreviatura',
        'codigo_sat',
        'tipo',
        'factor_conversion',
        'unidad_base',
        'permite_decimales',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'permite_decimales' => 'boolean',
        'factor_conversion' => 'decimal:6',
    ];


    // Relación inversa (opcional, pero útil) Productos que usan esta unidad
    public function productos(){
        return $this->hasMany(Producto::class, 'unidad_id');
    }

    // SCOPES
    /**
     * Filtrar unidades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Filtrar por tipo de unidad
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Filtrar unidades que permiten decimales
     */
    public function scopePermiteDecimales($query)
    {
        return $query->where('permite_decimales', true);
    }

    /**
     * Filtrar unidades sin decimales (piezas completas)
     */
    public function scopeSinDecimales($query)
    {
        return $query->where('permite_decimales', false);
    }

    /**
     * Buscar por nombre o abreviatura
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('abreviatura', 'like', "%{$termino}%")
              ->orWhere('codigo_sat', 'like', "%{$termino}%");
        });
    }

    // MÉTODOS AUXILIARES
    /**
     * Verificar si la unidad tiene productos asociados
     */
    public function tieneProductos(): bool
    {
        return $this->productos()->exists();
    }

    /**
     * Verificar si la unidad se puede eliminar
     */
    public function puedeEliminar(): bool
    {
        return !$this->tieneProductos();
    }

    /**
     * Obtener nombre completo con abreviatura
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} ({$this->abreviatura})";
    }

    /**
     * Obtener badge de tipo HTML
     */
    public function getTipoBadgeAttribute(): string
    {
        $badges = [
            'peso' => '<span class="badge badge-primary"><i class="fas fa-weight"></i> Peso</span>',
            'volumen' => '<span class="badge badge-info"><i class="fas fa-flask"></i> Volumen</span>',
            'longitud' => '<span class="badge badge-success"><i class="fas fa-ruler"></i> Longitud</span>',
            'pieza' => '<span class="badge badge-warning"><i class="fas fa-box"></i> Pieza</span>',
            'tiempo' => '<span class="badge badge-secondary"><i class="fas fa-clock"></i> Tiempo</span>',
            'otro' => '<span class="badge badge-dark"><i class="fas fa-ellipsis-h"></i> Otro</span>',
        ];

        return $badges[$this->tipo] ?? '<span class="badge badge-secondary">N/A</span>';
    }

    /**
     * Convertir cantidad a unidad base
     */
    public function convertirABase(float $cantidad): ?float
    {
        if ($this->factor_conversion === null) {
            return null;
        }

        return $cantidad * $this->factor_conversion;
    }

    /**
     * Convertir desde unidad base
     */
    public function convertirDesdeBase(float $cantidadBase): ?float
    {
        if ($this->factor_conversion === null || $this->factor_conversion == 0) {
            return null;
        }

        return $cantidadBase / $this->factor_conversion;
    }

    /**
     * Formatear cantidad según permite_decimales
     */
    public function formatearCantidad(float $cantidad): string
    {
        if ($this->permite_decimales) {
            return number_format($cantidad, 2, '.', ',');
        }

        return number_format(floor($cantidad), 0, '.', ',');
    }

    /**
     * Validar si una cantidad es válida para esta unidad
     */
    public function validarCantidad(float $cantidad): bool
    {
        // Si no permite decimales, verificar que sea entero
        if (!$this->permite_decimales && $cantidad != floor($cantidad)) {
            return false;
        }

        // La cantidad debe ser positiva
        if ($cantidad <= 0) {
            return false;
        }

        return true;
    }

    // MÉTODOS ESTÁTICOS ÚTILES
    /**
     * Obtener unidades agrupadas por tipo
     */
    public static function obtenerPorTipo(): array
    {
        return self::activas()
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->get()
            ->groupBy('tipo')
            ->toArray();
    }

    /**
     * Obtener array para select (id => nombre completo)
     */
    public static function paraSelect(): array
    {
        return self::activas()
            ->orderBy('nombre')
            ->get()
            ->pluck('nombre_completo', 'id')
            ->toArray();
    }

    /**
     * Obtener unidades más usadas
     */
    public static function masUsadas(int $limite = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::withCount('productos')
            ->orderBy('productos_count', 'desc')
            ->limit($limite)
            ->get();
    }

    // BOOT
    protected static function boot()
    {
        parent::boot();

        // Antes de eliminar, verificar que no tenga productos
        static::deleting(function ($unidad) {
            if ($unidad->tieneProductos()) {
                throw new \Exception(
                    "No se puede eliminar la unidad '{$unidad->nombre}' porque tiene productos asociados."
                );
            }
        });
    }
}
