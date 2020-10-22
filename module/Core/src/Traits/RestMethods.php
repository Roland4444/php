<?php

namespace Core\Traits;

use Zend\Http\Response;

trait RestMethods
{
    /**
     * Ответ в случае успешного ответа
     *
     * @param array $data
     * @return Response
     */
    protected function responseSuccess($data = []): Response
    {
        $response = new Response();

        $response->setContent(json_encode(array_merge(['status' => 'ok'], $data), JSON_THROW_ON_ERROR, 512));
        $response->getHeaders()->addHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
        return $response;
    }

    /**
     * Ответ в случае ошибки
     *
     * @param $msg
     * @param int $statusCode
     * @return Response
     */
    protected function responseError($msg, $statusCode = Response::STATUS_CODE_400)
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->getHeaders()->addHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
        $response->setContent(
            json_encode([
                'status' => 'error',
                'error' => $msg
            ])
        );
        return $response;
    }
}
