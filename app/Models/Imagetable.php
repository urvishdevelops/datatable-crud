<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagetable extends Model
{
    use HasFactory;

    // protected $table = "imagetable";
    protected $fillable = ['mainId', 'image'];
}
