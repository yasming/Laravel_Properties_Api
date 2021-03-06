<?php

namespace App\Services\Properties;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PropertiesService
{
    const RENTAL                                 = 'RENTAL';   
    const SALE                                   = 'SALE';
    const TYPE_ZAP                               = 1;
    const TYPE_VIVA_REAL                         = 2;   
    const MULTIPLY_ZAP_FACTOR_BOUNDING_BOX       = 0.9;
    const MULTIPLY_VIVA_REAL_FACTOR_BOUNDING_BOX = 1.5;
    const MULTIPLY_RENT_FACTOR_VIVA_REAL         = 0.3;

    const MIN_LONGITUDE                          = -46.693419;
    const MAX_LONGITUDE                          = -46.641146;
    const MIN_LATITUDE                           = -23.568704;
    const MAX_LATITUDE                           = -23.546686;
        
    const MIN_VALUE_ZAP_RENTAL                   = 3500;
    const MIN_VALUE_ZAP_SALE                     = 600000;
    const MIN_VALUE_ZAP_SALE_USABLE_AREA         = 3500;
        
    const MAX_VALUE_VIVA_REAL_RENTAL             = 4000;
    const MAX_VALUE_VIVA_REAL_SALE               = 700000;

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
        if(!Cache::has('allProperties')) {
            $this->allProperties = Http::get($this->urlToGetProperties)->collect();
            $this->putItensOnCache('allProperties',$this->allProperties);
        }
        if(Cache::has('allProperties')) $this->allProperties = Cache::get('allProperties');
        return $this;
    }

    public function setZapProperties()
    {
        if(!Cache::has('zapProperties')) {
            $this->zapProperties =  $this->getSpecificProperties(self::TYPE_ZAP);
            $this->putItensOnCache('zapProperties',$this->zapProperties);
        }
        if(Cache::has('zapProperties')) $this->zapProperties = Cache::get('zapProperties');
        return $this;
    }

    public function getZapProperties()
    {
        return $this->zapProperties->values();
    }

    public function setVivaRealProperties()
    {
        if(!Cache::has('vivaRealProperties')) {
            $this->vivaRealProperties = $this->getSpecificProperties(self::TYPE_VIVA_REAL);
            $this->putItensOnCache('vivaRealProperties',$this->vivaRealProperties);
        }
        if(Cache::has('vivaRealProperties')) $this->vivaRealProperties = Cache::get('vivaRealProperties');
        return $this;
    }

    public function getVivaRealProperties()
    {
        return $this->vivaRealProperties->values();
    }

    private function getSpecificProperties($type)
    {
        return $this->allProperties->filter(function ($item) use ($type){
            if(!$this->checkIfHaveLatAndLong($item))          return false;
            if(!isset($item['pricingInfos']['businessType'])) return false;
            if($this->applyBusinessRules($item, $type))       return $item;
        });
    }

    private function checkIfHaveLatAndLong($item)
    {
        if( !isset($item['address']['geoLocation']['location']['lon']) 
                && 
            !isset($item['address']['geoLocation']['location']['lat'])
        ) return false;

        if( $item['address']['geoLocation']['location']['lon'] == 0 
                && 
            $item['address']['geoLocation']['location']['lat'] == 0
        ) return false;

        return true;   
    }

    private function applyBusinessRules($item,$type) : bool
    {
        if($item['pricingInfos']['businessType'] == self::RENTAL) return $this->applyRentalRule($item,$type);
        if($item['pricingInfos']['businessType'] == self::SALE)   return $this->applySalesRule($item,$type);
    }

    private function applyRentalRule($item,$type) : bool
    {
        if(!isset($item['pricingInfos']['rentalTotalPrice']))                                                  return false;
        if($type == self::TYPE_ZAP && $item['pricingInfos']['rentalTotalPrice'] >= self::MIN_VALUE_ZAP_RENTAL) return true;
        if($type == self::TYPE_VIVA_REAL)                                                                      return $this->applyRentalRuleVivaReal($item);
        return false; 
    }

    private function applyRentalRuleVivaReal($item) : bool
    {
        if(!isset($item['pricingInfos']['monthlyCondoFee']))           return false;
        if(!is_numeric((int)$item['pricingInfos']['monthlyCondoFee'])) return false;
        if($item['pricingInfos']['monthlyCondoFee'] >= $item['pricingInfos']['rentalTotalPrice']*self::MULTIPLY_RENT_FACTOR_VIVA_REAL) 
            return false;

        $boundingBox = $this->getBoundingbox($item);
        if ($boundingBox && 
            $item['pricingInfos']['rentalTotalPrice'] 
                <=
            self::MAX_VALUE_VIVA_REAL_RENTAL*self::MULTIPLY_VIVA_REAL_FACTOR_BOUNDING_BOX
        ) return true;

        if($boundingBox == false && $item['pricingInfos']['rentalTotalPrice'] <= self::MAX_VALUE_VIVA_REAL_RENTAL) return true;
        return false;
    }

    private function applySalesRule($item, $type) : bool
    {
        if(!isset($item['pricingInfos']['price']))                                                            return false;
        if($type == self::TYPE_ZAP)                                                                           return $this->applySalesRuleZap($item);
        if($type == self::TYPE_VIVA_REAL && $item['pricingInfos']['price'] <= self::MAX_VALUE_VIVA_REAL_SALE) return true;
        return false; 
    }

    private function applySalesRuleZap($item)
    {
        if(!isset($item['usableAreas']))                                                            return false;
        if($item['usableAreas'] == 0)                                                               return false;
        if($item['usableAreas'] <= 3500 )                                                           return false;
        $boundingBox = $this->getBoundingbox($item);
        if($boundingBox && $item['pricingInfos']['price'] >= self::MIN_VALUE_ZAP_SALE*self::MULTIPLY_ZAP_FACTOR_BOUNDING_BOX) 
            return true;
        if($boundingBox == false && $item['pricingInfos']['price'] >= self::MIN_VALUE_ZAP_SALE) return true;
        return false;
    }

    private function getBoundingbox($item)
    {
        $longitude = $item['address']['geoLocation']['location']['lon'];
        $latitude  = $item['address']['geoLocation']['location']['lat'];
        return  numberBetween(self::MIN_LONGITUDE,$longitude,self::MAX_LONGITUDE)
                    &&
                numberBetween(self::MIN_LATITUDE,$latitude,self::MAX_LATITUDE);
    }

    private function putItensOnCache($key,$value)
    {
        Cache::put($key,$value);
    }
}
