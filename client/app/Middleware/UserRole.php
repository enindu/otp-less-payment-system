<?php

namespace App\Middleware;

use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class UserRole
{
  public function __construct(private Container $container, private array $roles) {}

  public function __invoke(Request $request, RequestHandlerInterface $handler): Response
  {
    $eloquent = $this->container->get("eloquent");

    $roleID = $eloquent->table("users")->where("unique_id", $request->getCookieParams()[$_ENV["app"]["cookie"]["user"]])->value("role_id");
    $roleExists = array_search($roleID, $this->roles);
    if($roleExists === false) {
      throw new HttpForbiddenException($request);
    }

    return $handler->handle($request);
  }
}
