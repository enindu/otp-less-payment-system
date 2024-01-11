<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AdminAuth
{
  public function __construct(private Container $container) {}

  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $path = $request->getUri()->getPath();
    $sessionExists = isset($_SESSION["auth"]["admin"]);
    $cookieExists = isset($request->getCookieParams()[$_ENV["app"]["cookie"]["admin"]]);
    if(!$cookieExists) {
      if($sessionExists) {
        unset($_SESSION["auth"]["admin"]);
      }

      if($path != "/admin/accounts/login" && $path != "/admin/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/admin/accounts/login");
      }

      return $handler->handle($request);
    }

    $eloquent = $this->container->get("eloquent");

    $account = $eloquent->table("admins")->where("status", true)->where("unique_id", $request->getCookieParams()[$_ENV["app"]["cookie"]["admin"]])->first();
    if($account == null) {
      if($sessionExists) {
        unset($_SESSION["auth"]["admin"]);
      }

      setcookie($_ENV["app"]["cookie"]["admin"], "expired", strtotime("now") - 1, "/admin", $_ENV["app"]["domain"], false, true);

      if($path != "/admin/accounts/login" && $path != "/admin/accounts/register") {
        $response = new Response();
        return $response->withHeader("Location", "/admin/accounts/login");
      }

      return $handler->handle($request);
    }

    if($path == "/admin/accounts/login" || $path == "/admin/accounts/register") {
      $response = new Response();
      return $response->withHeader("Location", "/admin");
    }

    $_SESSION["auth"]["admin"] = [
      "id"       => $account->id,
      "role-id"  => $account->role_id,
      "status"   => $account->status,
      "username" => $account->username
    ];

    return $handler->handle($request);
  }
}
