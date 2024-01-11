<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
  use SoftDeletes;

  public $timestamps = true;
  protected $table = "admins";

  public function role(): HasOne
  {
    return $this->hasOne(Role::class, "id", "role_id")->withTrashed();
  }
}
