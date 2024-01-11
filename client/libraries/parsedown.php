<?php

$container->set("parsedown", function(): Parsedown {
  $parsedown = new Parsedown();
  $parsedown->setSafeMode($_ENV["parsedown"]["safe-mode"]);
  return $parsedown;
});
