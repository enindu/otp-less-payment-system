<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Models\Category;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use function DI\string;

class Actions extends Controller
{
  public function transferMoney(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@user/actions.transfer-money.twig", [
        "types" => Category::where("section_id", 1)->get(),
        "banks" => Category::where("section_id", 5)->orderBy("id", "asc")->get()
      ]);
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "type-id"         => "required|integer",
        "bank-id"         => "required|integer",
        "amount"          => "required|numeric|min:100|max:1000000",
        "account"         => "required|min:12|max:12",
        "description"     => "max:500"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $typeID = (int) trim($inputs["type-id"]);
      $amount = (float) trim($inputs["amount"]);
      $account = trim($inputs["account"]);
      $description = trim($inputs["description"]);

      $type = Category::where("section_id", 1)->where("id", $typeID)->first();
      if($type == null) {
        throw new HttpBadRequestException($request, "There is no transfer type found.");
      }

      if($type->subtitle != "IBFT") {
        $senderAccount = $this->auth("account", "user");
        if($senderAccount == $account) {
          throw new HttpBadRequestException($request, "You cannot transfer money to same account.");
        }
      }

      $guzzleHTTP = $this->container->get("guzzle-http");

      $authenticationResponse = $guzzleHTTP->post($_ENV["app"]["server"]["authentication"] . "/generate-key", [
        "verify"      => __DIR__ . "/../../../sources/certificates/authentication-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => ["device-id" => $this->auth("device-id", "user")]
      ]);
      
      $authenticationResponseArray = json_decode((string) $authenticationResponse->getBody(), true);
      if(!$authenticationResponseArray["status"]) {
        throw new HttpBadRequestException($request, $authenticationResponseArray["code"] . ": " . reset($authenticationResponseArray["data"]) . ".");
      }

      $middlewareURL = $type->subtitle == "IBFT" ? $_ENV["app"]["server"]["middleware"] . "/transfer-money-other-bank" : $_ENV["app"]["server"]["middleware"] . "/transfer-money-same-bank";
      $middlewareResponse = $guzzleHTTP->post($middlewareURL, [
        "verify"      => __DIR__ . "/../../../sources/certificates/middleware-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => [
          "sender-nic"       => $this->auth("nic", "user"),
          "sender-email"     => $this->auth("email", "user"),
          "sender-phone"     => $this->auth("phone", "user"),
          "sender-account"   => $this->auth("account", "user"),
          "sender-device-id" => $this->auth("device-id", "user"),
          "receiver-account" => $account,
          "middleware-token" => reset($authenticationResponseArray["data"]),
          "type"             => $type->subtitle,
          "amount"           => $amount,
          "description"      => $description
        ]
      ]);

      $middlewareResponseArray = json_decode((string) $middlewareResponse->getBody(), true);
      if(!$middlewareResponseArray["status"]) {
        throw new HttpBadRequestException($request, $middlewareResponseArray["code"] . ": " . reset($middlewareResponseArray["data"]) . ".");
      }

      return $this->view($response, "@common/message.twig", [
        "message" => "Transaction completed, reference number is " . reset($middlewareResponseArray["data"]) . "."
      ]);
    }
  }

  public function payOnline(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@user/actions.pay-online.twig", [
        "payees" => Category::where("section_id", 2)->orderBy("title", "asc")->get()
      ]);
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "payee-id"        => "required|integer",
        "amount"          => "required|numeric|min:100|max:1000000",
        "description"     => "required|max:500"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $payeeID = (int) trim($inputs["payee-id"]);
      $amount = (float) trim($inputs["amount"]);
      $description = trim($inputs["description"]);

      $payee = Category::where("section_id", 2)->where("id", $payeeID)->first();
      if($payee == null) {
        throw new HttpBadRequestException($request, "There is no payee found.");
      }

      $guzzleHTTP = $this->container->get("guzzle-http");

      $authenticationResponse = $guzzleHTTP->post($_ENV["app"]["server"]["authentication"] . "/generate-key", [
        "verify"      => __DIR__ . "/../../../sources/certificates/authentication-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => ["device-id" => $this->auth("device-id", "user")]
      ]);

      $authenticationResponseArray = json_decode((string) $authenticationResponse->getBody(), true);
      if(!$authenticationResponseArray["status"]) {
        throw new HttpBadRequestException($request, $authenticationResponseArray["code"] . ": " . reset($authenticationResponseArray["data"]) . ".");
      }

      $middlewareResponse = $guzzleHTTP->post($_ENV["app"]["server"]["middleware"] . "/transfer-money-same-bank", [
        "verify"      => __DIR__ . "/../../../sources/certificates/middleware-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => [
          "sender-nic"       => $this->auth("nic", "user"),
          "sender-email"     => $this->auth("email", "user"),
          "sender-phone"     => $this->auth("phone", "user"),
          "sender-account"   => $this->auth("account", "user"),
          "sender-device-id" => $this->auth("device-id", "user"),
          "receiver-account" => $payee->subtitle,
          "middleware-token" => reset($authenticationResponseArray["data"]),
          "type"             => "EPAY",
          "amount"           => $amount,
          "description"      => $payee->title . " - " . $description
        ]
      ]);

      $middlewareResponseArray = json_decode((string) $middlewareResponse->getBody(), true);
      if(!$middlewareResponseArray["status"]) {
        throw new HttpBadRequestException($request, $middlewareResponseArray["code"] . ": " . reset($middlewareResponseArray["data"]) . ".");
      }

      return $this->view($response, "@common/message.twig", [
        "message" => "Transaction completed, reference number is " . reset($middlewareResponseArray["data"]) . "."
      ]);
    }
  }

  public function rechargePhone(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@user/actions.recharge-phone.twig", [
        "service_providers" => Category::where("section_id", 3)->orderBy("title", "asc")->get()
      ]);
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "service-provider-id" => "required|integer",
        "phone"               => "required|min:10|max:10",
        "amount"              => "required|numeric|min:10|max:10000"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $serviceProviderID = (int) trim($inputs["service-provider-id"]);
      $phone = trim($inputs["phone"]);
      $amount = (float) trim($inputs["amount"]);

      $serviceProvider = Category::where("section_id", 3)->where("id", $serviceProviderID)->first();
      if($serviceProvider == null) {
        throw new HttpBadRequestException($request, "There is no service provider found.");
      }

      $guzzleHTTP = $this->container->get("guzzle-http");

      $authenticationResponse = $guzzleHTTP->post($_ENV["app"]["server"]["authentication"] . "/generate-key", [
        "verify"      => __DIR__ . "/../../../sources/certificates/authentication-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => ["device-id" => $this->auth("device-id", "user")]
      ]);

      $authenticationResponseArray = json_decode((string) $authenticationResponse->getBody(), true);
      if(!$authenticationResponseArray["status"]) {
        throw new HttpBadRequestException($request, $authenticationResponseArray["code"] . ": " . reset($authenticationResponseArray["data"]) . ".");
      }

      $middlewareResponse = $guzzleHTTP->post($_ENV["app"]["server"]["middleware"] . "/transfer-money-same-bank", [
        "verify"      => __DIR__ . "/../../../sources/certificates/middleware-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => [
          "sender-nic"       => $this->auth("nic", "user"),
          "sender-email"     => $this->auth("email", "user"),
          "sender-phone"     => $this->auth("phone", "user"),
          "sender-account"   => $this->auth("account", "user"),
          "sender-device-id" => $this->auth("device-id", "user"),
          "receiver-account" => $serviceProvider->subtitle,
          "middleware-token" => reset($authenticationResponseArray["data"]),
          "type"             => "EPAY",
          "amount"           => $amount,
          "description"      => $serviceProvider->title . " - " . $phone
        ]
      ]);

      $middlewareResponseArray = json_decode((string) $middlewareResponse->getBody(), true);
      if(!$middlewareResponseArray["status"]) {
        throw new HttpBadRequestException($request, $middlewareResponseArray["code"] . ": " . reset($middlewareResponseArray["data"]) . ".");
      }

      return $this->view($response, "@common/message.twig", [
        "message" => "Transaction completed, reference number is " . reset($middlewareResponseArray["data"]) . "."
      ]);
    }
  }

  public function exchangeRates(Request $request, Response $response, array $data): Response
  {
    $guzzleHTTP = $this->container->get("guzzle-http");

    $apiResponse = $guzzleHTTP->get($_ENV["app"]["server"]["exchange-rates"], [
      "http_errors" => false
    ]);

    $apiResponseArray = json_decode((string) $apiResponse->getBody(), true);
    return $this->view($response, "@user/actions.exchange-rates.twig", [
      "usd" => 1 / (float) $apiResponseArray["rates"]["USD"],
      "eur" => 1 / (float) $apiResponseArray["rates"]["EUR"],
      "gbp" => 1 / (float) $apiResponseArray["rates"]["GBP"],
      "sar" => 1 / (float) $apiResponseArray["rates"]["SAR"],
      "inr" => 1 / (float) $apiResponseArray["rates"]["INR"],
      "aud" => 1 / (float) $apiResponseArray["rates"]["AUD"]
    ]);
  }

  public function transactions(Request $request, Response $response, array $data): Response
  {
    $guzzleHTTP = $this->container->get("guzzle-http");

    $middlewareResponse = $guzzleHTTP->post($_ENV["app"]["server"]["middleware"] . "/get-transactions", [
      "verify"      => __DIR__ . "/../../../sources/certificates/middleware-certificate.pem",
      "http_errors" => false,
      "auth"        => ["client", "password"],
      "json"        => [
        "nic"       => $this->auth("nic", "user"),
        "email"     => $this->auth("email", "user"),
        "phone"     => $this->auth("phone", "user"),
        "account"   => $this->auth("account", "user"),
        "device-id" => $this->auth("device-id", "user")
      ]
    ]);

    $middlewareResponseArray = json_decode((string) $middlewareResponse->getBody(), true);
    if(!$middlewareResponseArray["status"]) {
      throw new HttpBadRequestException($request, $middlewareResponseArray["code"] . ": " . reset($middlewareResponseArray["data"]) . ".");
    }

    return $this->view($response, "@user/actions.transactions.twig", [
      "transactions" => $middlewareResponseArray["data"]
    ]);
  }
}
