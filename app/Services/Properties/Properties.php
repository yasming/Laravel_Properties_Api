<?php

namespace App\Services\Properties;

use Illuminate\Support\Facades\Http;

class Properties
{
    const RENTAL               = 'RENTAL';   
    const SALE                 = 'SALE';   
    const MIN_VALUE_ZAP_RENTAL = 3500;
    const MIN_VALUE_ZAP_SALE   = 600000;

    private $urlToGetProperties;
    private $allProperties;
    private $zapProperties;

    public function __construct()
    {
        $this->urlToGetProperties = config('properties.url');
    }

    public function setAllProperties()
    {
        $this->allProperties = Http::get($this->urlToGetProperties)->collect();
        return $this;
    }

    public function setZapProperties()
    {
        if ($this->allProperties) {
            $this->zapProperties =  $this->allProperties->filter(function ($item){
                                        if(!isset($item['pricingInfos']['businessType'])) return false;
                                        if($this->applyBusinessRules($item))              return $item;
                                    });
        }
        return $this;
    }

    public function getZapProperties()
    {
        return $this->zapProperties;
    }

    private function applyBusinessRules($item) : bool
    {
        if($item['pricingInfos']['businessType'] == self::RENTAL) return $this->applyRentalRule($item);
        if($item['pricingInfos']['businessType'] == self::SALE)   return $this->applySaleRule($item);
    }

    private function applyRentalRule($item) : bool
    {
        if(!isset($item['pricingInfos']['rentalTotalPrice']))                        return false;
        if($item['pricingInfos']['rentalTotalPrice'] >= self::MIN_VALUE_ZAP_RENTAL) return true;
        return false; 
    }

    private function applySaleRule($item) : bool
    {
        if(!isset($item['pricingInfos']['price']))                    return false;
        if($item['pricingInfos']['price'] >= self::MIN_VALUE_ZAP_SALE) return true;
        return false; 
    }
}
