<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Agent extends Model
// {
//     use HasFactory;
//     protected $table="agent";
//     protected $primaryKey="a_id";
//     protected $fillable=["a_id","a_fist_name","a_last_name","a_phone","a_email","a_password","a_address","a_image","a_store_name","a_store_info"];
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;
    protected $table="agent";
    protected $primaryKey="a_id";
    protected $fillable=["a_id","a_fist_name","a_last_name","a_phone","a_email","a_password","a_address","a_image","a_store_name","a_store_info"];
}

