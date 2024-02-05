<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagetable extends Model
{
    use HasFactory;

    protected $table = "imagetables";
    protected $fillable = ['mainId', 'image'];
}
