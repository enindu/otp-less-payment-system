<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
  use SoftDeletes;

  public $timestamps = true;
  protected $table = "subcategories";

  public function category(): HasOne
  {
    return $this->hasOne(Category::class, "id", "category_id")->withTrashed();
  }
}
