<?php

namespace Vuforia;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Psr\Http\Message\ResponseInterface;
use Vuforia\Exceptions\AuthenticationFailureException;
use Vuforia\Exceptions\BadImageException;
use Vuforia\Exceptions\DateRangeErrorException;
use Vuforia\Exceptions\ImageTooLargeException;
use Vuforia\Exceptions\InternalServerException;
use Vuforia\Exceptions\MetadataTooLargeException;
use Vuforia\Exceptions\RequestQuotaReachedException;
use Vuforia\Exceptions\RequestTimeTooSkewedException;
use Vuforia\Exceptions\TargetNameExistException;
use Vuforia\Exceptions\TargetStatusProcessingException;
use Vuforia\Exceptions\UnknownTargetException;

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
    public function get(string $path, $body = null): ResponseInterface
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
    public function post(string $path, $body = null): ResponseInterface
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
    public function put(string $path, $body = null): ResponseInterface
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
    public function delete(string $path, $body = null): ResponseInterface
    {
        return $this->call('DELETE', $path, $body);
    }

    /**
     * Call a request with Vuforia authorization parameters.
     *
     * @param string $method
     * @param string $path
     * @param string $body
     * @return ResponseInterface
     * @throws AuthenticationFailureException
     * @throws BadImageException
     * @throws DateRangeErrorException
     * @throws ImageTooLargeException
     * @throws InternalServerException
     * @throws MetadataTooLargeException
     * @throws RequestQuotaReachedException
     * @throws RequestTimeTooSkewedException
     * @throws TargetNameExistException
     * @throws TargetStatusProcessingException
     * @throws UnknownTargetException
     */
    private function call($method, string $path, $body = null): ResponseInterface
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
        $headers['Date'] = $date->format('D, d M Y H:i:s') . ' GMT';

        $headers['Content-Type'] = 'application/json';

        // Build authorization signature
        $signature = $this->signatureBuilder()->build($method, $url, $headers, $body);

        $headers['Authorization'] = "VWS {$this->access_key}:{$signature}";

        // Create a request
        $request = new GuzzleRequest($method, $url, $headers, $body);

        try {
            return $this->guzzleClient()->send($request);
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());

            switch ($response->result_code) {
                case 'AuthenticationFailure':
                    throw new AuthenticationFailureException('Signature authentication failed', 401);
                    break;
                case 'RequestTimeTooSkewed':
                    throw new RequestTimeTooSkewedException('Request timestamp outside allowed range', 403);
                    break;
                case 'TargetNameExist':
                    throw new TargetNameExistException('The corresponding target name already exists', 403);
                    break;
                case 'RequestQuotaReached':
                    throw new RequestQuotaReachedException('Your request quota is reached', 403);
                    break;
                case 'UnknownTarget':
                    throw new UnknownTargetException('The specified target ID does not exist', 404);
                    break;
                case 'BadImage':
                    throw new BadImageException('Image corrupted or format not supported', 422);
                    break;
                case 'ImageTooLarge':
                    throw new ImageTooLargeException('Target metadata size exceeds maximum limit', 422);
                    break;
                case 'MetadataTooLarge':
                    throw new MetadataTooLargeException('Image size exceeds maximum limit', 422);
                    break;
                case 'DateRangeError':
                    throw new DateRangeErrorException('Start date is after the end date', 422);
                    break;
                case 'TargetStatusProcessing':
                    throw new TargetStatusProcessingException(
                        'The target is not processed yet. Please repeat the request after few minutes.',
                        422
                    );
                    break;
                default:
                    throw new InternalServerException('The server encountered an internal error; please retry the request', 500);
            }
        }
    }

    /**
     * Returns a new instance of signature builder.
     *
     * @return SignatureBuilder
     */
    private function signatureBuilder(): SignatureBuilder
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
