<?php

session_start();

date_default_timezone_set("Israel");

require_once("connection/db.php");
require_once("handlers/db-handler.php");
require_once("interfaces/i-person.php");
require_once("models/person.php");
require_once("models/administrator.php");
require_once("models/student.php");
require_once("models/course.php");
require_once("models/upload.php");
require_once("handlers/validation-handler.php");
require_once("handlers/message-handler.php");
require_once("handlers/student-handler.php");
require_once("handlers/course-handler.php");
require_once("handlers/school-handler.php");
require_once("handlers/administrator-handler.php");
require_once("auth/restrict.php");

?>