<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QRCode
 *
 * @property int $id
 * @property string $identifier
 * @property string|null $expires
 * @property int|null $scanned_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode whereScannedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QRCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QRCode extends Model
{
    protected $table = 'qr_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier',
        'expires',
        'scanned_by',
    ];
}
