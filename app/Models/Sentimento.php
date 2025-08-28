<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentimento extends Model
{
    protected $fillable = [
        'age',
        'stream_date',
        'analised_message',
        'sentiment',
        'goal',
        'value'
            ];
}
