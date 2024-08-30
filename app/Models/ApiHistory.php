<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApiHistory
 *
 * @property int $id
 * @property mixed $request
 * @property mixed $response
 * @property mixed $headers
 * @property int $status_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ApiHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApiHistory extends Model
{
    use HasFactory;

    protected $table = 'api_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'request',
        'response',
        'headers',
        'status_code',
    ];

    /**
     * Json encode all values being saved into this field.
     *
     * @param String $value
     *
     * @return Void
     */
    public function setRequestAttribute($value)
    {
        $this->attributes['request'] = json_encode($value);
    }

    /**
     * Json encode all values being saved into this field.
     *
     * @param String $value
     *
     * @return Void
     */
    public function setResponseAttribute($value)
    {
        $this->attributes['response'] = json_encode($value);
    }

    /**
     * Json encode all values being saved into this field.
     *
     * @param String $value
     *
     * @return Void
     */
    public function setHeadersAttribute($value)
    {
        $this->attributes['headers'] = json_encode($value);
    }
}
