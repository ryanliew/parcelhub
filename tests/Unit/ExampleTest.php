<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
	public function setUp()
	{   
		parent::setUp(); //this is reqired 

		$this->artisan('migrate');
	}
	
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCreationInSqlite()
    {
        $user = factory(\App\User::class)->create();
		
		$users = \App\User::all();
		
		$this->assertCount(1, $users);
		
    }
}
