<?php

$container->set("swift-message", function(): Swift_Message {
  return new Swift_Message();
});
