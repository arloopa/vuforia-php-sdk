<?php

namespace Vuforia;

use Vuforia\Services\SummaryService;
use Vuforia\Services\TargetService;
use Vuforia\Traits\Attributable;

/**
 * Class Vuforia.
 *
 * @property-read TargetService $targets
 * @property-read SummaryService $summaries
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
     * Instance of target service.
     *
     * @var TargetService
     */
    private $target_service;

    /**
     * Instance of summary service.
     *
     * @var SummaryService
     */
    private $summary_service;

    /**
     * @var Request[]
     */
    private $request_instances;

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
        $this->reconfigure($access_key, $secret_key);
    }

    /**
     * Set the Vuforia config.
     *
     * @param string $access_key
     * @param string $secret_key
     */
    public static function config($access_key, $secret_key)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($access_key, $secret_key);
        }
        else {
            self::$instance->reconfigure($access_key, $secret_key);
        }
    }

    /**
     * Returns the Vuforia instance.
     *
     * @return Vuforia
     */
    public static function instance()
    {
        return self::$instance;
    }

    /**
     * Return a single instance of request.
     *
     * @return Request
     */
    public function getRequestAttribute()
    {
        $hash = md5($this->access_key . $this->secret_key);

        if (!array_key_exists($hash, $this->request_instances)) {
            $this->request_instances[$hash] = new Request($this->access_key, $this->secret_key);
        }

        return $this->request_instances[$hash];
    }

    /**
     * Get targets service.
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

    /**
     * Get summaries service.
     *
     * @return SummaryService
     */
    public function getSummariesAttribute()
    {
        if (is_null($this->summary_service)) {
            $this->summary_service = new SummaryService();
        }

        return $this->summary_service;
    }

    /**
     * Reconfigures the Vuforia instance
     *
     * @param string $access_key
     * @param string $secret_key
     */
    private function reconfigure($access_key, $secret_key)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
    }
}
