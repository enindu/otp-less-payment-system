<?php

use Symfony\Component\Filesystem\Filesystem;

$container->set("filesystem", function(): Filesystem {
  return new Filesystem();
});
