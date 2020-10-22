<?php

namespace ShipmentDocs\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use ShipmentDocs\Exception\ServiceException;

class ApiService
{
    protected string $url;
    protected Client $guzzleClient;

    /**
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param array $data
     * @return string
     * @throws ServiceException
     */
    public function save(array $data): string
    {
        return $this->apiAction('POST', $this->url, ['json' => $data]);
    }

    /**
     * @param int $id
     * @return string
     * @throws ServiceException
     */
    public function delete(int $id): string
    {
        return $this->apiAction('DELETE', $this->url . '/' . $id);
    }

    /**
     * @param $method
     * @param string $url
     * @param array $options
     * @return string
     * @throws ServiceException
     */
    public function apiAction($method, string $url, array $options = []): string
    {
        try {
            $response = $this->guzzleClient->request($method, $url, $options);
        } catch (ConnectException $e) {
            throw new ServiceException('Невозможно подключиться к сервису');
        } catch (GuzzleException $e) {
            $responseBody = $e->getResponse()->getBody();
            $response = json_decode($responseBody);
            throw new ServiceException($response->message);
        }
        return $response->getBody()->getContents();
    }
}
