<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    protected $fillable = [
        'common_name',
        'watering_general_benchmark',
    ];

    protected $casts = [
        'watering_general_benchmark' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
