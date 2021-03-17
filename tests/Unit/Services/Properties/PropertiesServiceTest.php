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
        $this->service = (new PropertiesService)->setAllProperties()->setZapProperties();
    }

    public function test_it_should_test_zap_rent_properties_rules()
    {
        $this->assertEquals($this->getZapRentPropertiesNotInRule(), collect());
    }

    public function test_it_should_test_zap_sale_properties_rules()
    {
        $this->assertEquals($this->getZapSalesPropertiesNotInRule(), collect());
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

    private function getZapSalesPropertiesNotInRule()
    {
        return $this->service->getZapProperties()->filter(function ($item) {
            if( 
                $item['pricingInfos']['businessType'] == PropertiesService::SALE 
                    && 
                $item['pricingInfos']['price'] < PropertiesService::MIN_VALUE_ZAP_SALE
            ) return $item;
        });
    }
}
