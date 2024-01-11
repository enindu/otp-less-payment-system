<?php

namespace App\Controllers;

use DI\Container;
use Slim\Psr7\Response;

class Controller
{
  public function __construct(protected Container $container)
  {
    $this->container->get("eloquent");
  }

  protected function view(Response $response, string $template, array $data = []): Response
  {
    $twig = $this->container->get("twig");

    $response->getBody()->write($twig->render($template, $data));
    return $response->withHeader("Content-Type", "text/html");
  }

  protected function email(string $template, array $data): null|int
  {
    $swiftMessage = $this->container->get("swift-message");
    $twig = $this->container->get("twig");

    $view = $twig->render($template, $data["body"]);
    $swiftMessage->setSubject($data["subject"]);
    $swiftMessage->setFrom($data["from"]);
    $swiftMessage->setTo($data["to"]);
    $swiftMessage->setBody($view, "text/html");

    $swiftMailer = $this->container->get("swift-mailer");

    $emailRecipients = $swiftMailer->send($swiftMessage);
    if($emailRecipients == 0) {
      return $emailRecipients;
    }

    return null;
  }

  protected function validate(array $data, array $rules): null|array
  {
    $validation = $this->container->get("validation");

    $validate = $validation->validate($data, $rules);
    $validationFails = $validate->fails();
    if($validationFails) {
      return $validate->errors()->all();
    }

    return null;
  }

  protected function auth(string $key, string $type): string|null
  {
    $keyExists = isset($_SESSION["auth"][$type][$key]);
    if(!$keyExists) {
      return null;
    }

    return $_SESSION["auth"][$type][$key];
  }
}
