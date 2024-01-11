<?php

$container->set("swift-mailer", function(): Swift_Mailer {
  $swiftSMTPTransport = new Swift_SmtpTransport();
  $swiftSMTPTransport->setHost($_ENV["swift-mailer"]["host"]);
  $swiftSMTPTransport->setPort($_ENV["swift-mailer"]["port"]);
  $swiftSMTPTransport->setUsername($_ENV["swift-mailer"]["username"]);
  $swiftSMTPTransport->setPassword($_ENV["swift-mailer"]["password"]);
  return new Swift_Mailer($swiftSMTPTransport);
});
