<?php

namespace App\Services\Properties;

use Illuminate\Support\Facades\Http;

class PropertiesService
{
    const RENTAL                     = 'RENTAL';   
    const SALE                       = 'SALE';
    const TYPE_ZAP                   = 1;
    const TYPE_VIVA_REAL             = 2;   
    const MIN_VALUE_ZAP_RENTAL       = 3500;
    const MAX_VALUE_VIVA_REAL_RENTAL = 4000;
    const MIN_VALUE_ZAP_SALE         = 600000;
    const MAX_VALUE_VIVA_REAL_SALE   = 700000;

    private $urlToGetProperties;
    private $allProperties;
    private $zapProperties;
    private $vivaRealProperties;

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
        $this->zapProperties =  $this->allProperties->filter(function ($item){
                                    if(!isset($item['pricingInfos']['businessType']))   return false;
                                    if($this->applyBusinessRules($item,self::TYPE_ZAP)) return $item;
                                });
        
        return $this;
    }

    public function getZapProperties()
    {
        return $this->zapProperties->values();
    }

    public function setVivaRealProperties()
    {
        $this->vivaRealProperties = $this->allProperties->filter(function ($item){
                                        if(!isset($item['pricingInfos']['businessType']))          return false;
                                        if($this->applyBusinessRules($item, self::TYPE_VIVA_REAL)) return $item;
                                    });
        
        return $this;
    }

    public function getVivaRealProperties()
    {
        return $this->vivaRealProperties->values();
    }

    private function applyBusinessRules($item,$type) : bool
    {
        if($item['pricingInfos']['businessType'] == self::RENTAL) return $this->applyRentalRule($item,$type);
        if($item['pricingInfos']['businessType'] == self::SALE)   return $this->applySalesRule($item,$type);
    }

    private function applyRentalRule($item,$type) : bool
    {
        if(!isset($item['pricingInfos']['rentalTotalPrice']))                                                              return false;
        if($type == self::TYPE_ZAP       && $item['pricingInfos']['rentalTotalPrice'] >= self::MIN_VALUE_ZAP_RENTAL)       return true;
        if($type == self::TYPE_VIVA_REAL && $item['pricingInfos']['rentalTotalPrice'] <= self::MAX_VALUE_VIVA_REAL_RENTAL) return true;
        return false; 
    }

    private function applySalesRule($item, $type) : bool
    {
        if(!isset($item['pricingInfos']['price']))                                                            return false;
        if($type == self::TYPE_ZAP       && $item['pricingInfos']['price'] >= self::MIN_VALUE_ZAP_SALE)       return true;
        if($type == self::TYPE_VIVA_REAL && $item['pricingInfos']['price'] <= self::MAX_VALUE_VIVA_REAL_SALE) return true;
        return false; 
    }
}
