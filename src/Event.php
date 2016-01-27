<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

class Event extends \Phalcon\Mvc\User\Plugin
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
     * @param \Phalcon\Events\Event       $event
     * @param \Phalcon\Mvc\ModelInterface $model
     */
    public function afterSave(\Phalcon\Events\Event $event, \Phalcon\Mvc\ModelInterface $model, $data)
    {
        if ($this->canModelBeIndexed($model)) {
            $this->elasticsearchIndexer->index($model);
        }
    }

    /**
     * @param \Phalcon\Events\Event       $event
     * @param \Phalcon\Mvc\ModelInterface $model
     */
    public function beforeDelete(\Phalcon\Events\Event $event, \Phalcon\Mvc\ModelInterface $model, $data)
    {
        if ($this->canModelBeIndexed($model)) {
            $this->elasticsearchIndexer->delete($model);
        }
    }
    
    
    
    /**
     * @param \Phalcon\Mvc\ModelInterface $model
     *
     * @return boolean
     */
    protected function canModelBeIndexed(\Phalcon\Mvc\ModelInterface $model)
    {
        return ($this->indexAllModels || ($model instanceof \Sid\Phalcon\ElasticsearchIndexer\IndexInterface));
    }
}
