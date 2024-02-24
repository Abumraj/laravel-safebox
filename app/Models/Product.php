<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status', 'product_id', 'created_by'];


    public function files()
    {
        return $this->hasMany(File::class);
    }
}
