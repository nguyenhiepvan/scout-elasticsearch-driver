<?php

namespace ScoutElastic\Builders;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;

class FilterBuilder extends Builder
{
    /**
     * The condition array.
     *
     * @var array
     */
    public $wheres = [
        'must'     => [],
        'must_not' => [],
    ];

    /**
     * The with array.
     *
     * @var array|string
     */
    public string|array $with;

    /**
     * The offset.
     *
     * @var int
     */
    public int $offset = 0;

    /**
     * The collapse parameter.
     *
     * @var string
     */
    public string $collapse = "";

    /**
     * The select array.
     *
     * @var array
     */
    public array $select = [];

    /**
     * The min_score parameter.
     *
     * @var string
     */
    public string $minScore = "";

    /**
     * FilterBuilder constructor.
     *
     * @param Model $model
     * @param callable|null $callback
     * @param bool $softDelete
     * @return void
     */
    public function __construct(Model $model, $callback = null, $softDelete = false)
    {
        parent::__construct($model, $callback, $softDelete);

        if ($softDelete) {
            $this->wheres['must'][] = [
                'term' => [
                    '__soft_deleted' => 0,
                ],
            ];
        }
    }

    /**
     * Add a where condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html Term query
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html Range query
     *
     * Supported operators are =, &gt;, &lt;, &gt;=, &lt;=, &lt;&gt;
     * @param string $field Field name
     * @param mixed $value Scalar value or an array
     * @return $this
     */
    public function where($field, $value): static
    {
        $args = func_get_args();

        if (count($args) === 3) {
            [$field, $operator, $value] = $args;
        } else {
            $operator = '=';
        }

        switch ($operator) {
            case '=':
                $this->wheres['must'][] = [
                    'term' => [
                        $field => $value,
                    ],
                ];
                break;

            case '>':
                $this->wheres['must'][] = [
                    'range' => [
                        $field => [
                            'gt' => $value,
                        ],
                    ],
                ];
                break;

            case '<':
                $this->wheres['must'][] = [
                    'range' => [
                        $field => [
                            'lt' => $value,
                        ],
                    ],
                ];
                break;

            case '>=':
                $this->wheres['must'][] = [
                    'range' => [
                        $field => [
                            'gte' => $value,
                        ],
                    ],
                ];
                break;

            case '<=':
                $this->wheres['must'][] = [
                    'range' => [
                        $field => [
                            'lte' => $value,
                        ],
                    ],
                ];
                break;

            case '!=':
            case '<>':
                $this->wheres['must_not'][] = [
                    'term' => [
                        $field => $value,
                    ],
                ];
                break;
        }

        return $this;
    }

    /**
     * Add a whereIn condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html Terms query
     *
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereIn(string $field, array $value): static
    {
        $this->wheres['must'][] = [
            'terms' => [
                $field => $value,
            ],
        ];

        return $this;
    }

    /**
     * Add a whereNotIn condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html Terms query
     *
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereNotIn(string $field, array $value): static
    {
        $this->wheres['must_not'][] = [
            'terms' => [
                $field => $value,
            ],
        ];

        return $this;
    }

    /**
     * Add a whereBetween condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html Range query
     *
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereBetween(string $field, array $value): static
    {
        $this->wheres['must'][] = [
            'range' => [
                $field => [
                    'gte' => $value[0],
                    'lte' => $value[1],
                ],
            ],
        ];

        return $this;
    }

    /**
     * Add a whereNotBetween condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html Range query
     *
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereNotBetween(string $field, array $value): static
    {
        $this->wheres['must_not'][] = [
            'range' => [
                $field => [
                    'gte' => $value[0],
                    'lte' => $value[1],
                ],
            ],
        ];

        return $this;
    }

    /**
     * Add a whereExists condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html Exists query
     *
     * @param string $field
     * @return $this
     */
    public function whereExists(string $field): static
    {
        $this->wheres['must'][] = [
            'exists' => [
                'field' => $field,
            ],
        ];

        return $this;
    }

