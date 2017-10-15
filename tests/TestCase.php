<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
    	parent::setUp();

    	TestResponse::macro('data', function($key) {
    		return $this->original->getData()[$key];
    	});
    }

    protected function from($url)
    {
        // sets the url we are posting from, so that when validation fails and redirects back
        // it goes to the correct url.
        session()->setPreviousUrl(url($url));

        return $this;
    }
}
