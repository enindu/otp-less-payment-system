<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Admins extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/admins.twig", [
      "roles"  => Role::get(),
      "admins" => Admin::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Admin::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/admins.all.twig", [
      "page"   => $page,
      "pages"  => $pages,
      "admins" => Admin::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "username"         => "required|max:6",
      "role-id"          => "required|integer",
      "password"         => "required|min:6|max:32",
      "confirm-password" => "required|same:password"
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

    return $response->withHeader("Location", "/admin/admins");
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

    $admin = Admin::where("id", $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    if($id == $this->auth("id", "admin")) {
      throw new HttpBadRequestException($request, "You cannot activate your own account.");
    }

    $admin->status = true;
    $admin->save();

    return $response->withHeader("Location", "/admin/admins");
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

    $admin = Admin::where("id", $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    if($id == $this->auth("id", "admin")) {
      throw new HttpBadRequestException($request, "You cannot deactivate your own account.");
    }

    $admin->status = false;
    $admin->save();

    return $response->withHeader("Location", "/admin/admins");
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

    $admin = Admin::where("id", $id)->first();
    if($admin == null) {
      throw new HttpBadRequestException($request, "There is no account found.");
    }

    if($id == $this->auth("id", "admin")) {
      throw new HttpBadRequestException($request, "You cannot remove your own account.");
    }

    $admin->delete();

    return $response->withHeader("Location", "/admin/admins");
  }
}
