<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Message;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Messages extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    return $this->view($response, "@admin/messages.twig", [
      "messages" => Message::orderBy("id", "desc")->take(10)->get()
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

    $results = count(Message::get());
    $resultsPerPage = 10;
    $pageResults = ($page - 1) * $resultsPerPage;
    $pages = ceil($results / $resultsPerPage);
    if($page < 1 || $page > $pages) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/messages.all.twig", [
      "page"     => $page,
      "pages"    => $pages,
      "messages" => Message::orderBy("id", "desc")->skip($pageResults)->take($resultsPerPage)->get()
    ]);
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

    $message = Message::where("id", $id)->first();
    if($message == null) {
      throw new HttpBadRequestException($request, "There is no message found.");
    }

    $message->delete();

    return $response->withHeader("Location", "/admin/messages");
  }

  public function single(Request $request, Response $response, array $data): Response
  {
    $message = Message::where("id", $data["id"])->first();
    if($message == null) {
      throw new HttpNotFoundException($request);
    }

    return $this->view($response, "@admin/messages.single.twig", [
      "message" => $message
    ]);
  }
}
