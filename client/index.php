<?php

use DI\Container;
use Slim\Factory\AppFactory;

require_once __DIR__ . "/vendor/autoload.php";

// $ubench = new Ubench();
// $ubench->start();

require_once __DIR__ . "/settings/app.php";
require_once __DIR__ . "/settings/system.php";

$container = new Container();
require_once __DIR__ . "/libraries/carbon.php";
require_once __DIR__ . "/libraries/filesystem.php";
require_once __DIR__ . "/libraries/image.php";
require_once __DIR__ . "/libraries/parsedown.php";
require_once __DIR__ . "/libraries/eloquent.php";
require_once __DIR__ . "/libraries/twig.php";
require_once __DIR__ . "/libraries/swift-mailer.php";
require_once __DIR__ . "/libraries/swift-message.php";
require_once __DIR__ . "/libraries/validation.php";
require_once __DIR__ . "/libraries/guzzle-http.php";

$app = AppFactory::createFromContainer($container);

$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile(__DIR__ . "/cache/routes/cache.php");

require_once __DIR__ . "/app/middleware.php";
require_once __DIR__ . "/app/routes.php";

$app->run();

// $ubench->end();
// file_put_contents(
//   __DIR__ . "/logs/performance.log",
//   "[" . date(DATE_ATOM) . "] " . $_ENV["app"]["name"] . ".DEBUG " . $ubench->getTime() . " | " . $ubench->getMemoryUsage() . "\n",
//   FILE_APPEND
// );
