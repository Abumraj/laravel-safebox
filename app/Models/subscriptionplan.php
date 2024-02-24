<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subscriptionplan extends Model
{
    use HasFactory;

  protected $fillable = ['name', 'price','storage', 'ads', 'refferal', 'color', 'code' ];

  public function users()
  {
      return $this->hasMany(User::class)->withCount('totalUser');
  }
  public function totalUser()
  {
      return $this->hasMany(User::class);
  }

}
