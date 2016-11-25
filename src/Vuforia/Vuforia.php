<?php

namespace Vuforia;

use Vuforia\Services\TargetService;
use Vuforia\Traits\Attributable;

/**
 * Class Vuforia
 * @package Vuforia
 * @property-read TargetService $targets
 * @property-read Request $request
 */
class Vuforia
{

    use Attributable;

    /**
     * @var string
     */
    private $access_key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * Instance of target servcie
     *
     * @var TargetService
     */
    private $target_service;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Vuforia
     */
    private static $instance;

    /**
     * Vuforia constructor.
     *
     * @param string $access_key
     * @param string $secret_key
     */
    private function __construct($access_key, $secret_key)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
    }

    /**
     * Set the Vuforia config
     *
     * @param string $access_key
     * @param string $secret_key
     */
    public static function config($access_key, $secret_key)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($access_key, $secret_key);
        }
    }

    /**
     * Returns the Vuforia instance
     *
     * @return Vuforia
     */
    public static function instance()
    {
        return self::$instance;
    }

    /**
     * Return a single instance of request
     *
     * @return Request
     */
    public function getRequestAttribute()
    {
        if (is_null($this->request)) {
            $this->request = new Request($this->access_key, $this->secret_key);
        }

        return $this->request;
    }

    /**
     * Get targets
     *
     * @return TargetService
     */
    public function getTargetsAttribute()
    {
        if (is_null($this->target_service)) {
            $this->target_service = new TargetService();
        }

        return $this->target_service;
    }
}
