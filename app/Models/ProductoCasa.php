<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCasa extends Model
{
    use HasFactory;
     protected $table='productoscasa';
    protected $primaryKey='id';
    public $timestamps = false;
}
