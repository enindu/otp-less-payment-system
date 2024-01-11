<?php

use Twig\Template;

$_ENV = [
  "app" => [
    "name"        => "Payment System",
    "description" => "Payment system demonstration",
    "keywords"    => "",
    "version"     => "v0.3.3-dev",
    "author"      => "Vihanga Hasintha (vihangahasintha95@gmail.com)",
    "domain"      => "localhost",
    "url"         => "http://localhost",
    "timezone"    => "Asia/Colombo",
    "charset"     => "UTF-8",
    "key"         => "uWTH7sh5MYkCyWsn5WkApY43gohu9ALj",
    "cookie"      => [
      "admin" => "slender_3u4ai2dIhZ",
      "user"  => "slender_3ngtpL6JQj"
    ],
    "server" => [
      "middleware"     => "https://localhost:5001/api",
      "core"           => "https://localhost:5002/api",
      "authentication" => "https://localhost:5003/api",
      "exchange-rates" => "https://api.exchangerate-api.com/v4/latest/LKR"
    ]
  ],
  "eloquent" => [
    "driver"    => "mysql",
    "host"      => "localhost",
    "database"  => "payment_system_client",
    "username"  => "root",
    "password"  => "root",
    "charset"   => "utf8",
    "collation" => "utf8_unicode_ci",
    "prefix"    => ""
  ],
  "twig" => [
    "debug"               => true,
    "charset"             => "UTF-8",
    "base-template-class" => Template::class,
    "cache"               => __DIR__ . "/../cache/views",
    "auto-reload"         => true,
    "strict-variables"    => true,
    "auto-escape"         => "html",
    "optimizations"       => -1
  ],
  "swift-mailer" => [
    "host"     => "",
    "port"     => "",
    "username" => "",
    "password" => ""
  ],
  "parsedown" => [
    "safe-mode" => true
  ],
  "image" => [
    "driver" => "gd"
  ],
  "middleware" => [
    "session" => [
      "name"       => "slender_Ore6hY0CFl",
      "lifetime"   => 0,
      "path"       => "/",
      "domain"     => "localhost",
      "secure"     => false,
      "http-only"  => true,
      "sid-length" => 32
    ]
  ]
];
