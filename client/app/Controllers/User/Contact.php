<?php

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Models\Message;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Contact extends Controller
{
  public function base(Request $request, Response $response, array $data): Response
  {
    $method = $request->getMethod();
    if($method == "GET") {
      return $this->view($response, "@user/contact.twig");
    }
    if($method == "POST") {
      $inputs = $request->getParsedBody();
      $validation = $this->validate($inputs, [
        "name"    => "required|max:191",
        "email"   => "required|email|max:191",
        "subject" => "required|max:191",
        "message" => "required|max:1000"
      ]);
      if($validation != null) {
        throw new HttpBadRequestException($request, reset($validation) . ".");
      }

      $name = trim($inputs["name"]);
      $email = trim($inputs["email"]);
      $subject = trim($inputs["subject"]);
      $message = trim($inputs["message"]);

      $carbon = $this->container->get("carbon");

      Message::insert([
        "name"       => $name,
        "email"      => $email,
        "subject"    => $subject,
        "message"    => $message,
        "created_at" => $carbon::now(),
        "updated_at" => $carbon::now()
      ]);

      return $this->view($response, "@common/message.twig", [
        "message" => "Message sent successfully. We will contact you soon."
      ]);
    }
  }
}
