<?php

namespace ScoutElastic;

trait Migratable
{
    /**
     * Get the write alias.
     *
     * @return string
     */
    public function getWriteAlias(): string
    {
        return $this->getName().'_write';
    }
}
