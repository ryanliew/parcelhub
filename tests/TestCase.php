<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create ajax POST request
     * @param $uri
     * @param array $data
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function ajaxPost($uri, $data = [])
    {
        return $this->post($uri, $data, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    }

    /**
     * Create ajax GET request
     * @param $uri
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function ajaxGet($uri)
    {
        return $this->get($uri, ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    }
}
