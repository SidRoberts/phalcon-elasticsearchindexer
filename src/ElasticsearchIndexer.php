<?php

namespace Sid\Phalcon\ElasticsearchIndexer;

use Phalcon\Di\DiInterface;
use Phalcon\Di\Injectable;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;
use Phalcon\Mvc\ModelInterface;

class ElasticsearchIndexer extends Injectable implements EventsAwareInterface
{
    protected ?EventsManagerInterface $eventsManager;

    protected string $index;



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



    public function getEventsManager(): ?EventsManagerInterface
    {
        return $this->eventsManager;
    }

    public function setEventsManager(EventsManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }



    /**
     * @throws Exception
     */
    public function index(ModelInterface $model): array
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
                "body"  => $model->toArray(),
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
    public function delete(ModelInterface $model): array
    {
        $eventsManager = $this->getEventsManager();

        if ($eventsManager instanceof EventsManagerInterface) {
            $eventsManager->fire("search:beforeDelete", $this);
        }

        $response = $this->elasticsearch->delete(
            [
                "index" => $this->index,
                "type"  => $model->getSource(),
                "id"    => $this->getPrimaryKeyValue($model),
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
    protected function getPrimaryKeyValue(ModelInterface $model): mixed
    {
        $primaryKeyAttributes = $this->modelsMetadata->getPrimaryKeyAttributes($model);

        if (count($primaryKeyAttributes) !== 1) {
            throw new Exception(
                "Model does not have a single Primary Key field."
            );
        }

        $primaryKeyAttribute = $primaryKeyAttributes[0];

        return $model->readAttribute($primaryKeyAttribute);
    }
}
