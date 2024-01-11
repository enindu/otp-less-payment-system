<?php

namespace System\Twig;

use DI\Container;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class Globals extends AbstractExtension implements GlobalsInterface
{
  public function __construct(private Container $container) {}

  public function getGlobals(): array
  {
    return [
      "app" => [
        "name"        => $_ENV["app"]["name"],
        "description" => $_ENV["app"]["description"],
        "keywords"    => $_ENV["app"]["keywords"],
        "version"     => $_ENV["app"]["version"],
        "author"      => $_ENV["app"]["author"],
        "domain"      => $_ENV["app"]["domain"],
        "url"         => $_ENV["app"]["url"]
      ],
      "auth" => [
        "admin" => [
          "logged"   => isset($_SESSION["auth"]["admin"]),
          "id"       => $_SESSION["auth"]["admin"]["id"] ?? false,
          "role_id"  => $_SESSION["auth"]["admin"]["role-id"] ?? false,
          "status"   => $_SESSION["auth"]["admin"]["status"] ?? false,
          "username" => $_SESSION["auth"]["admin"]["username"] ?? false
        ],
        "user" => [
          "logged"     => isset($_SESSION["auth"]["user"]),
          "id"         => $_SESSION["auth"]["user"]["id"] ?? false,
          "role_id"    => $_SESSION["auth"]["user"]["role-id"] ?? false,
          "status"     => $_SESSION["auth"]["user"]["status"] ?? false,
          "first_name" => $_SESSION["auth"]["user"]["first-name"] ?? false,
          "last_name"  => $_SESSION["auth"]["user"]["last-name"] ?? false,
          "nic"        => $_SESSION["auth"]["user"]["nic"] ?? false,
          "email"      => $_SESSION["auth"]["user"]["email"] ?? false,
          "phone"      => $_SESSION["auth"]["user"]["phone"] ?? false,
          "account"    => $_SESSION["auth"]["user"]["account"] ?? false,
          "device_id"  => $_SESSION["auth"]["user"]["device-id"] ?? false
        ]
      ],
      "global" => [
        "balance" => $this->getBalance()
      ]
    ];
  }

  private function getBalance(): float|false
  {
    $logged = isset($_SESSION["auth"]["user"]);
    if(!$logged) {
      return false;
    }

    $guzzleHTTP = $this->container->get("guzzle-http");

    $middlewareResponse = $guzzleHTTP->post("https://localhost:5001/api/get-balance", [
      "verify"      => __DIR__ . "/../../sources/certificates/middleware-certificate.pem",
      "http_errors" => false,
      "auth"        => ["client", "password"],
      "json"        => [
        "nic"       => $_SESSION["auth"]["user"]["nic"],
        "email"     => $_SESSION["auth"]["user"]["email"],
        "phone"     => $_SESSION["auth"]["user"]["phone"],
        "account"   => $_SESSION["auth"]["user"]["account"],
        "device-id" => $_SESSION["auth"]["user"]["device-id"]
      ]
    ]);

    $middlewareResponseArray = json_decode((string) $middlewareResponse->getBody(), true);
    if(!$middlewareResponseArray["status"]) {
      return false;
    }

    return (float) reset($middlewareResponseArray["data"]);
  }
}
