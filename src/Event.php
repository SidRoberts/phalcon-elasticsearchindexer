<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

class Event extends \Phalcon\Mvc\User\Plugin
{
    protected $onlyIndexModelsWithInterface;
    
    
    
    /**
     * @param boolean $onlyIndexModelsWithInterface
     */
    public function __construct($onlyIndexModelsWithInterface = false)
    {
        $this->onlyIndexModelsWithInterface = $onlyIndexModelsWithInterface;
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
        return (($model instanceof \Sid\Phalcon\ElasticsearchIndexer\IndexInterface) || !$this->onlyIndexModelsWithInterface);
    }
}
