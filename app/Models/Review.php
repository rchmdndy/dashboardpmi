<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperReview
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_email',
        'user_transaction_id',
        'review',
        'score'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function user_transaction(){
        return $this->belongsTo(UserTransaction::class);
    }
}
