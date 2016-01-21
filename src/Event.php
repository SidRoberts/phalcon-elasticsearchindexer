<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

class Event extends \Phalcon\Mvc\User\Plugin
{
    /**
     * @param \Phalcon\Events\Event       $event
     * @param \Phalcon\Mvc\ModelInterface $model
     */
    public function afterSave(\Phalcon\Events\Event $event, \Phalcon\Mvc\ModelInterface $model, $data)
    {
        $this->elasticsearchIndexer->index($model);
    }

    /**
     * @param \Phalcon\Events\Event       $event
     * @param \Phalcon\Mvc\ModelInterface $model
     */
    public function beforeDelete(\Phalcon\Events\Event $event, \Phalcon\Mvc\ModelInterface $model, $data)
    {
        $this->elasticsearchIndexer->delete($model);
    }
}
