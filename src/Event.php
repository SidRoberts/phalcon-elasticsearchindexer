<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

use Phalcon\Mvc\ModelInterface;
use Phalcon\Mvc\User\Plugin;

class Event extends Plugin
{
    protected $indexAllModels;
    
    
    
    /**
     * @param boolean $indexAllModels
     */
    public function __construct($indexAllModels = true)
    {
        $this->indexAllModels = $indexAllModels;
    }
    
    
    
    /**
     * @param \Phalcon\Events\Event $event
     * @param ModelInterface        $model
     */
    public function afterSave(\Phalcon\Events\Event $event, ModelInterface $model, $data)
    {
        if ($this->canModelBeIndexed($model)) {
            $this->elasticsearchIndexer->index($model);
        }
    }

    /**
     * @param \Phalcon\Events\Event $event
     * @param ModelInterface        $model
     */
    public function beforeDelete(\Phalcon\Events\Event $event, ModelInterface $model, $data)
    {
        if ($this->canModelBeIndexed($model)) {
            $this->elasticsearchIndexer->delete($model);
        }
    }
    
    
    
    /**
     * @param ModelInterface $model
     *
     * @return boolean
     */
    protected function canModelBeIndexed(ModelInterface $model)
    {
        return ($this->indexAllModels || ($model instanceof IndexInterface));
    }
}
