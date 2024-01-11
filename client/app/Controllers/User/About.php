<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class About extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@user/about.twig");
  }
}
