<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaCasa extends Model
{
    use HasFactory;
    protected $table='facturacasa';
    protected $primaryKey='id';
    public $timestamps = false;
}
