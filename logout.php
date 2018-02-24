<?php

require_once("auth/config.php");

session_destroy();
header("Location: login.php");
exit();

?>