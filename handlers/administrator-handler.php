<?php

class AdministratorHandler {
    private function __construct() {
    }

    public static function loginAction() {
        if (isset($_POST["login"]) && count($_POST["login"]) > 0) {
            $counter = 0;

            if (ValidationHandler::validateEmail($_POST["email"])) {
                $email = ValidationHandler::testInput($_POST["email"]);
                $email = DatabaseHandler::escape(strtolower($email));
            } else {
                $counter += 1;
            }

            if (!empty($_POST["password"])) {
                $password = ValidationHandler::testInput($_POST["password"]);
                $password = DatabaseHandler::escape($password);
            } else {
                $counter += 1;
            }

            if ($counter === 0) {
                $result = DatabaseHandler::whereOne("administrator", "Email", $email);
                if ($result && $result->getPassword() === $password) {
                    $result->setSession();
                } else {
                    return MessageHandler::error("You have entered an invalid email or password");
                }
            } else {
                return MessageHandler::error("You have entered an invalid email or password");
            }
        }
    }

    public static function getInstance($id) {
        return DatabaseHandler::find("administrator", $id);
    }

    public static function getRoleIdByAdministratorId($id) {
        $administrator = self::getInstance($id);
        return $administrator->getRoleId();
    }

    public static function checkIfSales() {
        if ($_SESSION["role"] === "sales") {
            return true;
        } else {
            return false;
        }
    }

