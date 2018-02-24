<?php

class StudentHandler {
    private function __construct() {
    }

    public static function getStudentsData() {
        $students = DatabaseHandler::all("student", "DESC", "Id");

        if (AdministratorHandler::checkIfSales()) {
            $plusStudent = "";
        } else {
            $plusStudent = "<h3 class='text-secondary title-plus'>STUDENTS<button class='btn btn-primary btn-plus' id='addStudentBtn'><i class='fa fa-plus'></i></button></h3>
            <hr class='mb-5'>";
        }

        $data = $plusStudent;

        if ($students) {
            foreach ($students as $student) {
                $data .= 
                "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' id='getCourseForm'>
                    <div class='media mb-5 students'>
                        <input type='hidden' name='studentId' value='{$student->getId()}'>
                        <img class='small-image mr-3' src='{$student->getImage()}' alt='{$student->getFullName()}'>
                        <div class='media-body'>
                            <h5>{$student->getFullName()}</h5>
                            <p>{$student->getPhone()}</p>
                        </div>
                    </div>
                </form>";
            }
        } else {
            $data .= MessageHandler::warning("There are no courses right now");
        }
        return $data;
    }

    public static function addStudent($fullName, $email, $phone, $courses, $file, $upload) {
        if ($upload !== NULL) {
            if ($upload->checkUpload($file)) {
                $student = new Student(NULL, $fullName, $email, $phone, NULL);
                if ($id = $student->add()) {
                    $upload->fileUpload($file, "images/uploads/students/", "student$id");
                    $image = $upload->getFinallyName();
                    if (SchoolHandler::updateImage("student", $id, $image)) {
                        if ($courses !== NULL) {
                            SchoolHandler::insertCoursesForStudents($id, $courses);
                        }
                        self::initAddStudent();
                        return MessageHandler::success("You added this student successfully");
                    }
                }
            } else {
                return MessageHandler::error($upload->getErrorMsg());
            }
        } else {
            $student = new Student(NULL, $fullName, $email, $phone, NULL);
            if ($id = $student->add()) {
                if ($courses !== NULL) {
                    SchoolHandler::insertCoursesForStudents($id, $courses);
                }
                self::initAddStudent();
                return MessageHandler::success("You added this student successfully");
            }
        }
    }

    public static function updateStudent($studentId, $fullName, $email, $phone, $courses, $file, $upload) {
        if ($upload !== NULL) {
            if ($upload->checkUpload($file)) {
                $upload->fileUpload($file, "images/uploads/students/", "student$studentId");
                $image = $upload->getFinallyName();
                $student = new Student($studentId, $fullName, $email, $phone, $image);
                if ($student->update()) {
                    SchoolHandler::insertCoursesForStudents($studentId, $courses);
                    return MessageHandler::success("You updated this student successfully");
                }
            } else {
                $_POST["showUpdateStudentEdit"] = true;
                return MessageHandler::error($upload->getErrorMsg());
            }
        } else {
            if (empty($file)) {
                $file = NULL;
            }
            $student = new Student($studentId, $fullName, $email, $phone, $file);
            if ($student->update()) {
                SchoolHandler::insertCoursesForStudents($studentId, $courses);
                return MessageHandler::success("You updated this student successfully");
            }
        }
    }

