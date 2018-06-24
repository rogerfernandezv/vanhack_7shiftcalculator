<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Http\Controllers\ShiftController;

class ShitTest extends TestCase
{

    /**
     * Just testing using Route.
     *
     * @return void
     */
    public function testRoute()
    {
    	$this->get(route('shift'))->assertStatus(200);
    	$response = $this->get('/shift');
        $response->assertStatus(200);
    }

    /**
     * Test testGetData function.
     * from ShiftController
     * convert minutes to 'H:m' string
     *
     * @return void
     */
    public function testGetDataFunction()
    {
    	$shift = new ShiftController;

    	$response = $shift->getData('https://shiftstestapi.firebaseio.com/locations.json', null, null);
    	$this->assertNotEmpty($response);
    }

    /**
     * Test testConvertMinuteToHours function.
     * from ShiftController
     * convert minutes to 'H:m' string
     *
     * @return void
     */
    public function testConvertMinuteToHoursFunction()
    {

    	$shift = new ShiftController;

    	$minutes1 = 60;
    	$response = $shift->convertMinuteToHours($minutes1);
    	$this->assertEquals($response, "1h");

    	$minutes1 = 90;
    	$response = $shift->convertMinuteToHours($minutes1);
    	$this->assertEquals($response, "1h30min");

    }

}
