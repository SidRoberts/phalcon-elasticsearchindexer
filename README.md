Sid\Phalcon\ElasticsearchIndexer
================================

Elasticsearch indexer component for Phalcon.



## Installing

Install using Composer:

```
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

        $eventsManager->attach("model", new \Sid\Phalcon\ElasticsearchIndexer\Event());

        $modelsManager->setEventsManager($eventsManager);

        return $modelsManager;
    },
    true
);

$di->set(
    "elasticsearch",
    function () {
        //FIXME Change these accordingly.
        $host = "127.0.0.1";
        $port = "9200";
        
        $elasticsearch = new \Elasticsearch\Client(
            [
                "hosts" => [
                    $host . ":" . $port
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

That's it! Whenever a Model is saved (created/updated) or deleted, it will be reflected in Elasticsearch at the same time.
