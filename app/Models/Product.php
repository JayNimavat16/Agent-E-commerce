<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table="product";
    protected $primaryKey="id";
    protected $fillable=["id","ca_id","name","des","price","stock","image"];
}
