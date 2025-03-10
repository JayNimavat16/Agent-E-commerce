<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table="customer";
    protected $primaryKey="c_id";
    protected $fillable=["c_id","c_username","c_phone","c_email","c_password"];
}
