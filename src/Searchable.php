<?php

namespace ScoutElastic;

use Exception;
use Illuminate\Support\Arr;
use JetBrains\PhpStorm\Pure;
use Laravel\Scout\Searchable as SourceSearchable;
use ScoutElastic\Builders\FilterBuilder;
use ScoutElastic\Builders\SearchBuilder;

trait Searchable
{
    use SourceSearchable {
        SourceSearchable::getScoutKeyName as sourceGetScoutKeyName;
    }

    /**
     * The highligths.
     *
     * @var Highlight|null
     */
    private ?Highlight $highlight = null;

    /**
     * Get the index configurator.
     *
     * @return IndexConfigurator
     * @throws \Exception
     */
    public function getIndexConfigurator(): IndexConfigurator
    {
        static $indexConfigurator;

        if (! $indexConfigurator) {
            if (! isset($this->indexConfigurator) || empty($this->indexConfigurator)) {
                throw new Exception(sprintf(
                    'An index configurator for the %s model is not specified.',
                    __CLASS__
                ));
            }

            $indexConfiguratorClass = $this->indexConfigurator;
            $indexConfigurator = new $indexConfiguratorClass;
        }

        return $indexConfigurator;
    }

    /**
     * Get the mapping.
     *
     * @return array
     */
    public function getMapping(): array
    {
        $mapping = $this->mapping ?? [];

        if ($this::usesSoftDelete() && config('scout.soft_delete', false)) {
            Arr::set($mapping, 'properties.__soft_deleted', ['type' => 'integer']);
        }

        return $mapping;
    }

    /**
     * Get the search rules.
     *
     * @return array
     */
    public function getSearchRules(): array
    {
        return isset($this->searchRules) && count($this->searchRules) > 0 ?
            $this->searchRules : [SearchRule::class];
    }

    /**
     * Execute the search.
     *
     * @param  string  $query
     * @param  callable|null  $callback
     * @return FilterBuilder|SearchBuilder
     */
    public static function search($query, $callback = null): FilterBuilder|SearchBuilder
    {
        $softDelete = static::usesSoftDelete() && config('scout.soft_delete', false);

        if ($query === '*') {
            return new FilterBuilder(new static, $callback, $softDelete);
        }

        return new SearchBuilder(new static, $query, $callback, $softDelete);
    }

    /**
     * Execute a raw search.
     *
     * @param  array  $query
     * @return array
     */
    public static function searchRaw(array $query): array
    {
        $model = new static;

        return $model->searchableUsing()
            ->searchRaw($model, $query);
    }

    /**
     * Set the highlight attribute.
     *
     * @param Highlight $value
     * @return void
     */
    public function setHighlightAttribute(Highlight $value)
    {
        $this->highlight = $value;
    }

    /**
     * Get the highlight attribute.
     *
     * @return Highlight|null
     */
    public function getHighlightAttribute()
    {
        return $this->highlight;
    }

    /**
     * Get the key name used to index the model.
     *
     * @return mixed
     */
    #[Pure]
    public function getScoutKeyName()
    {
        return $this->getKeyName();
    }
}
