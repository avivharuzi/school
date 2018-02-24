<?php

if (!(basename($_SERVER["SCRIPT_FILENAME"], ".php") === "login")) {
    isLoggedIn();
    administratorInstance();
    checkAdministrationPage();
}

function checkAdministrationPage() {
    if ((basename($_SERVER["SCRIPT_FILENAME"], ".php") === "administration") && $_SESSION["role"] === "sales") {
        header("Location: index.php");
    }
}

function isLoggedIn() {
    if (!isset($_SESSION["isLoggedIn"]) && !isset($_SESSION["role"]) && !isset($_SESSION["administratorId"])) {
        header("Location: login.php");
        exit();
    }   
}

function administratorInstance() {
    if (isset($_SESSION["administratorId"])) {
        global $administrator;
        $id = $_SESSION["administratorId"];
        $administrator = AdministratorHandler::getInstance($id);
    }
}

?>