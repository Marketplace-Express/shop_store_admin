<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/29
 * Time: 15:39
 */

namespace App\Http\Controllers\Helpers;



use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;

class RequestHelper
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $route
     * @param $method
     * @param array|null $body
     * @param array|null $headers
     * @return array
     */
    public function call($route, $method, $body = [], $headers = [], $query = [])
    {
//        try {
            $request = $this->client->request(
                $method,
                sprintf('%s%s', env('JURRY_BASE_API_URI'), $route),
                [
                    'headers' => $headers,
                    'json' => $body,
                    'query' => $query
                ]
            );

            $result['status'] = $request->getStatusCode();

            if (!empty($body = $request->getBody()->getContents())) {
                $body = json_decode($body, true);
            } else {
                $body = null;
            }

            $result['message'] = $body;

            return $result;
//        } catch (ClientException $exception) {
//            $this->handleHttpException($exception);
//        }
    }

    /**
     * @param \Throwable $exception
     * @throws \Exception
     */
    private function handleHttpException(\Throwable $exception)
    {
        switch ($exception->getCode()) {
            case Response::HTTP_NOT_FOUND:
                throw new \Exception('entity not found', Response::HTTP_NOT_FOUND);
            case Response::HTTP_UNAUTHORIZED:
                throw new \Exception('unauthorized', Response::HTTP_UNAUTHORIZED);
            case Response::HTTP_BAD_REQUEST:
                throw new \Exception('bad request', Response::HTTP_BAD_REQUEST);
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                throw new \Exception('internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}