    public static function checkIfOwner($id) {
        $sql = "SELECT * FROM administrator WHERE RoleId = 1 AND Id = $id";
        $result = $GLOBALS["conn"]->getSingleData($sql);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public static function protectOwner($id) {
        if (self::checkIfOwner($id) && $_SESSION["administratorId"] != 1) {
            return true;
        } else {
            return false;
        }
    }

    public static function getAdministratorsData() {
        $administrators = DatabaseHandler::all("administrator", "DESC", "Id");

        $plusAdministrator = "<h3 class='text-secondary title-plus'>ADMINISTRATORS<button class='btn btn-primary btn-plus' id='addAdministratorBtn'><i class='fa fa-plus'></i></button></h3>
        <hr class='mb-5'>";

        if ($administrators) {
            $data = $plusAdministrator;
            foreach ($administrators as $administrator) {
                $role = $administrator->getRoleName();

                if (!($_SESSION["role"] === "manager" && $role === "owner")) {
                    $data .=
                    "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' id='getAdministratorForm'>
                        <div class='media mb-5 administrators'>
                            <input type='hidden' name='administratorId' value='{$administrator->getId()}'>
                            <img class='small-image mr-3' src='{$administrator->getImage()}' alt='{$administrator->getFullName()}'>
                            <div class='media-body'>
                                <h5>{$administrator->getFullName()} <span class='text-secondary'>" . ucwords($role) . "</span></h5>
                                <p>{$administrator->getPhone()}<br>{$administrator->getEmail()}</p>
                            </div>
                        </div>
                    </form>";
                }
            }
            return $data;
        }
    }

    public static function administratorCounter() {
        $administrators = DatabaseHandler::count("administrator");

        if ($_SESSION["administratorId"] != 1) {
            $administrators -= 1;
        }

        return
        "<div class='jumbotron bg-info text-light'>
            <h3>ADMINISTRATORS<span class='badge badge-light ml-3 counter'>$administrators</span></h3>
        </div>";
    }

    public static function selectRoles($roleId = "") {
        $roles = 
        "<div class='form-group'>
        <select name='role' class='form-control'>";

        if ($roleId == 2) {
            $roles .= "<option value='2' selected>Manager</option>";
        } else {
            $roles .= "<option value='2'>Manager</option>";
        }

        if ($roleId == 3) {
            $roles .= "<option value='3' selected>Sales</option>";
        } else {
            $roles .= "<option value='3'>Sales</option>";
        }

        $roles .= "</select></div>";

        return $roles;
    }

    public static function addAdministrator($fullName, $email, $phone, $role, $password, $file, $upload) {
        if ($upload !== NULL) {
            if ($upload->checkUpload($file)) {
                $administrator = new Administrator(NULL, $fullName, $email, $phone, NULL, $password, $role);
                if ($id = $administrator->add()) {
                    $upload->fileUpload($file, "images/uploads/administrators/", "profile$id");
                    $image = $upload->getFinallyName();
                    if (SchoolHandler::updateImage("administrator", $id, $image)) {
                        self::initAddAdministrator();
                        return MessageHandler::success("You added this administrator successfully");
                    }
                }
            } else {
                return MessageHandler::error($upload->getErrorMsg());
            }
        } else {
            $administrator = new Administrator(NULL, $fullName, $email, $phone, NULL, $password, $role);
            if ($id = $administrator->add()) {
                self::initAddAdministrator();
                return MessageHandler::success("You added this administrator successfully");
            }
        }
    }

    public static function updateAdministrator($administratorId, $fullName, $email, $phone, $role, $file, $upload) {
        if ($upload !== NULL) {
            if ($upload->checkUpload($file)) {
                $upload->fileUpload($file, "images/uploads/administrators/", "profile$administratorId");
                $image = $upload->getFinallyName();
                $administrator = new Administrator($administratorId, $fullName, $email, $phone, $image, NULL, $role);
                if ($administrator->update()) {
                    return MessageHandler::success("You updated this administrator successfully");
                }
            } else {
                $_POST["showUpdateAdministratorEdit"] = true;
                return MessageHandler::error($upload->getErrorMsg());
            }
        } else {
            if (empty($file)) {
                $file = NULL;
            }
            $administrator = new Administrator($administratorId, $fullName, $email, $phone, $file, NULL, $role);
            if ($administrator->update()) {
                return MessageHandler::success("You updated this administrator successfully");
            }
        }
    }

    public static function addAdministratorAction() {
        global $showAdministratorCounter;
        global $showAdministratorForm;

        if (isset($_POST["addAdministrator"])) {
            $showAdministratorCounter = "none";
            $showAdministratorForm = "block";

            if (ValidationHandler::validateInputs($_POST["addAdministratorFullName"], "/^[a-zA-Z ]*$/")) {
                $fullName = ValidationHandler::testInput($_POST["addAdministratorFullName"]);
                $fullName = strtolower($fullName);
            } else {
                $errors[] = "This full name is invalid";
            }

            if (ValidationHandler::validateEmail($_POST["addAdministratorEmail"])) {
                $email = ValidationHandler::testInput($_POST["addAdministratorEmail"]);
                $email = strtolower($email);
                if (SchoolHandler::checkIfExists("administrator", "Email", $email)) {
                    $errors[] = "This email is already in used";
                }
            } else {
                $errors[] = "This email is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["addAdministratorPhone"], "/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/")) {
                $phone = ValidationHandler::testInput($_POST["addAdministratorPhone"]);
                if (SchoolHandler::checkIfExists("administrator", "Phone", $phone)) {
                    $errors[] = "This phone is already in used";
                }
            } else {
                $errors[] = "This phone is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["addAdministratorPassword"], "/^[A-Za-z0-9!@#$%^&*()_]{6,20}$/")) {
                $password = ValidationHandler::testInput($_POST["addAdministratorPassword"]);
            } else {
                $errors[] = "This password is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["addAdministratorPasswordConfirm"], "/^[A-Za-z0-9!@#$%^&*()_]{6,20}$/")) {
                $passwordConfirm = ValidationHandler::testInput($_POST["addAdministratorPasswordConfirm"]);
            } else {
                $errors[] = "This password confirmation is invalid";
            }

            if ($_POST["addAdministratorPassword"] !== $_POST["addAdministratorPasswordConfirm"]) {
                $errors[] = "This passwords are not matched";
            }

            if (ValidationHandler::validateInputs($_POST["role"], "/^[0-9]*$/")) {
                $role = ValidationHandler::testInput($_POST["role"]);
                if ($role == 1) {
                    $errors[] = "This role belongs to owner only";
                }
            } else {
                $errors[] = "This role is invalid";
            }

            if (!empty($_FILES["addAdministratorImage"]["name"])) {
                $file = $_FILES["addAdministratorImage"];
                $upload = new Upload();
            } else {
                $file = NULL;
                $upload = NULL;
            }

            if (empty($errors)) {
                return self::addAdministrator($fullName, $email, $phone, $role, $password, $file, $upload);
            } else {
                return MessageHandler::error($errors);
            }
        }
    }

    public static function updateAdministratorAction() {
        if (isset($_POST["updateAdministrator"])) {
            if (self::protectOwner($_POST["administratorId"])) {
                return MessageHandler::error("You dont have permission to update this administrator");
            }

            $administratorId = $_POST["administratorId"];

            if (ValidationHandler::validateInputs($_POST["fullName"], "/^[a-zA-Z ]*$/")) {
                $fullName = ValidationHandler::testInput($_POST["fullName"]);
                $fullName = strtolower($fullName);
            } else {
                $errors[] = "This full name is invalid";
            }
            
            if (ValidationHandler::validateEmail($_POST["email"])) {
                $email = ValidationHandler::testInput($_POST["email"]);
                $email = strtolower($email);
                if (!empty($_POST["emailExist"] && $_POST["emailExist"] === $_POST["email"])) {
                } else {
                    if (SchoolHandler::checkIfExists("administrator", "Email", $email)) {
                        $errors[] = "This email is already in used";
                    }
                }
            } else {
                $errors[] = "This email is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["phone"], "/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/")) {
                $phone = ValidationHandler::testInput($_POST["phone"]);
                if (!empty($_POST["phoneExist"] && $_POST["phoneExist"] === $_POST["phone"])) {
                } else {
                    if (SchoolHandler::checkIfExists("administrator", "Phone", $phone)) {
                        $errors[] = "This phone is already in used";
                    }
                }
            } else {
                $errors[] = "This phone is invalid";
            }

            if (empty($_POST["role"]) || $_SESSION["administratorId"] == $administratorId) {
                $role = self::getRoleIdByAdministratorId($administratorId);
            } else {
                if (ValidationHandler::validateInputs($_POST["role"], "/^[0-9]*$/")) {
                    $role = ValidationHandler::testInput($_POST["role"]);
                    if ($role == 1) {
                        $errors[] = "This role belongs to owner only";
                    }
                } else {
                    $errors[] = "This role is invalid";
                }
            }

            if (!empty($_FILES["administratorImage"]["name"])) {
                $file = $_FILES["administratorImage"];
                $upload = new Upload();
            } else {
                $file = $_POST["imageExist"];
                $upload = NULL;
            }

            if (empty($errors)) {
                return self::updateAdministrator($administratorId, $fullName, $email, $phone, $role, $file, $upload);
            } else {
                $_POST["showUpdateAdministratorEdit"] = true;
                return MessageHandler::error($errors);
            }
        }
    }

    public static function deleteAdministratorAction() {
        if (!empty($_POST["deleteAdministrator"])) {
            $administratorId = $_POST["deleteAdministrator"];

            if ($_SESSION["administratorId"] !== $administratorId && !(self::checkIfOwner($administratorId))) {
                $administratorImage = SchoolHandler::getImageById("administrator", $administratorId);
                if (!empty($administratorImage)) {
                    unlink("images/uploads/administrators/" . $administratorImage);
                    return SchoolHandler::delete("administrator", $administratorId);
                } else {
                    return SchoolHandler::delete("administrator", $administratorId);
                } 
            } else {
                return MessageHandler::error("You cant delete this administrator");
            }
        }
    }

    public static function initAddAdministrator() {
        $_POST["addAdministratorFullName"] = $_POST["addAdministratorEmail"] = $_POST["addAdministratorPhone"] =
        $_POST["addAdministratorPassword"] = $_POST["addAdministratorPasswordConfirm"] = "";
    }

    public static function getAdministratorByIdAction() {
        global $showAdministratorCounter;
        global $showAdministratorForm;

        if (!empty($_POST["administratorId"])) {
            $showAdministratorCounter = "none";
            $showAdministratorForm = "none";

            if (isset($_POST["showUpdateAdministratorEdit"])) {
                $showUpdateAdministratorEdit = "block";
                $showUpdateAdministratorInformation = "none";             
                $showUpdateAdministratorBtn = "block";             
            } else {
                $showUpdateAdministratorEdit = "none";
                $showUpdateAdministratorInformation = "block";
                $showUpdateAdministratorBtn = "none";           
            }

            if (self::protectOwner($_POST["administratorId"])) {
                return MessageHandler::error("You dont have permission to see this administrator");
            } else {
                $administratorId = $_POST["administratorId"];
            }

            $administrator = DatabaseHandler::find("administrator", $administratorId);

            $deleteBtn = "";
            $selectRoles = "";

            if ($administrator) {
                $role = $administrator->getRoleName();

                if ($_SESSION["administratorId"] !== $administrator->getId()) {
                    $deleteBtn = "<button class='btn btn-danger text-light w-25 float-right' id='deleteAdministratorBtn' style='display:$showUpdateAdministratorBtn;'><i class='fa fa-trash'></i></button>
                    <input type='hidden' name='deleteAdministrator' value='{$administrator->getId()}'>";
                    $selectRoles = AdministratorHandler::selectRoles($administrator->getRoleId());
                }

                return
                "<div id='editAdministratorContainer'>
                    <div class='card'>
                        <div class='card-header'>
                            <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST'>
                                <button class='btn btn-warning text-light w-25' id='editAdministratorBtn'><i class='fa fa-pencil'></i></button>
                                $deleteBtn
                            </form>
                        </div>
                        <div id='administratorInformation' style='display:$showUpdateAdministratorInformation;'>
                            <div class='card-header text-center'>
                                <img src='{$administrator->getImage()}' alt='{$administrator->getFullName()}' height='150'>
                            </div>
                            <div class='card-body text-center'>
                                <h4 class='card-title'>{$administrator->getFullName()}</h4>
                                <h4 class='card-text p-2'><span class='badge badge-success'>{$administrator->getEmail()}</span></h4>
                                <h4 class='card-text p-2'><span class='badge badge-dark'>{$administrator->getPhone()}</span></h4>
                                <h4 class='card-text p-2'><span class='badge badge-secondary'>" . ucwords($role) . "</span></h4>
                            </div>
                        </div>
                        <div class='card-body' id='updateAdministrator' style='display:$showUpdateAdministratorEdit;'>
                             <div class='jumbotron bg-info text-light p-2'>
                                <h3>Edit Administrator</h3>
                            </div>
                            <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' enctype='multipart/form-data' autocomplete='off' id='editAdministratorForm'>
                                <div class='form-group'>
                                    <input type='text' name='fullName' id='fullName' class='form-control' placeholder='Full Name'
                                    value='" . ValidationHandler::preserveValue("fullName", $administrator->getFullName()) . "' data-validation='required custom' data-validation-regexp='^[a-zA-Z ]*$'>
                                </div>
                                <div class='form-group'>
                                    <input type='text' name='email' id='email' class='form-control' placeholder='Email'
                                    value='" . ValidationHandler::preserveValue("email", $administrator->getEmail()) . "' data-validation='required email'>
                                </div>
                                <div class='form-group'>
                                    <input type='text' name='phone' id='phone' class='form-control phone' placeholder='Phone'
                                    value='" . ValidationHandler::preserveValue("phone", $administrator->getPhone()) . "' data-validation='required length' data-validation-length='min12'>
                                </div>
                                $selectRoles
                                <div class='form-group none' id='previewUpdateAdministratorImage'>
                                    <img src='' alt='Preview Image' id='previewAdministratorImageSrcUpdate' class='w-100'>
                                </div>
                                <div class='form-group'>
                                    <label class='custom-file w-100'>
                                        <input type='file' name='administratorImage' id='administratorImageInpUpdate' class='custom-file-input form-control'
                                        data-validation='mime size' data-validation-allowing='jpg, jpeg, png' data-validation-max-size='1M'>
                                        <span class='custom-file-control'></span>
                                    </label>
                                </div>
                                <div class='form-group'>
                                    <input type='submit' value='Submit' name='updateAdministrator' class='form-control btn btn-success'>
                                    <input type='hidden' value='{$administrator->getId()}' name='administratorId' class='none'>
                                    <input type='hidden' value='{$administrator->getImageWithout()}' name='imageExist' class='none'>
                                    <input type='hidden' value='{$administrator->getFullName()}' name='fullNameExist' class='none'>
                                    <input type='hidden' value='{$administrator->getEmail()}' name='emailExist' class='none'>
                                    <input type='hidden' value='{$administrator->getPhone()}' name='phoneExist' class='none'>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>";
            } else {
                return false;
            }
        }
    }

    public static function administratorActions() {
        $addAdministrator = self::addAdministratorAction();
        $updateAdministrator = self::updateAdministratorAction();
        $deleteAdministrator = self::deleteAdministratorAction();
        

        if (!empty($addAdministrator)) {
            return $addAdministrator;
        }

        if (!empty($updateAdministrator)) {
            return $updateAdministrator;
        }

        if (!empty($deleteAdministrator)) {
            return $deleteAdministrator;
        }
    }

    public static function administratorShowActions() {
        $getAdministratorById = self::getAdministratorByIdAction();

        if (!empty($getAdministratorById)) {
            return $getAdministratorById;
        }
    }
}

?>