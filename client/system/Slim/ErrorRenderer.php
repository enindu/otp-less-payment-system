<?php

namespace System\Slim;

use DI\Container;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface
{
  public function __construct(private Container $container)
  {
    $this->container->get("eloquent");
  }

  public function __invoke(Throwable $throwable, bool $displayErrorDetails): string
  {
    $twig = $this->container->get("twig");

    return $twig->render("@common/error.twig", [
      "code"    => $throwable->getCode(),
      "message" => $throwable->getMessage(),
      "file"    => $throwable->getFile(),
      "line"    => $throwable->getLine(),
      "traces"  => $throwable->getTrace()
    ]);
  }
}
