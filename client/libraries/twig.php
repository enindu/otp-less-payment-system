<?php

use System\Twig\Filters;
use System\Twig\Functions;
use System\Twig\Globals;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

$container->set("twig", function() use ($container): Environment {
  $filesystemLoader = new FilesystemLoader();
  $filesystemLoader->addPath(__DIR__ . "/../resources/admin/views", "admin");
  $filesystemLoader->addPath(__DIR__ . "/../resources/admin/assets", "admin_assets");
  $filesystemLoader->addPath(__DIR__ . "/../resources/user/views", "user");
  $filesystemLoader->addPath(__DIR__ . "/../resources/user/assets", "user_assets");
  $filesystemLoader->addPath(__DIR__ . "/../resources/common/views", "common");
  $filesystemLoader->addPath(__DIR__ . "/../resources/common/assets", "common_assets");

  $environment = new Environment($filesystemLoader, [
    "debug"               => $_ENV["twig"]["debug"],
    "charset"             => $_ENV["twig"]["charset"],
    "base_template_class" => $_ENV["twig"]["base-template-class"],
    "cache"               => $_ENV["twig"]["cache"],
    "auto_reload"         => $_ENV["twig"]["auto-reload"],
    "strict_variables"    => $_ENV["twig"]["strict-variables"],
    "autoescape"          => $_ENV["twig"]["auto-escape"],
    "optimizations"       => $_ENV["twig"]["optimizations"]
  ]);
  
  $globals = new Globals($container);
  $functions = new Functions($container);
  $filters = new Filters($container);
  $intlExtension = new IntlExtension();
  
  $environment->addExtension($globals);
  $environment->addExtension($functions);
  $environment->addExtension($filters);
  $environment->addExtension($intlExtension);
  return $environment;
});
