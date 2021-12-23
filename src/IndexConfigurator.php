<?php

namespace ScoutElastic;

use Illuminate\Support\Str;

abstract class IndexConfigurator
{
    /**
     * The name.
     *
     * @var string
     */
    protected string $name;

    /**
     * The settings.
     *
     * @var array
     */
    protected array $settings = [];

    /**
     * The default mapping.
     *
     * @var array
     */
    protected array $defaultMapping = [];

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        $name = $this->name ?? Str::snake(str_replace('IndexConfigurator', '', class_basename($this)));

        return config('scout.prefix').$name;
    }

    /**
     * Get the settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @deprecated
     */
    public function getDefaultMapping(): array
    {
        return $this->defaultMapping;
    }
}
