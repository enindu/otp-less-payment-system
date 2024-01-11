<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Categories extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/categories.twig", [
      "sections"   => Section::get(),
      "categories" => Category::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Category::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }
    
    return $this->view($response, "@admin/categories.all.twig", [
      "page"       => $page,
      "pages"      => $pages,
      "categories" => Category::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "title"      => "required|max:191",
      "subtitle"   => "max:191",
      "section-id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $title = trim($inputs["title"]);
    $subtitle = trim($inputs["subtitle"]);
    $sectionID = (int) trim($inputs["section-id"]);
    $description = trim($inputs["description"]);

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $carbon = $this->container->get("carbon");

    Category::insert([
      "section_id"  => $sectionID,
      "slug"        => strtolower(uniqid(str_replace([" ", "/", "\\", "'", "\""], "-", str_replace(["(", ")", "[", "]", "{", "}", ",", "."], "", $title)) . "-")),
      "title"       => $title,
      "subtitle"    => $subtitle != "" ? $subtitle : "false",
      "description" => $description != "" ? $description : "false",
      "created_at"  => $carbon::now(),
      "updated_at"  => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/categories");
  }

  public function update(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id"         => "required|integer",
      "title"      => "required|max:191",
      "subtitle"   => "max:191",
      "section-id" => "required|integer"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);
    $title = trim($inputs["title"]);
    $subtitle = trim($inputs["subtitle"]);
    $sectionID = (int) trim($inputs["section-id"]);
    $description = trim($inputs["description"]);

    $category = Category::where("id", $id)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, "There is no category found.");
    }

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $category->section_id = $sectionID;
    $category->slug = strtolower(uniqid(str_replace([" ", "/", "\\", "\'", "\""], "-", str_replace(["(", ")", "[", "]", "{", "}", ",", "."], "", $title)) . "-"));
    $category->title = $title;
    $category->subtitle = $subtitle != "" ? $subtitle : "false";
    $category->description = $description != "" ? $description : "false";
    $category->save();

    return $response->withHeader("Location", "/admin/categories/" . $id);
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

    $category = Category::where("id", $id)->first();
    if($category == null) {
      throw new HttpBadRequestException($request, "There is no category found.");
    }

    $category->delete();

    return $response->withHeader("Location", "/admin/categories");
  }

  public function single(Request $request, Response $response, array $data): Response
  {
    $category = Category::where("id", $data["id"])->first();
    if($category == null) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/categories.single.twig", [
      "category" => $category,
      "sections" => Section::get()
    ]);
  }
}
