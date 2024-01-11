<?php

namespace System\Twig;

use DI\Container;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Filters extends AbstractExtension
{
  public function __construct(private Container $container) {}

  public function getFilters(): array
  {
    return [
      new TwigFilter("content", [$this, "content"]),
      new TwigFilter("limit", [$this, "limit"]),
      new TwigFilter("markdown", [$this, "markdown"])
    ];
  }

  public function content(string $file): string
  {
    $content = file_get_contents($file);
    if(!$content) {
      throw new RuntimeError("Cannot get content from " . $file . " file");
    }

    return $content;
  }

  public function limit(string $text, int $length = 100): string
  {
    $currentLength = strlen($text);
    if($currentLength < $length) {
      return $text;
    }

    return substr($text, 0, $length - 3) . "...";
  }

  public function markdown(string $text): string
  {
    $parsedown = $this->container->get("parsedown");
    return $parsedown->text($text);
  }
}
