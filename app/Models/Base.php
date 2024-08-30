<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Base
 *
 * @package App\Models
 */
abstract class Base extends Model
{
    protected $guarded = [];
}
