<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
  use SoftDeletes;

  public $timestamps = true;
  protected $table = "roles";

  public function admins(): HasMany
  {
    return $this->hasMany(Admin::class, "role_id", "id");
  }

  public function users(): HasMany
  {
    return $this->hasMany(User::class, "role_id", "id");
  }
}
