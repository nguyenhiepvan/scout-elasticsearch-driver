<?php

namespace ScoutElastic\Console\Features;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use ScoutElastic\Searchable;
use Symfony\Component\Console\Input\InputArgument;

trait RequiresModelArgument
{
    /**
     * Get the model.
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        $modelClass = trim($this->argument('model'));

        $modelInstance = new $modelClass;

        if (
            ! ($modelInstance instanceof Model) ||
            !in_array(Searchable::class, class_uses_recursive($modelClass), true)
        ) {
            throw new InvalidArgumentException(sprintf(
                'The %s class must extend %s and use the %s trait.',
                $modelClass,
                Model::class,
                Searchable::class
            ));
        }

        return $modelInstance;
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
                'model',
                InputArgument::REQUIRED,
                'The model class',
            ],
        ];
    }
}
