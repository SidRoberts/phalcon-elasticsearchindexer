<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

class ElasticsearchIndexer extends \Phalcon\Di\Injectable implements \Phalcon\Events\EventsAwareInterface
{
    /**
     * @var \Phalcon\Events\ManagerInterface
     */
    protected $eventsManager;

    /**
     * @var string
     */
    protected $index;



    /**
     * @param string $index
     *
     * @throws Exception
     */
    public function __construct($index)
    {
        $di = $this->getDI();
        if (!($di instanceof \Phalcon\DiInterface)) {
            throw new Exception("A dependency injection object is required to access internal services");
        }

        $this->index = $index;
    }



    /**
     * @return \Phalcon\Events\ManagerInterface
     */
    public function getEventsManager()
    {
        return $this->eventsManager;
    }

    /**
     * @param \Phalcon\Events\ManagerInterface $eventsManager
     */
    public function setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager)
    {
        $this->eventsManager = $eventsManager;
    }



    /**
     * @param \Phalcon\Mvc\ModelInterface $model
     *
     * @throws Exception
     */
    public function index(\Phalcon\Mvc\ModelInterface $model)
    {
        $eventsManager = $this->getEventsManager();

        if ($eventsManager instanceof \Phalcon\Events\ManagerInterface) {
            $eventsManager->fire("search:beforeIndex", $this);
        }

        $response = $this->elasticsearch->index(
            [
                "index" => $this->index,
                "type"  => $model->getSource(),
                "id"    => $this->getPrimaryKeyValue($model),
                "body"  => $model->toArray()
            ]
        );

        if ($eventsManager instanceof \Phalcon\Events\ManagerInterface) {
            $eventsManager->fire("search:afterIndex", $this);
        }

        return $response;
    }



    /**
     * @param \Phalcon\Mvc\ModelInterface $model
     *
     * @throws Exception
     */
    public function delete(\Phalcon\Mvc\ModelInterface $model)
    {
        $eventsManager = $this->getEventsManager();

        if ($eventsManager instanceof \Phalcon\Events\ManagerInterface) {
            $eventsManager->fire("search:beforeDelete", $this);
        }

        $response = $this->elasticsearch->delete(
            [
                "index" => $this->index,
                "type"  => $model->getSource(),
                "id"    => $this->getPrimaryKeyValue($model)
            ]
        );

        if ($eventsManager instanceof \Phalcon\Events\ManagerInterface) {
            $eventsManager->fire("search:afterDelete", $this);
        }

        return $response;
    }



    /**
     * @param \Phalcon\Mvc\ModelInterface $model
     *
     * @throws Exception
     */
    protected function getPrimaryKeyValue(\Phalcon\Mvc\ModelInterface $model)
    {
        $primaryKeyAttributes = $this->modelsMetadata->getPrimaryKeyAttributes($model);

        if (count($primaryKeyAttributes) != 1) {
            throw new \Sid\Phalcon\ElasticsearchIndexer\Exception("Model does not have a single Primary Key field.");
        }

        return $model->readAttribute($primaryKeyAttributes[0]);
    }
}
