<?php

use GuzzleHttp\Client;

$container->set("guzzle-http", function(): Client {
  return new Client();
});
