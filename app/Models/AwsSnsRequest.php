<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AwsSnsRequest
 *
 * @property int $id
 * @property string $json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest whereJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AwsSnsRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AwsSnsRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'json',
    ];
}
