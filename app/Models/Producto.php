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
        /* 'clave_prod_serv',//datos fiscales del sat cfdi 4.0
        'clave_unidad',
        'unidad_descripcion',
        'precio_unitario',
        'tasa_o_cuota',
        'tipo_factor',
        'objeto_imp',
        'numero_identificacion', */

    ];

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
