<?php

namespace Vuforia\Models;

abstract class Model
{
    protected $attributes;

    /**
     * @var static[][]
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
        $class = get_called_class();

        if (!array_key_exists($class, static::$instances)) {
            static::$instances[$class] = array();
        }

        if (!array_key_exists($id, static::$instances[$class])) {
            static::$instances[$class][$id] = new static($id);
        }

        return static::$instances[$class][$id];
    }
}
