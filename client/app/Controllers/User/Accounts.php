<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Accounts extends Controller
{
  public function login(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@user/accounts.login.twig");
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "email"    => "required|email|max:191",
        "password" => "required|min:6|max:32"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $email = trim($inputs["email"]);
      $password = trim($inputs["password"]) . $_ENV["app"]["key"];

      $user = User::where("status", true)->where("email", $email)->first();
      if($user == null) {
        throw new HttpBadRequestException($request, "There is no account found.");
      }

      $passwordMatches = password_verify($password, $user->password);
      if(!$passwordMatches) {
        throw new HttpBadRequestException($request, "Password is invalid.");
      }

      setcookie($_ENV["app"]["cookie"]["user"], $user->unique_id, 0, "/", $_ENV["app"]["domain"], false, true);
      
      return $response->withHeader("Location", "/accounts/profile");
    }
  }

  public function register(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@user/accounts.register.twig");
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $inputValidation = $this->validate($inputs, [
        "first-name"       => "required|alpha|max:191",
        "last-name"        => "required|alpha|max:191",
        "nic"              => "required|alpha_num|min:10|max:12",
        "email"            => "required|email|max:191",
        "phone"            => "required|min:10|max:10",
        "account"          => "required|min:12|max:12",
        "password"         => "required|min:6|max:32",
        "confirm-password" => "required|same:password"
      ]);
      if($inputValidation != null) {
        throw new HttpBadRequestException($request, reset($inputValidation) . ".");
      }

      $firstName = trim($inputs["first-name"]);
      $lastName = trim($inputs["last-name"]);
      $nic = trim($inputs["nic"]);
      $email = trim($inputs["email"]);
      $phone = trim($inputs["phone"]);
      $account = trim($inputs["account"]);
      $password = trim($inputs["password"]) . $_ENV["app"]["key"];

      $user = User::where("nic", $nic)
        ->orWhere("email", $email)
        ->orWhere("phone", $phone)
        ->orWhere("account", $account)
        ->first();
      if($user != null) {
        throw new HttpBadRequestException($request, "There is an account already using that details.");
      }

      $guzzleHTTP = $this->container->get("guzzle-http");

      $middlewareResponse = $guzzleHTTP->post($_ENV["app"]["server"]["middleware"] . "/register", [
        "verify"      => __DIR__ . "/../../../sources/certificates/middleware-certificate.pem",
        "http_errors" => false,
        "auth"        => ["client", "password"],
        "json"        => [
          "first-name" => $firstName,
          "last-name"  => $lastName,
          "nic"        => $nic,
          "email"      => $email,
          "phone"      => $phone,
          "account"    => $account
        ]
      ]);

      $middlewareResponseArray = json_decode((string) $middlewareResponse->getBody(), true);
      if(!$middlewareResponseArray["status"]) {
        throw new HttpBadRequestException($request, reset($middlewareResponseArray["data"]) . ".");
      }

      $carbon = $this->container->get("carbon");

      User::insert([
        "role_id"    => 2,
        "unique_id"  => md5(uniqid(bin2hex(random_bytes(32)))),
        "status"     => false,
        "first_name" => $firstName,
        "last_name"  => $lastName,
        "nic"        => $nic,
        "email"      => $email,
        "phone"      => $phone,
        "account"    => $account,
        "device_id"  => reset($middlewareResponseArray["data"]),
        "password"   => password_hash($password, PASSWORD_BCRYPT),
        "created_at" => $carbon::now(),
        "updated_at" => $carbon::now()
      ]);

      return $response->withHeader("Location", "/accounts/login");
    }
  }

  public function logout(Request $request, Response $response, array $data): Response
  {
    setcookie($_ENV["app"]["cookie"]["user"], "expired", strtotime("now") - 1, "/", $_ENV["app"]["domain"], false, true);
    return $response->withHeader("Location", "/accounts/login");
  }

  public function changeInformation(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "first-name"       => "required|max:191",
      "last-name"        => "required|max:191",
      "current-password" => "required|min:6|max:32"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $firstName = trim($inputs["first-name"]);
    $lastName = trim($inputs["last-name"]);
    $currentPassword = trim($inputs["current-password"]) . $_ENV["app"]["key"];

    $user = User::where("id", $this->auth("id", "user"))->first();
    $currentPasswordMatches = password_verify($currentPassword, $user->password);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, "Current password is invalid.");
    }

    $user->first_name = $firstName;
    $user->last_name = $lastName;
    $user->save();

    return $response->withHeader("Location", "/accounts/settings");
  }

  public function changePassword(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "current-password"     => "required|min:6|max:32",
      "new-password"         => "required|different:current-password|min:6|max:32",
      "confirm-new-password" => "same:new-password"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $currentPassword = trim($inputs["current-password"]) . $_ENV["app"]["key"];
    $newPassword = trim($inputs["new-password"]) . $_ENV["app"]["key"];

    $user = User::where("id", $this->auth("id", "user"))->first();
    $currentPasswordMatches = password_verify($currentPassword, $user->password);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, "Current password is invalid.");
    }

    setcookie($_ENV["app"]["cookie"]["user"], "expired", strtotime("now") - 1, "/", $_ENV["app"]["domain"], false, true);

    $user->unique_id = md5(uniqid(bin2hex(random_bytes(32))));
    $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
    $user->save();

    return $response->withHeader("Location", "/accounts/login");
  }

  public function profile(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@user/accounts.profile.twig", [
      "advertisement" => Image::where("section_id", 4)->inRandomOrder()->first()
    ]);
  }

  public function settings(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@user/accounts.settings.twig");
  }
}
