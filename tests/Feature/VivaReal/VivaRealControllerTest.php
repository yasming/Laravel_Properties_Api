<?php

namespace Tests\Feature\VivaReal;

use Tests\TestCase;
use App\Services\Properties\PropertiesService;
use App\Http\Controllers\Controller;

class VivaRealControllerTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        ini_set('memory_limit', '2G');
        $this->service = (new PropertiesService)->setAllProperties()
                                                ->setVivaRealProperties();
    }

    public function test_it_should_get_viva_real_properties()
    {
        $response = $this->get('/api/viva-real-properties')
                         ->assertStatus(200);
        $this->assertEquals(
                                $response->decodeResponseJson()['viva_real'],
                                $this->service->getVivaRealProperties()->paginate(Controller::PAGINATION_NUMBER)
                                                                       ->toArray()
                            ); 
    }
}
