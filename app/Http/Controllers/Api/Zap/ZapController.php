<?php

namespace App\Http\Controllers\Api\Zap;

use App\Http\Controllers\Controller;
use App\Services\Properties\PropertiesService;

class ZapController extends Controller
{    
    public function __invoke(PropertiesService $properties)
    {
        return $this->apiResponse(self::ZAP, $properties->getZapProperties());
    }
}
