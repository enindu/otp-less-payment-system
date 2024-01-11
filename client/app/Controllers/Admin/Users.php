<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Users extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/users.twig", [
      "roles" => Role::get(),
      "users" => User::orderBy("id", "desc")->take(10)->get()
    ]);
  }

  public function all(Request $request, Response $response, array $data): Response
  {
    $parameters = $request->getQueryParams();
    $validation = $this->validate($parameters, [
      "page" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpNotFoundException($request);
    }

    $page = (int) $parameters["page"];

    $results = count(User::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/users.all.twig", [
      "page"  => $page,
      "pages" => $pages,
      "users" => User::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "first-name"       => "required|max:191",
      "last-name"        => "required|max:191",
      "role-id"          => "required|max:191",
      "email"            => "required|email|max:191",
      "phone"            => "required|min:10|max:10",
      "password"         => "required|min:6|max:32",
      "confirm-password" => "required|same:password"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $firstName = trim($inputs["first-name"]);
    $lastName = trim($inputs["last-name"]);
    $roleID = (int) trim($inputs["role-id"]);
    $email = trim($inputs["email"]);
    $phone = trim($inputs["phone"]);
    $password = trim($inputs["password"]) . $_ENV["app"]["key"];

    $role = Role::where("id", $roleID)->first();
    if($role == null) {
      throw new HttpBadRequestException($request, "There is no role found.");
    }

    $user = User::where("email", $email)->orWhere("phone", $phone)->first();
    if($user != null) {
      throw new HttpBadRequestException($request, "There is an account already using that email or phone.");
    }

    $carbon = $this->container->get("carbon");

    User::insert([
      "role_id"    => $roleID,
      "unique_id"  => md5(uniqid(bin2hex(random_bytes(32)))),
      "first_name" => $firstName,
      "last_name"  => $lastName,
      "email"      => $email,
      "phone"      => $phone,
      "password"   => password_hash($password, PASSWORD_BCRYPT),
      "created_at" => $carbon::now(),
      "updated_at" => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/users");
  }

  public function activate(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);

    $user = User::where("id", $id)->first();
    if($user == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    $user->status = true;
    $user->save();

    return $response->withHeader("Location", "/admin/users");
  }

  public function deactivate(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);

    $user = User::where("id", $id)->first();
    if($user == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    $user->status = false;
    $user->save();

    return $response->withHeader("Location", "/admin/users");
  }

  public function remove(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);

    $user = User::where("id", $id)->first();
    if($user == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    $user->delete();

    return $response->withHeader("Location", "/admin/users");
  }
}
