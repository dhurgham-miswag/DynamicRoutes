<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRoute extends Model
{
    protected $fillable = [
        'middleware',
        'service_group',
        'route_name',
        'controller_name',
        'method_name',
        'http_method',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
