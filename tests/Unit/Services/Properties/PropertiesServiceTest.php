<?php

namespace Tests\Unit\Services\Properties;
use App\Services\Properties\PropertiesService;
use Tests\TestCase;

class PropertiesServiceTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        ini_set('memory_limit', '2G');
        $this->service = (new PropertiesService)->setAllProperties()
                                                ->setZapProperties()
                                                ->setVivaRealProperties();
    }

    /**
     * zap
    */

    public function test_it_should_test_zap_rent_properties_rules()
    {
        $this->assertEquals($this->getZapRentPropertiesNotInRule(), collect());
    }

    public function test_it_should_test_zap_sale_properties_rules()
    {
        $this->assertEquals($this->getZapSalesPropertiesNotInRule(), collect());
    }

    private function getZapSalesPropertiesNotInRule()
    {
        return $this->service->getZapProperties()->filter(function ($item) {
            if( 
                $item['pricingInfos']['businessType'] == PropertiesService::SALE 
                    && 
                $this->zapPropertiesRules($item)
            ) return $item;
        });
    }
    
    private function zapPropertiesRules($item)
    {
        $propertiesBetweenBoundingBox = $this->getBoundingbox($item);
        if($propertiesBetweenBoundingBox) {
            return $item['pricingInfos']['price'] < PropertiesService::MIN_VALUE_ZAP_SALE*PropertiesService::MULTIPLY_ZAP_FACTOR_BOUNDING_BOX;
        }
        return $item['pricingInfos']['price'] < PropertiesService::MIN_VALUE_ZAP_SALE*PropertiesService::MULTIPLY_ZAP_FACTOR_BOUNDING_BOX;
    }

    public function test_it_should_test_if_zap_properties_have_no_address()
    {
        $this->assertEquals($this->getPropertiesWithoutAddress($this->service->getZapProperties()), collect());
    }

    private function getZapRentPropertiesNotInRule()
    {
        return $this->service->getZapProperties()->filter(function ($item) {
            if( 
                $item['pricingInfos']['businessType'] == PropertiesService::RENTAL 
                    && 
                $item['pricingInfos']['rentalTotalPrice'] < PropertiesService::MIN_VALUE_ZAP_RENTAL
            ) return $item;
        });
    }


    /**
     *  viva real
     */

    public function test_it_should_test_viva_real_rent_properties_rules()
    {
        $this->assertEquals($this->getVivaRealRentPropertiesNotInRule(), collect());
    }

    public function test_it_should_test_viva_real_sale_properties_rules()
    {
        $this->assertEquals($this->getVivaRealSalesPropertiesNotInRule(), collect());
    }

    public function test_it_should_test_if_viva_real_properties_have_no_address()
    {
        $this->assertEquals($this->getPropertiesWithoutAddress($this->service->getVivaRealProperties()), collect());
    }

    private function getVivaRealRentPropertiesNotInRule()
    {
        return $this->service->getVivaRealProperties()->filter(function ($item) {
            if( 
                $item['pricingInfos']['businessType'] == PropertiesService::RENTAL 
                    && 
                $this->vivaRealPropertiesRules($item)
            ) return $item;
        });
    }

    private function vivaRealPropertiesRules($item)
    {
        if(!isset($item['pricingInfos']['monthlyCondoFee']))           return true;
        if(!is_numeric((int)$item['pricingInfos']['monthlyCondoFee'])) return true;
        $propertiesBetweenBoundingBox = $this->getBoundingbox($item);
        if ($propertiesBetweenBoundingBox && 
            $item['pricingInfos']['rentalTotalPrice'] 
                <=
            PropertiesService::MAX_VALUE_VIVA_REAL_RENTAL*PropertiesService::MULTIPLY_VIVA_REAL_FACTOR_BOUNDING_BOX
        ) return false;
        if($propertiesBetweenBoundingBox == false && $item['pricingInfos']['rentalTotalPrice'] <= PropertiesService::MAX_VALUE_VIVA_REAL_RENTAL) return false;
        return true;
    } 

    private function getVivaRealSalesPropertiesNotInRule()
    {
        return $this->service->getVivaRealProperties()->filter(function ($item) {
            if( 
                $item['pricingInfos']['businessType'] == PropertiesService::SALE 
                    && 
                $item['pricingInfos']['price'] > PropertiesService::MAX_VALUE_VIVA_REAL_SALE
            ) return $item;
        });
    }


    private function getPropertiesWithoutAddress($zapOrVivaReal)
    {
        return $zapOrVivaReal->filter(function ($item) {
            if( 
                $item["address"]['geoLocation']['location']['lon'] == 0
                    && 
                $item["address"]['geoLocation']['location']['lat'] == 0
            ) return $item;
        });
    }

    private function getBoundingbox($item)
    {
        $longitude            =  $item['address']['geoLocation']['location']['lon'];
        $latitude             =  $item['address']['geoLocation']['location']['lat'];
        return  numberBetween(PropertiesService::MIN_LONGITUDE,$longitude,PropertiesService::MAX_LONGITUDE)
                            &&
                numberBetween(PropertiesService::MIN_LATITUDE,$latitude,PropertiesService::MAX_LATITUDE);

    }
}
