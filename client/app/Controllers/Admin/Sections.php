<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Sections extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/sections.twig", [
      "sections" => Section::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Section::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/sections.all.twig", [
      "page"     => $page,
      "pages"    => $pages,
      "sections" => Section::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
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

    $section = Section::where("title", $title)->first();
    if($section != null) {
      throw new HttpBadRequestException($request, "There is a section already using that name.");
    }

    $carbon = $this->container->get("carbon");

    Section::insert([
      "title"      => $title,
      "created_at" => $carbon::now(),
      "updated_at" => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/sections");
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

    $section = Section::where("id", $id)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $section->delete();

    return $response->withHeader("Location", "/admin/sections");
  }
}
