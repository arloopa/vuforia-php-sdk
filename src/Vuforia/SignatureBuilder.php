<?php

namespace Vuforia;

use Vuforia\Exceptions\SignatureBuilderException;

/**
 * Class SignatureBuilder.
 */
class SignatureBuilder
{
    private $contentType = '';
    private $hexDigest = 'd41d8cd98f00b204e9800998ecf8427e'; // Hex digest of an empty string

    /**
     * @var string
     */
    private $access_key;

    /**
     * @var string
     */
    private $secret_key;

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
     * Build a signature for given request params.
     *
     * @param string $method
     * @param string $url
     * @param array  $headers
     * @param string $body
     *
     * @return string
     *
     * @throws SignatureBuilderException
     */
    public function build(string $method, string $url, array $headers, $body = null):string
    {
        $method = strtoupper($method);

        // note that header names are converted to lower case
        $date = $headers['Date'];
        $path = parse_url($url)['path'] ?? null;

        // Not all requests will define a content-type
        if (isset($headers['Content-Type'])) {
            $this->contentType = $headers['Content-Type'];
        }

        if ($method == 'GET' || $method == 'DELETE') {
            // Do nothing because the strings are already set correctly
        } elseif ($method == 'POST' || $method == 'PUT') {
            // If this is a POST or PUT the request should have a request body
            $this->hexDigest = md5($body, false);
        } else {
            throw new SignatureBuilderException('Invalid method passed to Signature Builder');
        }

        $toDigest = implode("\n", [$method, $this->hexDigest, $this->contentType, $date, $path]);

        try {
            // the SHA1 hash needs to be transformed from hexidecimal to Base64
            $shaHashed = $this->hexToBase64(hash_hmac('sha1', $toDigest, $this->secret_key));
        } catch (\Exception $e) {
            throw new SignatureBuilderException('Cannot generate signature.');
        }

        return $shaHashed;
    }

    /**
     * Convert hex to base64.
     *
     * @param $hex
     *
     * @return string
     */
    private function hexToBase64($hex)
    {
        $return = '';
        foreach (str_split($hex, 2) as $pair) {
            $return .= chr(hexdec($pair));
        }

        return base64_encode($return);
    }
}
