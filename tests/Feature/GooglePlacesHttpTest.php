<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GooglePlacesHttpTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCrawlningTest()
    {
        // valid Request
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('GET', '/api/testnew', [
            'lat' => '51.3556649',
            'lng' => '8.4801828',
            'type' => 'restaurant',
            'radius' => '1500',
            'placeid' => 'ChIJXWbrVpXqu0cR-shmtpMKcTo',
            'formattedaddress' => 'Olsberg, Germany'
            ]);
        $response->assertStatus(200);        

        // invalid Request | without placeid
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('GET', '/api/testnew', [
            'lat' => '51.3556649',
            'lng' => '8.4801828',
            'type' => 'restaurant',
            'radius' => '1500',            
            'formattedaddress' => 'Olsberg, Germany'            
            ]);
        $response->assertStatus(422);       


        #$response = $this->get('/api/testnew?lat=51.3556649&lng=8.4801828&type=restaurant&radius=1500&placeid=&formattedaddress=Olsberg, Germany');
        #$response->assertStatus(422);        
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchByZipTest()
    {
        // valid Request
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('GET', '/api/searchbyzip/59339');
        $response->assertStatus(200);           

        // invalid Request
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('GET', '/api/searchbyzip/1');
        $response->assertStatus(422);                   
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchByZipAndTypeTest()
    {
        // valid Request
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('GET', '/api/searchbyzip/59339/bar');
        $response->assertStatus(200);           

        // invalid Request
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('GET', '/api/searchbyzip/1/bar');
        $response->assertStatus(422); 
      
    }    
    

}
