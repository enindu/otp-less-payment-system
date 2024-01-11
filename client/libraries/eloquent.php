<?php

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;

$container->set("eloquent", function(): Manager {
  $manager = new Manager();
  $manager->addConnection([
    "driver"    => $_ENV["eloquent"]["driver"],
    "host"      => $_ENV["eloquent"]["host"],
    "database"  => $_ENV["eloquent"]["database"],
    "username"  => $_ENV["eloquent"]["username"],
    "password"  => $_ENV["eloquent"]["password"],
    "charset"   => $_ENV["eloquent"]["charset"],
    "collation" => $_ENV["eloquent"]["collation"],
    "prefix"    => $_ENV["eloquent"]["prefix"]
  ]);

  $container = new Container();
  $dispatcher = new Dispatcher($container);

  $manager->setEventDispatcher($dispatcher);
  $manager->setAsGlobal();
  $manager->bootEloquent();
  return $manager;
});
