<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lociones extends Model
{
    use HasFactory;
    protected $table='lociones';
    protected $primaryKey='id_productos';
    public $timestamps = false;
}
