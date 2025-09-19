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


        /* 'clave_prod_serv',//datos fiscales del sat cfdi 4.0
        'clave_unidad',
        'unidad_descripcion',
        'precio_unitario',
        'tasa_o_cuota',
        'tipo_factor',
        'objeto_imp',
        'numero_identificacion', */

    ];

    protected $casts = [

        'activo' => 'boolean',
        'permite_mayoreo' => 'boolean',
        'en_oferta' => 'boolean',
        'precio_venta' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'precio_mayoreo' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
        'cantidad_minima_mayoreo' => 'integer',
        'fecha_inicio_oferta' => 'date',
        'fecha_fin_oferta' => 'date',
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
