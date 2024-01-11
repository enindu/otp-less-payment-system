<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
  use SoftDeletes;
  
  public $timestamps = true;
  protected $table = "contents";

  public function section(): HasOne
  {
    return $this->hasOne(Section::class, "id", "section_id")->withTrashed();
  }
}
