<?php

namespace ScoutElastic\Payloads;

use Exception;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;

class TypePayload extends IndexPayload
{
    /**
     * The model.
     *
     * @var Model
     */
    protected $model;

    /**
     * TypePayload constructor.
     *
     * @param Model $model
     * @return void
     *@throws \Exception
     */
    public function __construct(Model $model)
    {
        if (!in_array(Searchable::class, class_uses_recursive($model), true)) {
            throw new Exception(sprintf(
                'The %s model must use the %s trait.',
                get_class($model),
                Searchable::class
            ));
        }

        $this->model = $model;

        parent::__construct($model->getIndexConfigurator());

        $this->payload['type'] = $model->searchableAs();
        $this->protectedKeys[] = 'type';
    }
}
