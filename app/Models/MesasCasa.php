<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesasCasa extends Model
{
    use HasFactory;
     protected $table='pager';
    protected $primaryKey='id';
    public $timestamps = false;
}
