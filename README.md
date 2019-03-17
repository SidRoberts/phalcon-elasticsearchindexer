Sid\Phalcon\ElasticsearchIndexer
================================

Elasticsearch indexer component for Phalcon.



## Installing

Install using Composer:

```json
{
    "require": {
        "sidroberts/phalcon-elasticsearchindexer": "dev-master"
    }
}
```

Add the following to your DI:

```php
$di->set(
    "modelsManager",
    function () use ($di) {
        $modelsManager = new \Phalcon\Mvc\Model\Manager();

        $eventsManager = $di->getShared("eventsManager");

        // If this is false, only models that implement \Sid\Phalcon\ElasticsearchIndexer\IndexInterface will be indexed.
        // By default, this is true.
        $indexAllModels = true;

        $eventsManager->attach(
            "model",
            new \Sid\Phalcon\ElasticsearchIndexer\Event($indexAllModels)
        );

        $modelsManager->setEventsManager($eventsManager);

        return $modelsManager;
    },
    true
);

$di->set(
    "elasticsearch",
    function () {
        $elasticsearch = new \Elasticsearch\Client(
            [
                "hosts" => [
                    //FIXME Change this accordingly.
                    "127.0.0.1:9200",
                ],
            ]
        );

        return $elasticsearch;
    },
    true
);

$di->set(
    "elasticsearchIndexer",
    function () {
        //FIXME Change these accordingly.
        $index = "db";
        
        $elasticsearchIndexer = new \Sid\Phalcon\ElasticsearchIndexer\ElasticsearchIndexer(
            $index
        );

        return $elasticsearchIndexer;
    },
    true
);
```

That's it!
Whenever a Model is saved (created/updated) or deleted, it will be reflected in Elasticsearch at the same time.

Currently, this library only supports models with a single field primary key.
