<?php

namespace Tests\Feature\Zap;

use Tests\TestCase;
use App\Services\Properties\PropertiesService;
use App\Http\Controllers\Controller;

class ZapControllerTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        ini_set('memory_limit', '2G');
        $this->service = (new PropertiesService)->setAllProperties()
                                                ->setZapProperties();
    }

    public function test_it_should_get_zap_properties()
    {
        $response = $this->get('/api/zap-properties')
                         ->assertStatus(200);
        $this->assertEquals(
                                $response->decodeResponseJson()['zap'],
                                $this->service->getZapProperties()->paginate(Controller::PAGINATION_NUMBER)
                                                                  ->toArray()
                            ); 
    }
}
