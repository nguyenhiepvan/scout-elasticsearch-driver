<?php

namespace ScoutElastic;

use JetBrains\PhpStorm\ArrayShape;
use ScoutElastic\Builders\SearchBuilder;

class SearchRule
{
    /**
     * The builder.
     *
     * @var SearchBuilder
     */
    protected SearchBuilder $builder;

    /**
     * SearchRule constructor.
     *
     * @param SearchBuilder $builder
     * @return void
     */
    public function __construct(SearchBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Check if this is applicable.
     *
     * @return bool
     */
    public function isApplicable(): bool
    {
        return true;
    }

    /**
     * Build the highlight payload.
     *
     * @return array|null
     */
    public function buildHighlightPayload(): ?array
    {
    }

    /**
     * Build the query payload.
     *
     * @return array
     */
    #[ArrayShape(['must' => "array[]"])]
    public function buildQueryPayload()
    {
        return [
            'must' => [
                'query_string' => [
                    'query' => $this->builder->query,
                ],
            ],
        ];
    }
}
