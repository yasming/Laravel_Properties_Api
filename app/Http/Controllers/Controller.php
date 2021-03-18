<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    const ZAP               = 'zap';
    const VIVA_REAL         = 'viva_real';
    const PAGINATION_NUMBER = 10;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function apiResponse($key,$value)
    {
        return response()->json([$key => $value]);
    }
}
