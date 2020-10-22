<?php


namespace CoreTest\Traits;

use Core\Traits\RestMethods;
use PHPUnit\Framework\TestCase;
use Zend\Http\Response;

class RestMethodsTest extends TestCase
{
    use RestMethods;

    public function testResponseSuccessWithData()
    {
        $response = $this->responseSuccess(['data' => 'dummy_data']);

        $this->assertEquals('{"status":"ok","data":"dummy_data"}', $response->getBody());
    }

    public function testResponseSuccessWithoutData()
    {
        $response = $this->responseSuccess();

        $this->assertEquals('{"status":"ok"}', $response->getBody());
    }

    public function testResponseErrorWithDefaultStatusCode()
    {
        $response = $this->responseError('dummy_error');

        $this->assertEquals('{"status":"error","error":"dummy_error"}', $response->getBody());
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testResponseErrorWithCustomStatusCode()
    {
        $response = $this->responseError('dummy_error', Response::STATUS_CODE_403);

        $this->assertEquals('{"status":"error","error":"dummy_error"}', $response->getBody());
        $this->assertEquals(403, $response->getStatusCode());
    }
}
