<?php namespace Modules\Shop\Entities;
   
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model {

    use SoftDeletes;
    protected $table = 'facturas';
    protected $fillable = [
        'user_id',
        'order_id',
        'estado',
        'fecha_vencimiento',
        'fecha_pago',
        'importe',
        'importe_abonado',
        'importe_pendiente'
    ];

}