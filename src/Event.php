<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

use Phalcon\Di\Injectable;
use Phalcon\Mvc\ModelInterface;

class Event extends Injectable
{
    protected bool $indexAllModels;



    public function __construct(bool $indexAllModels = true)
    {
        $this->indexAllModels = $indexAllModels;
    }



    public function afterSave(\Phalcon\Events\Event $event, ModelInterface $model, $data): void
    {
        if ($this->canModelBeIndexed($model)) {
            $this->elasticsearchIndexer->index($model);
        }
    }

    public function beforeDelete(\Phalcon\Events\Event $event, ModelInterface $model, $data): void
    {
        if ($this->canModelBeIndexed($model)) {
            $this->elasticsearchIndexer->delete($model);
        }
    }



    protected function canModelBeIndexed(ModelInterface $model): bool
    {
        return ($this->indexAllModels || ($model instanceof IndexInterface));
    }
}
