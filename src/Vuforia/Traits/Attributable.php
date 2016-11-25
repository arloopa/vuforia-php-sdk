<?php

namespace Vuforia\Traits;

trait Attributable
{

    /**
     * Attribute mutator
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $attribute_name = 'get' . ucfirst(strtolower($name)) . 'Attribute';

        if (method_exists($this, $attribute_name)) {

            return $this->{$attribute_name}();
        } else {
            return $this->{$name};
        }
    }
}
