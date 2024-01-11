<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Accounts extends Controller
{
  public function login(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@admin/accounts.login.twig");
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "username" => "required|max:6",
        "password" => "required|min:6|max:32"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $username = trim($inputs["username"]);
      $password = trim($inputs["password"]) . $_ENV["app"]["key"];

      $admin = Admin::where("status", true)->where("username", $username)->first();
      if($admin == null) {
        throw new HttpBadRequestException($request, "There is no account found.");
      }

      $passwordMatches = password_verify($password, $admin->password);
      if(!$passwordMatches) {
        throw new HttpBadRequestException($request, "Password is invalid.");
      }

      setcookie($_ENV["app"]["cookie"]["admin"], $admin->unique_id, 0, "/admin", $_ENV["app"]["domain"], false, true);
      
      return $response->withHeader("Location", "/admin");
    }
  }

  public function register(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@admin/accounts.register.twig", [
        "roles" => Role::get()
      ]);
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "username"         => "required|max:6",
        "role-id"          => "required|integer",
        "password"         => "required|min:6|max:32",
        "confirm-password" => "same:password"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $username = trim($inputs["username"]);
      $roleID = (int) trim($inputs["role-id"]);
      $password = trim($inputs["password"]) . $_ENV["app"]["key"];

      $role = Role::where("id", $roleID)->first();
      if($role == null) {
        throw new HttpBadRequestException($request, "There is no role found.");
      }

      $admin = Admin::where("username", $username)->first();
      if($admin != null) {
        throw new HttpBadRequestException($request, "There is an account already using that username.");
      }

      $carbon = $this->container->get("carbon");

      Admin::insert([
        "role_id"    => $roleID,
        "unique_id"  => md5(uniqid(bin2hex(random_bytes(32)))),
        "username"   => $username,
        "password"   => password_hash($password, PASSWORD_BCRYPT),
        "created_at" => $carbon::now(),
        "updated_at" => $carbon::now()
      ]);

      return $response->withHeader("Location", "/admin/accounts/login");
    }
  }

  public function logout(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@admin/accounts.logout.twig");
    }
    if($method == "POST") {
      setcookie($_ENV["app"]["cookie"]["admin"], "expired", strtotime("now") - 1, "/admin", $_ENV["app"]["domain"], false, true);

      return $response->withHeader("Location", "/admin/accounts/login");
    }
  }

  public function changeInformation(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "username"         => "required|max:6",
      "current-password" => "required|min:6|max:32"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $username = trim($inputs["username"]);
    $currentPassword = trim($inputs["current-password"]) . $_ENV["app"]["key"];

    $adminWithID = Admin::where("id", $this->auth("id", "admin"))->first();
    $currentPasswordMatches = password_verify($currentPassword, $adminWithID->password);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, "Current password is invalid.");
    }

    $adminWithUsername = Admin::where("username", $username)->first();
    if($adminWithUsername != null && $adminWithUsername->id != $adminWithID->id) {
      throw new HttpBadRequestException($request, "There is an account already using that username.");
    }

    $adminWithID->username = $username;
    $adminWithID->save();

    return $response->withHeader("Location", "/admin/accounts/profile");
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

    $admin = Admin::where("id", $this->auth("id", "admin"))->first();
    $currentPasswordMatches = password_verify($currentPassword, $admin->password);
    if(!$currentPasswordMatches) {
      throw new HttpBadRequestException($request, "Current password is invalid.");
    }

    setcookie($_ENV["app"]["cookie"]["admin"], "expired", strtotime("now") - 1, "/admin", $_ENV["app"]["domain"], false, true);

    $admin->unique_id = md5(uniqid(bin2hex(random_bytes(32))));
    $admin->password = password_hash($newPassword, PASSWORD_BCRYPT);
    $admin->save();

    return $response->withHeader("Location", "/admin/accounts/login");
  }

  public function profile(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/accounts.profile.twig", [
      "admin" => Admin::where("id", $this->auth("id", "admin"))->first()
    ]);
  }
}
