<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
  use SoftDeletes;
  
  public $timestamps = true;
  protected $table = "sections";

  public function images(): HasMany
  {
    return $this->hasMany(Image::class, "section_id", "id");
  }

  public function files(): HasMany
  {
    return $this->hasMany(File::class, "section_id", "id");
  }

  public function contents(): HasMany
  {
    return $this->hasMany(Content::class, "section_id", "id");
  }

  public function categories(): HasMany
  {
    return $this->hasMany(Category::class, "section_id", "id");
  }
}
