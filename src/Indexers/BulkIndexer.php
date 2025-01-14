<?php

namespace ScoutElastic\Indexers;

use Illuminate\Database\Eloquent\Collection;
use ScoutElastic\Facades\ElasticClient;
use ScoutElastic\Migratable;
use ScoutElastic\Payloads\RawPayload;
use ScoutElastic\Payloads\TypePayload;

class BulkIndexer implements IndexerInterface
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function update(Collection $models)
    {
        $model = $models->first();
        $indexConfigurator = $model->getIndexConfigurator();

        $bulkPayload = new TypePayload($model);

        if (in_array(Migratable::class, class_uses_recursive($indexConfigurator), true)) {
            $bulkPayload->useAlias('write');
        }

        if ($documentRefresh = config('scout_elastic.document_refresh')) {
            $bulkPayload->set('refresh', $documentRefresh);
        }

        $models->each(function ($model) use ($bulkPayload) {
            if ($model::usesSoftDelete() && config('scout.soft_delete', false)) {
                $model->pushSoftDeleteMetadata();
            }

            $modelData = array_merge(
                $model->toSearchableArray(),
                $model->scoutMetadata()
            );

            if (empty($modelData)) {
                return true;
            }

            $actionPayload = (new RawPayload)
                ->set('index._id', $model->getScoutKey());

            $bulkPayload
                ->add('body', $actionPayload->get())
                ->add('body', $modelData);
        });

        ElasticClient::bulk($bulkPayload->get());
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function delete(Collection $models)
    {
        $model = $models->first();

        $bulkPayload = new TypePayload($model);

        $models->each(function ($model) use ($bulkPayload) {
            $actionPayload = (new RawPayload)
                ->set('delete._id', $model->getScoutKey());

            $bulkPayload->add('body', $actionPayload->get());
        });

        if ($documentRefresh = config('scout_elastic.document_refresh')) {
            $bulkPayload->set('refresh', $documentRefresh);
        }

        $bulkPayload->set('client.ignore', 404);

        ElasticClient::bulk($bulkPayload->get());
    }
}
