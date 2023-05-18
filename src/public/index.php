<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config;
use Phalcon\Mvc\Router;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/apps');

require_once BASE_PATH . '/vendor/autoload.php';



// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'router',
    function () {
        $router = new Router();

        $router->setDefaultModule('front');
        $router->add(
            '/admin/:controller/:action/:params',
            [
                'module'     => 'admin',
                'controller' => 1,
                'action'     => 2,
                'params'     => 3,
            ]
        );
        return $router;
    }
);

$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );

        return $mongo->store;
    },
    true
);

$container->set(
    'log',
    function () {

        $adapter = new Stream(APP_PATH . '/storage/logs/main.log');
        return new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );
    },
    true
);


$application = new Application($container);

$application->registerModules(
    [
        'front' => [
            'className' => \Multi\Front\Module::class,
            'path'      => APP_PATH . '/front/Module.php',
        ],
        'admin'  => [
            'className' => \Multi\Admin\Module::class,
            'path'      => APP_PATH . '/admin/Module.php',
        ]
    ]
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
