<?php

use Carbon\Carbon;

$container->set("carbon", function(): Carbon {
  return new Carbon();
});
