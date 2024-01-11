<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Reports extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    $carbon = $this->container->get("carbon");
    
    return $this->view($response, "@admin/reports.twig", [
      "users" => User::whereBetween("created_at", [$carbon::parse("first day of this month")->startOfDay(), $carbon::now()])->orderBy("id", "desc")->take(10)->get()
    ]);
  }
}