    /**
     * Add a whereNotExists condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-exists-query.html Exists query
     *
     * @param string $field
     * @return $this
     */
    public function whereNotExists(string $field): static
    {
        $this->wheres['must_not'][] = [
            'exists' => [
                'field' => $field,
            ],
        ];

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html Match query
     *
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function whereMatch(string $field, string $value): static
    {
        $this->wheres['must'][] = [
            'match' => [
                $field => $value,
            ],
        ];

        return $this;
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html Match query
     *
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function whereNotMatch(string $field, string $value): static
    {
        $this->wheres['must_not'][] = [
            'match' => [
                $field => $value,
            ],
        ];

        return $this;
    }

    /**
     * Add a whereRegexp condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html Regexp query
     *
     * @param string $field
     * @param string $value
     * @param string $flags
     * @return $this
     */
    public function whereRegexp(string $field, string $value, string $flags = 'ALL'): static
    {
        $this->wheres['must'][] = [
            'regexp' => [
                $field => [
                    'value' => $value,
                    'flags' => $flags,
                ],
            ],
        ];

        return $this;
    }

    /**
     * Add a whereGeoDistance condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html Geo distance query
     *
     * @param string $field
     * @param array|string $value
     * @param int|string $distance
     * @return $this
     */
    public function whereGeoDistance(string $field, array|string $value, int|string $distance): static
    {
        $this->wheres['must'][] = [
            'geo_distance' => [
                'distance' => $distance,
                $field     => $value,
            ],
        ];

        return $this;
    }

    /**
     * Add a whereGeoBoundingBox condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-bounding-box-query.html Geo bounding box query
     *
     * @param string $field
     * @param array $value
     * @return $this
     */
    public function whereGeoBoundingBox(string $field, array $value): static
    {
        $this->wheres['must'][] = [
            'geo_bounding_box' => [
                $field => $value,
            ],
        ];

        return $this;
    }

    /**
     * Add a whereGeoPolygon condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-polygon-query.html Geo polygon query
     *
     * @param string $field
     * @param array $points
     * @return $this
     */
    public function whereGeoPolygon(string $field, array $points): static
    {
        $this->wheres['must'][] = [
            'geo_polygon' => [
                $field => [
                    'points' => $points,
                ],
            ],
        ];

        return $this;
    }

    /**
     * Add a whereGeoShape condition.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html Querying Geo Shapes
     *
     * @param string $field
     * @param array $shape
     * @param string $relation
     * @return $this
     */
    public function whereGeoShape(string $field, array $shape, $relation = 'INTERSECTS'): static
    {
        $this->wheres['must'][] = [
            'geo_shape' => [
                $field => [
                    'shape'    => $shape,
                    'relation' => $relation,
                ],
            ],
        ];

        return $this;
    }

    /**
     * Add a orderBy clause.
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function orderBy($field, $direction = 'asc'): static
    {
        $this->orders[] = [
            $field => strtolower($direction) === 'asc' ? 'asc' : 'desc',
        ];

        return $this;
    }

    /**
     * Add a raw order clause.
     *
     * @param array $payload
     * @return $this
     */
    public function orderRaw(array $payload): static
    {
        $this->orders[] = $payload;

        return $this;
    }

    /**
     * Explain the request.
     *
     * @return array
     */
    public function explain(): array
    {
        return $this
            ->engine()
            ->explain($this);
    }

    /**
     * Profile the request.
     *
     * @return array
     */
    public function profile(): array
    {
        return $this
            ->engine()
            ->profile($this);
    }

    /**
     * Build the payload.
     *
     * @return array
     */
    public function buildPayload(): array
    {
        return $this
            ->engine()
            ->buildSearchQueryPayloadCollection($this);
    }

    /**
     * Eager load some some relations.
     *
     * @param array|string $relations
     * @return $this
     */
    public function with(array|string $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Set the query offset.
     *
     * @param int $offset
     * @return $this
     */
    public function from(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): Collection
    {
        $collection = parent::get();

        if (isset($this->with) && $collection->count() > 0) {
            $collection->load($this->with);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $paginator = parent::paginate($perPage, $pageName, $page);

        if (isset($this->with) && $paginator->total() > 0) {
            $paginator
                ->getCollection()
                ->load($this->with);
        }

        return $paginator;
    }

    /**
     * Collapse by a field.
     *
     * @param string $field
     * @return $this
     */
    public function collapse(string $field): static
    {
        $this->collapse = $field;

        return $this;
    }

    /**
     * Select one or many fields.
     *
     * @param mixed $fields
     * @return $this
     */
    public function select(mixed $fields): static
    {
        $this->select = array_merge(
            $this->select,
            Arr::wrap($fields)
        );

        return $this;
    }

    /**
     * Set the min_score on the filter.
     *
     * @param float $score
     * @return $this
     */
    public function minScore(float $score): static
    {
        $this->minScore = $score;

        return $this;
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function count(): int
    {
        return $this
            ->engine()
            ->count($this);
    }

    /**
     * {@inheritdoc}
     */
    public function withTrashed(): FilterBuilder|Builder|static
    {
        $this->wheres['must'] = collect($this->wheres['must'])
            ->filter(function ($item) {
                return Arr::get($item, 'term.__soft_deleted') !== 0;
            })
            ->values()
            ->all();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function onlyTrashed()
    {
        return tap($this->withTrashed(), function () {
            $this->wheres['must'][] = ['term' => ['__soft_deleted' => 1]];
        });
    }
}
