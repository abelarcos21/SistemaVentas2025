<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;//USO ACEDER ALA RELACION

class Producto extends Model
{
    //
    use HasFactory;

    protected $table = 'productos';


    protected $fillable = [
        'user_id',
        'categoria_id',
        'proveedor_id',
        'marca_id',
        'impuesto_id',
        'codigo',
        'barcode_path',
        'nombre',
        'descripcion',
        'precio_venta',
        'activo',

        // Campos de mayoreo
        'permite_mayoreo',
        'precio_mayoreo',
        'cantidad_minima_mayoreo',

        //Campos de oferta
        'en_oferta',
        'precio_oferta',
        'fecha_inicio_oferta',
        'fecha_fin_oferta',

        //Campos de caducidad
        'requiere_fecha_caducidad',
        'fecha_caducidad',

    ];

    protected $casts = [

        'activo' => 'boolean',
        'permite_mayoreo' => 'boolean',
        'en_oferta' => 'boolean',
        'requiere_fecha_caducidad' => 'boolean', 
        'precio_venta' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'precio_mayoreo' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
        'cantidad_minima_mayoreo' => 'integer',
        'fecha_inicio_oferta' => 'date',
        'fecha_fin_oferta' => 'date',
        'fecha_caducidad' => 'date', 
    ];

    //Accesor para obtener el precio vigente
    public function getPrecioVigenteAttribute(){
        $hoy = now()->toDateString();

        if ($this->en_oferta && $this->fecha_inicio_oferta <= $hoy && $this->fecha_fin_oferta >= $hoy) {
            return $this->precio_oferta;
        }

        return $this->precio_venta;
    }

    //Método para validar si aplica mayoreo
    public function aplicaMayoreo($cantidad){
        return $this->permite_mayoreo && $cantidad >= $this->cantidad_minima_mayoreo;
    }

    //Helper para mostrar precio aplicado (base, mayoreo, oferta)
    public function getPrecioAplicadoAttribute(){
        if ($this->en_oferta && $this->precio_oferta && now()->between($this->fecha_inicio_oferta, $this->fecha_fin_oferta)) {
            return $this->precio_oferta;
        }

        if ($this->permite_mayoreo && $this->precio_mayoreo) {
            return $this->precio_mayoreo;
        }

        return $this->precio_venta;
    }



    //RELACION PARA ACCEDER ALA IMAGEN
    public function imagen(){
        return $this->hasOne(Imagen::class);
    }

    //RELACION PARA ACCEDER ALA MARCA
    public function marca(): BelongsTo{
        return $this->belongsTo(Marca::class);
    }


    public function moneda(): BelongsTo{
        return $this->belongsTo(Moneda::class, 'moneda_id', 'id');
    }

    /**
     * Verifica si el producto tiene Ventas registradas
     */
    public function tieneVentas(){
        return $this->detalleVentas()->exists();
    }

    /**
     * Verifica si el producto tiene Compras registradas
     */
    public function tieneCompras(){
        return $this->compras()->exists();
    }

    /**
     * Filtrar productos activos en las consultas
     */
    public function scopeActivos($query){
        return $query->where('activo', true);
    }

    /**
     * Filtrar productos inactivos en las consultas
     */
    public function scopeInactivos($query){
        return $query->where('activo', false);
    }

    /**
     * Scope para productos que requieren control de caducidad
    */
    public function scopeConCaducidad($query)
    {
        return $query->where('requiere_fecha_caducidad', true)
                    ->whereNotNull('fecha_caducidad');
    }

    /**
     * Scope para productos próximos a vencer
     * @param int $dias Días de anticipación para la alerta
    */
    public function scopeProximosAVencer($query, $dias = 30)
    {
        $fechaLimite = now()->addDays($dias);
        
        return $query->where('requiere_fecha_caducidad', true)
                    ->whereNotNull('fecha_caducidad')
                    ->where('fecha_caducidad', '<=', $fechaLimite)
                    ->where('fecha_caducidad', '>=', now())
                    ->where('activo', true);
    }

    /**
     * Scope para productos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('requiere_fecha_caducidad', true)
                    ->whereNotNull('fecha_caducidad')
                    ->where('fecha_caducidad', '<', now())
                    ->where('activo', true);
    }

    /**
     * Verificar si el producto está próximo a vencer
     * @param int $dias
     * @return bool
     */
    public function estaProximoAVencer($dias = 30)
    {
        if (!$this->requiere_fecha_caducidad || !$this->fecha_caducidad) {
            return false;
        }
        
        // Asegurarse de que sea un objeto Carbon
        $fecha = $this->fecha_caducidad instanceof \Carbon\Carbon 
            ? $this->fecha_caducidad 
            : \Carbon\Carbon::parse($this->fecha_caducidad);
        
        return $fecha->isFuture() && $fecha->diffInDays(now()) <= $dias;
    }

    
    /**
     * Verificar si el producto está vencido
     * @return bool
     */
    public function estaVencido()
    {
        if (!$this->requiere_fecha_caducidad || !$this->fecha_caducidad) {
            return false;
        }
        
        // Asegurarse de que sea un objeto Carbon
        $fecha = $this->fecha_caducidad instanceof \Carbon\Carbon 
            ? $this->fecha_caducidad 
            : \Carbon\Carbon::parse($this->fecha_caducidad);
        
        return $fecha->isPast();
    }

    /**
     * Obtener días restantes hasta vencer
     * @return int|null
    */
    public function diasParaVencer()
    {
        if (!$this->requiere_fecha_caducidad || !$this->fecha_caducidad) {
            return null;
        }
        
        // Asegurarse de que sea un objeto Carbon
        $fecha = $this->fecha_caducidad instanceof \Carbon\Carbon 
            ? $this->fecha_caducidad 
            : \Carbon\Carbon::parse($this->fecha_caducidad);
        
        if ($fecha->isPast()) {
            return 0;
        }
        
        return $fecha->diffInDays(now());
    }

    /**
     * Obtener clase de badge según estado de caducidad
     * @return string
     */
    public function getBadgeCaducidad()
    {
        if (!$this->requiere_fecha_caducidad) {
            return '';
        }
        
        if ($this->estaVencido()) {
            return 'badge-danger';
        }
        
        $dias = $this->diasParaVencer();
        
        if ($dias <= 7) {
            return 'badge-danger';
        } elseif ($dias <= 15) {
            return 'badge-warning';
        } elseif ($dias <= 30) {
            return 'badge-info';
        }
        
        return 'badge-success';
    }

    /**
     * Relación con detalle de ventas
     */
    public function detalleVentas(){
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    /**
     * Verifica si el código de barras se puede editar
     */
    public function codigoEsEditable(){
        return !$this->tieneVentas();
    }

    // Relaciones

    public function compras(){
        return $this->hasMany(Compra::class, 'producto_id');
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor() {
        return $this->belongsTo(Proveedor::class);
    }

}