    public static function addStudentAction() {
        global $showSchoolCounter;
        global $showStudentForm;

        if (isset($_POST["addStudent"])) {
            $showSchoolCounter = "none";
            $showStudentForm = "block";

            if (ValidationHandler::validateInputs($_POST["addStudentFullName"], "/^[a-zA-Z ]*$/")) {
                $fullName = ValidationHandler::testInput($_POST["addStudentFullName"]);
                $fullName = strtolower($fullName);
            } else {
                $errors[] = "This full name is invalid";
            }

            if (ValidationHandler::validateEmail($_POST["addStudentEmail"])) {
                $email = ValidationHandler::testInput($_POST["addStudentEmail"]);
                $email = strtolower($email);
                if (SchoolHandler::checkIfExists("student", "Email", $email)) {
                    $errors[] = "This email is already in used";
                }
            } else {
                $errors[] = "This email is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["addStudentPhone"], "/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/")) {
                $phone = ValidationHandler::testInput($_POST["addStudentPhone"]);
                if (SchoolHandler::checkIfExists("student", "Phone", $phone)) {
                    $errors[] = "This phone is already in used";
                }
            } else {
                $errors[] = "This phone is invalid";
            }

            if (!empty($_POST["courses"])) {
                $courses = $_POST["courses"];
            } else {
                $courses = NULL;
            }

            if (!empty($_FILES["addStudentImage"]["name"])) {
                $file = $_FILES["addStudentImage"];
                $upload = new Upload();
            } else {
                $file = NULL;
                $upload = NULL;
            }

            if (empty($errors)) {
                return self::addStudent($fullName, $email, $phone, $courses, $file, $upload);
            } else {
                return MessageHandler::error($errors);
            }
        }
    }

