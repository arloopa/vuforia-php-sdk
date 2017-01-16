<?php

namespace Vuforia;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\ResponseInterface;
use Vuforia\Exceptions\ResourceNotFoundException;
use Vuforia\Exceptions\UnauthorizedException;

class Request
{
    /**
     * @var string
     */
    private $access_key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * @var GuzzleClient
     */
    private $guzzle_client;

    /**
     * Vuforia constructor.
     *
     * @param string $access_key
     * @param string $secret_key
     */
    public function __construct($access_key, $secret_key)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
    }

    /**
     * Make a GET request to Vuforia.
     *
     * @param string $path
     * @param $body
     *
     * @return ResponseInterface
     */
    public function get(string $path, $body = null) : ResponseInterface
    {
        return $this->call('GET', $path, $body);
    }

    /**
     * Make a POST request to Vuforia.
     *
     * @param string $path
     * @param $body
     *
     * @return ResponseInterface
     */
    public function post(string $path, $body = null) : ResponseInterface
    {
        return $this->call('POST', $path, $body);
    }

    /**
     * Make a PUT request to Vuforia.
     *
     * @param string $path
     * @param $body
     *
     * @return ResponseInterface
     */
    public function put(string $path, $body = null) : ResponseInterface
    {
        return $this->call('PUT', $path, $body);
    }

    /**
     * Make a DELETE request to Vuforia.
     *
     * @param string $path
     * @param $body
     *
     * @return ResponseInterface
     */
    public function delete(string $path, $body = null) : ResponseInterface
    {
        return $this->call('DELETE', $path, $body);
    }

    /**
     * Call a request with Vuforia authorization parameters.
     *
     * @param string $method
     * @param string $path
     * @param string $body
     *
     * @throws UnauthorizedException
     *
     * @return ResponseInterface
     */
    private function call($method, string $path, $body = null):ResponseInterface
    {
        // Clear path and build url
        $path = trim($path, " \t\n\r\0\x0B\\/");
        $url = "https://vws.vuforia.com/{$path}";

        // JSONify the body
        if (!is_string($body) && !is_null($body)) {
            $body = json_encode($body);
        }

        // Build headers
        $headers = array();

        $date = new DateTime('now', new DateTimeZone('GMT'));
        $headers['Date'] = $date->format('D, d M Y H:i:s').' GMT';

        $headers['Content-Type'] = 'application/json';

        // Build authorization signature
        $signature = $this->signatureBuilder()->build($method, $url, $headers, $body);

        $headers['Authorization'] = "VWS {$this->access_key}:{$signature}";

        // Create a request
        $request = new GuzzleRequest($method, $url, $headers, $body);

        try {
            return $this->guzzleClient()->send($request);
        } catch (\Exception $e) {
            if ($e->getCode() == 404) {
                throw new ResourceNotFoundException("Your requested resource ({$method} {$url}) not found.");
            }
//            elseif ($e->getCode() == 403) {
//                throw new UnauthorizedException($e->getMessage());
//            }

            throw new UnauthorizedException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns a new instance of signature builder.
     *
     * @return SignatureBuilder
     */
    private function signatureBuilder() : SignatureBuilder
    {
        return new SignatureBuilder($this->access_key, $this->secret_key);
    }

    /**
     * Return a single instance of guzzle client.
     *
     * @return GuzzleClient
     */
    private function guzzleClient(): GuzzleClient
    {
        if (is_null($this->guzzle_client)) {
            $this->guzzle_client = new GuzzleClient(array(
                'verify' => false,
            ));
        }

        return $this->guzzle_client;
    }
}
