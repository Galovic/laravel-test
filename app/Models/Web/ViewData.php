<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class ViewData extends Model
{
    /**
     * @var string Model table
     */
    protected $table = 'urls';

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'keywords', 'language', 'theme'
    ];
}
