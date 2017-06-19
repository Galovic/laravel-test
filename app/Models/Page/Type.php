<?php

namespace App\Models\Page;

use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use AdvancedEloquentTrait;

    protected $table = 'page_types';

    protected $fillable = [ 'name' ];
}
