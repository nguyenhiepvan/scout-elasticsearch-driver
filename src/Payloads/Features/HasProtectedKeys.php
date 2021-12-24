<?php

namespace ScoutElastic\Payloads\Features;

trait HasProtectedKeys
{
    public function set($key, $value)
    {
        if (in_array($key, $this->protectedKeys, true)) {
            return $this;
        }

        return parent::set($key, $value);
    }
}
