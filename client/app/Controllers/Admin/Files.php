<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\File;
use App\Models\Section;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Files extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/files.twig", [
      "sections" => Section::get(),
      "files"    => File::orderBy("id", "desc")->take(10)->get()
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

    $results = count(File::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }
    
    return $this->view($response, "@admin/files.all.twig", [
      "page"  => $page,
      "pages" => $pages,
      "files" => File::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
  }

  public function add(Request $request, Response $response, array $data): Response
  {
    $inputs = $request->getParsedBody();
    $inputValidation = $this->validate($inputs, [
      "title"       => "max:191",
      "subtitle"    => "max:191",
      "section-id"  => "required|integer",
      "description" => "max:500"
    ]);
    if($inputValidation != null) {
      throw new HttpBadRequestException($request, reset($inputValidation) . ".");
    }

    $files = $request->getUploadedFiles();
    $fileValidation = $this->validate($_FILES, [
      "file" => "required|uploaded_file:0,10M"
    ]);
    if($fileValidation != null) {
      throw new HttpBadRequestException($request, reset($fileValidation) . ".");
    }

    $title = trim($inputs["title"]);
    $subtitle = trim($inputs["subtitle"]);
    $sectionID = (int) trim($inputs["section-id"]);
    $description = trim($inputs["description"]);
    $file = $files["file"];

    $section = Section::where("id", $sectionID)->first();
    if($section == null) {
      throw new HttpBadRequestException($request, "There is no section found.");
    }

    $fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
    $fileName = uniqid(bin2hex(random_bytes(8))) . "." . $fileExtension;
    $file->moveTo(__DIR__ . "/../../../uploads/files/" . $fileName);

    $carbon = $this->container->get("carbon");

    File::insert([
      "section_id"  => $sectionID,
      "title"       => $title != "" ? $title : "false",
      "subtitle"    => $subtitle != "" ? $subtitle : "false",
      "description" => $description != "" ? $description : "false",
      "file"        => $fileName,
      "created_at"  => $carbon::now(),
      "updated_at"  => $carbon::now()
    ]);

    return $response->withHeader("Location", "/admin/files");
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

    $file = File::where("id", $id)->first();
    if($file == null) {
      throw new HttpBadRequestException($request, "There is no file found.");
    }

    $file->delete();

    return $response->withHeader("Location", "/admin/files");
  }
}
