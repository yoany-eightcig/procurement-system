<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parts extends Model
{
    // The attributes that are mass assignable.
    // protected $fillable = ['sku', 'name', 'quantity'];
    // protected $fillable = ['unissued'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
