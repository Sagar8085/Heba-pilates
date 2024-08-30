<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailVerificationTokens
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property int $confirmed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens whereConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailVerificationTokens whereUserId($value)
 * @mixin \Eloquent
 */
class EmailVerificationTokens extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
    ];
}
