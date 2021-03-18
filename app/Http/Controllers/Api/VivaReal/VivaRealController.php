<?php

namespace App\Http\Controllers\Api\VivaReal;

use App\Http\Controllers\Controller;
use App\Services\Properties\PropertiesService;

class VivaRealController extends Controller
{    
    public function __invoke(PropertiesService $properties)
    {
        return $this->apiResponse(self::VIVA_REAL, $properties->getVivaRealProperties()->paginate(self::PAGINATION_NUMBER));
    }
}
