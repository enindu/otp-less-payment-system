<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Content;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Contents extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/contents.twig", [
      "sections" => Section::get(),
      "contents" => Content::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Content::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }
    
    return $this->view($response, "@admin/contents.all.twig", [
      "page"     => $page,
      "pages"    => $pages,
      "contents" => Content::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "title"       => "required|max:191",
      "subtitle"    => "max:191",
      "section-id"  => "required|integer",
      "description" => "required"
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

    Content::insert([
      "section_id"  => $sectionID,
      "title"       => $title,
      "subtitle"    => $subtitle != "" ? $subtitle : "false",
      "description" => $description,
      "created_at"  => $carbon::now(),
      "updated_at"  => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/contents");
  }

  public function update(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $validation = $this->validate($inputs, [
      "id"          => "required|integer",
      "title"       => "required|max:191",
      "subtitle"    => "max:191",
      "section-id"  => "required|integer",
      "description" => "required"
    ]);
    if($validation != null) {
      throw new HttpBadRequestException($request, reset($validation) . ".");
    }

    $id = (int) trim($inputs["id"]);
    $title = trim($inputs["title"]);
    $subtitle = trim($inputs["subtitle"]);
    $sectionID = (int) trim($inputs["section-id"]);
    $description = trim($inputs["description"]);

    $content = Content::where("id", $id)->first();
    if($content == null) {
      throw new HttpBadRequestException($request, "There is no content found.");
    }

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $content->section_id = $sectionID;
    $content->title = $title;
    $content->subtitle = $subtitle != "" ? $subtitle : "false";
    $content->description = $description;
    $content->save();

    return $response->withHeader("Location", "/admin/contents/" . $id);
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

    $content = Content::where("id", $id)->first();
    if($content == null) {
      throw new HttpBadRequestException($request, "There is no content found.");
    }

    $content->delete();

    return $response->withHeader("Location", "/admin/contents");
  }

  public function single(Request $request, Response $response, array $data): Response
  {
    $content = Content::where("id", $data["id"])->first();
    if($content == null) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/contents.single.twig", [
      "content"  => $content,
      "sections" => Section::get()
    ]);
  }
}
