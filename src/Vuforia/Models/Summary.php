<?php

namespace Vuforia\Models;

use Vuforia\Vuforia;

/**
 * Class Summary.
 *
 * @property string $id
 * @property string $database_name
 * @property string $target_name
 * @property string $upload_date
 * @property bool $active_flag
 * @property string $status
 * @property int $tracking_rating
 * @property string $reco_rating
 * @property int $total_recos
 * @property int $current_month_recos
 * @property int $previous_month_recos
 */
class Summary extends Model
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $is_loaded;

    /**
     * Target constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the model attribute. If not exist, load it.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'id') {
            return $this->id;
        }

        if (!$this->is_loaded) {
            Vuforia::instance()->summaries->find($this);
        }

        return $this->attributes[$name];
    }

    /**
     * Set the model attributes.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if ($name === 'id') {
            $this->id = $value;

            return;
        }

        $this->is_loaded = true;

        $this->attributes[$name] = $value;
    }
}
