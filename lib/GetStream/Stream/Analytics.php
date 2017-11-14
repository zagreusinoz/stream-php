<?php
namespace GetStream\Stream;

use Exception;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;


class Analytics extends Feed
{
    const API_ENDPOINT = 'https://analytics.stream-io-api.com/analytics/';

    /**
     * @var string
     */
    protected $token;

    /**
     * @param Client $client
     * @param string $api_key
     * @param string $token
     */
    public function __construct($client, $api_key, $token)
    {
        $this->client = $client;
        $this->api_key = $api_key;
        $this->token = $token;
    }

    /**
     * @param string $resource
     * @param string $action
     *
     * @return array
     */
    protected function getHttpRequestHeaders($resource, $action)
    {
        $headers = parent::getHttpRequestHeaders($resource, $action);
        $headers['Authorization'] = $this->token;

        return $headers;
    }

    /**
     * @param string $targetUrl
     * @param array $events
     *
     * @return string
     */
    public function createRedirectUrl($targetUrl, $events)
    {
        $parsed_url = parse_url($targetUrl);
        $query_params = $this->getHttpRequestHeaders('analytics', '*');
        $query_params['api_key'] = $this->api_key;
        $query_params['url'] = $targetUrl;
        $query_params['auth_type'] = 'jwt';
        $query_params['authorization'] = $query_params['Authorization'];
        unset($query_params['Authorization']);
        unset($query_params['stream-auth-type']);
        unset($query_params['Content-Type']);
        unset($query_params['X-Stream-Client']);
        $query_params['events'] = json_encode($events);
        $qString = http_build_query($query_params);
        return static::API_ENDPOINT . 'redirect/' . "?{$qString}";
    }

}
