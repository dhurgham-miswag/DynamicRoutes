<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class GgController extends Controller
{
    public function test(): JsonResponse
    {
        return response()->json(
            ['gg' => 'yes']
        );
    }
}
