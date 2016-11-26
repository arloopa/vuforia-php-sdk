<?php

namespace Vuforia\Models;

abstract class Model
{
    protected $attributes;

    /**
     * @var static[]
     */
    protected static $instances = array();

    /**
     * Find instance of model.
     *
     * @param mixed $id
     *
     * @return static
     */
    public static function find($id)
    {
        if (!array_key_exists($id, self::$instances)) {
            self::$instances[$id] = new static($id);
        }

        return self::$instances[$id];
    }
}
