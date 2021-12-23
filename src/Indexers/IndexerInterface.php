<?php

namespace ScoutElastic\Indexers;

use Illuminate\Database\Eloquent\Collection;

interface IndexerInterface
{
    /**
     * Update documents.
     *
     * @param Collection $models
     */
    public function update(Collection $models);

    /**
     * Delete documents.
     *
     * @param Collection $models
     */
    public function delete(Collection $models);
}
