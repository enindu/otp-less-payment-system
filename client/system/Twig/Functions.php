<?php

namespace System\Twig;

use DI\Container;
use Twig\Extension\AbstractExtension;

class Functions extends AbstractExtension
{
  public function __construct(private Container $container) {}

  public function getFunctions(): array
  {
    return [];
  }
}
