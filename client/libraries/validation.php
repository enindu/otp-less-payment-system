<?php

use Rakit\Validation\Validator;

$container->set("validation", function(): Validator {
  return new Validator();
});
