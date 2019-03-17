<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

use Phalcon\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Mvc\ModelInterface;

class ElasticsearchIndexer extends Injectable implements EventsAwareInterface
{
    /**
     * @var EventsManagerInterface
     */
    protected $eventsManager;

    /**
     * @var string
     */
    protected $index;



    /**
     * @throws Exception
     */
    public function __construct(string $index)
    {
        $di = $this->getDI();

        if (!($di instanceof DiInterface)) {
            throw new Exception(
                "A dependency injection object is required to access internal services"
            );
        }

        $this->index = $index;
    }



    public function getEventsManager() : EventsManagerInterface
    {
        return $this->eventsManager;
    }

    public function setEventsManager(EventsManagerInterface $eventsManager)
    {
        $this->eventsManager = $eventsManager;
    }



    /**
     * @throws Exception
     */
    public function index(ModelInterface $model)
    {
        $eventsManager = $this->getEventsManager();

        if ($eventsManager instanceof EventsManagerInterface) {
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

        if ($eventsManager instanceof EventsManagerInterface) {
            $eventsManager->fire("search:afterIndex", $this);
        }

        return $response;
    }



    /**
     * @throws Exception
     */
    public function delete(ModelInterface $model)
    {
        $eventsManager = $this->getEventsManager();

        if ($eventsManager instanceof EventsManagerInterface) {
            $eventsManager->fire("search:beforeDelete", $this);
        }

        $response = $this->elasticsearch->delete(
            [
                "index" => $this->index,
                "type"  => $model->getSource(),
                "id"    => $this->getPrimaryKeyValue($model)
            ]
        );

        if ($eventsManager instanceof EventsManagerInterface) {
            $eventsManager->fire("search:afterDelete", $this);
        }

        return $response;
    }



    /**
     * @throws Exception
     */
    protected function getPrimaryKeyValue(ModelInterface $model)
    {
        $primaryKeyAttributes = $this->modelsMetadata->getPrimaryKeyAttributes($model);

        if (count($primaryKeyAttributes) != 1) {
            throw new Exception(
                "Model does not have a single Primary Key field."
            );
        }

        return $model->readAttribute($primaryKeyAttributes[0]);
    }
}
