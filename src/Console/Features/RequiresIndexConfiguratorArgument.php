<?php

namespace ScoutElastic\Console\Features;

use InvalidArgumentException;
use ScoutElastic\IndexConfigurator;
use Symfony\Component\Console\Input\InputArgument;

trait RequiresIndexConfiguratorArgument
{
    /**
     * Get the index configurator.
     *
     * @return IndexConfigurator
     */
    protected function getIndexConfigurator(): IndexConfigurator
    {
        $configuratorClass = trim($this->argument('index-configurator'));

        $configuratorInstance = new $configuratorClass;

        if (! ($configuratorInstance instanceof IndexConfigurator)) {
            throw new InvalidArgumentException(sprintf(
                'The class %s must extend %s.',
                $configuratorClass,
                IndexConfigurator::class
            ));
        }

        return new $configuratorClass;
    }

    /**
     * Get the arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            [
                'index-configurator',
                InputArgument::REQUIRED,
                'The index configurator class',
            ],
        ];
    }
}
