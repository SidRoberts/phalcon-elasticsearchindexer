<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

class Event extends \Phalcon\Mvc\User\Plugin
{
    public function afterSave(\Phalcon\Events\Event $event, \Phalcon\Mvc\ModelInterface $model, $data)
    {
        $this->elasticsearchIndexer->index($model);
    }

    public function beforeDelete(\Phalcon\Events\Event $event, \Phalcon\Mvc\ModelInterface $model, $data)
    {
        $this->elasticsearchIndexer->delete($model);
    }
}
