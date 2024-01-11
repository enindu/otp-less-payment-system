<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Role;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Roles extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/roles.twig", [
      "roles" => Role::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Role::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }
    
    return $this->view($response, "@admin/roles.all.twig", [
      "page"  => $page,
      "pages" => $pages,
      "roles" => Role::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "title" => "required|max:191"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $title = trim($inputs["title"]);

    $role = Role::where("title", $title)->first();
    if($role != null) {
      throw new HttpBadRequestException($request, "There is a role already using that title.");
    }

    $carbon = $this->container->get("carbon");

    Role::insert([
      "title"      => $title,
      "created_at" => $carbon::now(),
      "updated_at" => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/roles");
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

    $role = Role::where("id", $id)->first();
    if($role == null) {
      throw new HttpBadRequestException($request, "There is no role found.");
    }

    $role->delete();

    return $response->withHeader("Location", "/admin/roles");
  }
}