    public static function updateStudentAction() {
        if (isset($_POST["updateStudent"])) {
            $studentId = $_POST["studentId"];

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
                    if (SchoolHandler::checkIfExists("student", "Email", $email)) {
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
                    if (SchoolHandler::checkIfExists("student", "Phone", $phone)) {
                        $errors[] = "This phone is already in used";
                    }
                }
            } else {
                $errors[] = "This phone is invalid";
            }

            if (!empty($_POST["courses"])) {
                $courses = $_POST["courses"];
            } else {
                $courses = NULL;
            }

            if (!empty($_FILES["studentImage"]["name"])) {
                $file = $_FILES["studentImage"];
                $upload = new Upload();
            } else {
                $file = $_POST["imageExist"];
                $upload = NULL;
            }

            if (empty($errors)) {
                return self::updateStudent($studentId, $fullName, $email, $phone, $courses, $file, $upload);
            } else {
                $_POST["showUpdateStudentEdit"] = true;
                return MessageHandler::error($errors);
            }
        }        
    }

    public static function deleteStudentAction() {
        if (!empty($_POST["deleteStudent"])) {
            $studentId = $_POST["deleteStudent"];

            if (SchoolHandler::studentCourses($studentId)) {
                $_POST["studentId"] = $studentId;
                return MessageHandler::error("You cant delete this student");
            } else {
                $studentImage = SchoolHandler::getImageById("student", $studentId);
                if (!empty($studentImage)) {
                    unlink("images/uploads/students/" . $studentImage);
                    return SchoolHandler::delete("student", $studentId);
                } else {
                    return SchoolHandler::delete("student", $studentId);
                } 
            }
        }
    }

    public static function initAddStudent() {
        $_POST["addStudentFullName"] = $_POST["addStudentEmail"] = $_POST["addStudentPhone"] = "";
    }

    public static function getStudentByIdAction() {
        global $showSchoolCounter;
        global $showCourseForm;
        global $showStudentForm;

        if (!empty($_POST["studentId"])) {
            $showSchoolCounter = "none";
            $showCourseForm = "none";
            $showStudentForm = "none";

            if (isset($_POST["showUpdateStudentEdit"])) {
                $showUpdateStudentEdit = "block";
                $showUpdateStudentInformation = "none";             
                $showUpdateStudentBtn = "block";             
            } else {
                $showUpdateStudentEdit = "none";
                $showUpdateStudentInformation = "block";
                $showUpdateStudentBtn = "none";           
            }

            $studentId = $_POST["studentId"];

            $student = DatabaseHandler::whereOne("student", "Id", $studentId);

            $deleteBtn = "";
            $coursesCheckbox = "";

            if ($student) {
                if ($studentCourses = SchoolHandler::studentCourses($student->getId())) {
                    $courses = 
                    "<h4 class='card-text text-left p-2'><span class='badge badge-danger'>Courses</span></h4>
                    <ul class='text-left'>";
                    foreach ($studentCourses as $value) {
                        $courses .= "<li>" . strtoupper($value->CourseName) . "</li>";
                    }
                    $courses .= "</ul>";
                    $coursesCheckbox = CourseHandler::getCoursesNames($studentCourses);
                } else {
                    $courses = 
                    "<p class='card-text text-left alert alert-info'><span><i class='fa fa-exclamation-circle mr-2'></i></span>This student does not have courses right now</p>";
                    $coursesCheckbox = CourseHandler::getCoursesNames();
                    if (!AdministratorHandler::checkIfSales()) {
                        $deleteBtn = "<button class='btn btn-danger text-light w-25 float-right' id='deleteStudentBtn' style='display:$showUpdateStudentBtn;'><i class='fa fa-trash'></i></button>
                        <input type='hidden' name='deleteStudent' value='{$student->getId()}'>";
                    }
                }

                return
                "<div id='editStudentContainer'>
                    <div class='card'>
                        <div class='card-header'>
                        <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST'>
                            <button class='btn btn-warning text-light w-25' id='editStudentBtn'><i class='fa fa-pencil'></i></button>
                            $deleteBtn
                        </form>
                        </div>
                        <div id='studentInformation' style='display:$showUpdateStudentInformation;'>
                            <div class='card-header text-center'>
                                <img src='{$student->getImage()}' alt='{$student->getFullName()}' height='150'>
                            </div>
                            <div class='card-body text-center'>
                                <h4 class='card-title'>{$student->getFullName()}</h4>
                                <h4 class='card-text p-2'><span class='badge badge-success'>{$student->getEmail()}</span></h4>
                                <h4 class='card-text p-2'><span class='badge badge-dark'>{$student->getPhone()}</span></h4>
                                $courses
                            </div>
                        </div>
                        <div class='card-body' id='updateStudent' style='display:$showUpdateStudentEdit;'>
                            <div class='jumbotron bg-info text-light p-2'>
                                <h3>Edit Student</h3>
                            </div>
                            <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' enctype='multipart/form-data' autocomplete='off' id='editStudentForm'>
                                <div class='form-group'>
                                    <input type='text' name='fullName' id='fullName' class='form-control' placeholder='Full Name'
                                    value='" . ValidationHandler::preserveValue("fullName", $student->getFullName()) . "' data-validation='required custom' data-validation-regexp='^[a-zA-Z ]*$'>
                                </div>
                                <div class='form-group'>
                                    <input type='text' name='email' id='email' class='form-control' placeholder='Email'
                                    value='" . ValidationHandler::preserveValue("email", $student->getEmail()) . "' data-validation='required email'>
                                </div>
                                <div class='form-group'>
                                    <input type='text' name='phone' id='phone' class='form-control phone' placeholder='Phone'
                                    value='" . ValidationHandler::preserveValue("phone", $student->getPhone()) . "' data-validation='required length' data-validation-length='min12'>
                                </div>
                                <div class='form-group'>
                                    $coursesCheckbox
                                </div>
                                <div class='form-group none' id='previewUpdateStudentImage'>
                                    <img src='' alt='Preview Image' id='previewStudentImageSrcUpdate' class='w-100'>
                                </div>
                                <div class='form-group'>
                                    <label class='custom-file w-100'>
                                        <input type='file' name='studentImage' id='studentImageInpUpdate' class='custom-file-input form-control'
                                        data-validation='mime size' data-validation-allowing='jpg, jpeg, png' data-validation-max-size='1M'>
                                        <span class='custom-file-control'></span>
                                    </label>
                                </div>
                                <div class='form-group'>
                                    <input type='submit' value='Submit' name='updateStudent' class='form-control btn btn-success'>
                                    <input type='hidden' value='{$student->getId()}' name='studentId' class='none'>
                                    <input type='hidden' value='{$student->getImageWithout()}' name='imageExist' class='none'>
                                    <input type='hidden' value='{$student->getEmail()}' name='emailExist' class='none'>
                                    <input type='hidden' value='{$student->getPhone()}' name='phoneExist' class='none'>
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
}

?>