<?php

namespace App\Http\Controllers\Api\Zap;

use App\Http\Controllers\Controller;
use App\Services\Properties\Properties;

class ZapController extends Controller
{    
    public function __invoke(Properties $properties)
    {
        return $this->apiResponse(self::ZAP, $properties->getZapProperties());
    }
}
