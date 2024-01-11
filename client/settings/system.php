<?php

date_default_timezone_set($_ENV["app"]["timezone"]);
mb_internal_encoding($_ENV["app"]["charset"]);
session_start([
  "name"            => $_ENV["middleware"]["session"]["name"],
  "cookie_lifetime" => $_ENV["middleware"]["session"]["lifetime"],
  "cookie_path"     => $_ENV["middleware"]["session"]["path"],
  "cookie_domain"   => $_ENV["middleware"]["session"]["domain"],
  "cookie_secure"   => $_ENV["middleware"]["session"]["secure"],
  "cookie_httponly" => $_ENV["middleware"]["session"]["http-only"],
  "sid_length"      => $_ENV["middleware"]["session"]["sid-length"]
]);
