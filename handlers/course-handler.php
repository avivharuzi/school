<?php

class CourseHandler {
    private function __construct() {
    }

    public static function getCoursesData() {
        $courses = DatabaseHandler::all("course", "DESC", "Id");

        if (AdministratorHandler::checkIfSales()) {
            $plusCourse = "";
        } else {
            $plusCourse = "<h3 class='text-secondary title-plus'>COURSES<button class='btn btn-primary btn-plus' id='addCourseBtn'><i class='fa fa-plus'></i></button></h3>
            <hr class='mb-5'>";
        }

        $data = $plusCourse;

        if ($courses) {
            foreach ($courses as $course) {
                $data .=
                "<form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' id='getCourseForm'>
                    <div class='media mb-5 courses'>
                        <input type='hidden' name='courseId' value='{$course->getId()}'>
                        <img class='small-image mr-3' src='{$course->getImage()}' alt='{$course->getName()}'>
                        <div class='media-body'>
                            <h5>{$course->getName()}</h5>
                            <p>{$course->getPrice()}</p>
                        </div>
                    </div>
                </form>";
            }
        } else {
            $data .= MessageHandler::warning("There are no courses right now");
        }
        return $data;
    }

    public static function getCoursesNames($existCourses = NULL) {
        $courses = DatabaseHandler::all("course", "DESC", "Id");

        if ($courses) {
            $data = "";
            if ($existCourses === NULL) {
                foreach ($courses as $course) {
                    $data .=
                    "<label class='custom-control custom-checkbox mb-3 mr-sm-5 mb-sm-0'>
                        <input type='checkbox' class='custom-control-input' name='courses[]' value='{$course->getId()}'>
                        <span class='custom-control-indicator'></span>
                        <span class='custom-control-description'>{$course->getName()}</span>
                    </label>";
                }
            } else {
                foreach ($courses as $course) {
                    $counter = 0;
                    foreach ($existCourses as $existCourse) {
                        if ($course->getId() === $existCourse->CourseId) {
                            $counter += 1;
                        }
                    }

                    $data .= "<label class='custom-control custom-checkbox mb-3 mr-sm-5 mb-sm-0'>";

                    if ($counter === 0) {
                        $data .= "<input type='checkbox' class='custom-control-input' name='courses[]' value='{$course->getId()}'>";
                    } else {
                        $data .= "<input type='checkbox' class='custom-control-input' checked name='courses[]' value='{$course->getId()}'>";
                    }

                    $data .= 
                        "<span class='custom-control-indicator'></span>
                        <span class='custom-control-description'>{$course->getName()}</span>
                    </label>";
                }
            }
            return $data;
        } else {
            return MessageHandler::warning("There are no courses right now");
        }
    }

    public static function addCourse($courseName, $description, $price, $file, $upload) {
        if ($upload !== NULL) {
            if ($upload->checkUpload($file)) {
                $course = new Course(NULL, $courseName, $description, $price, NULL);
                if ($id = $course->add()) {
                    $upload->fileUpload($file, "images/uploads/courses/", "course$id");
                    $image = $upload->getFinallyName();
                    if (SchoolHandler::updateImage("course", $id, $image)) {
                        self::initAddCourse();
                        return MessageHandler::success("You added this course successfully");
                    }
                }
            } else {
                return MessageHandler::error($upload->getErrorMsg());
            }
        } else {
            $course = new Course(NULL, $courseName, $description, $price, NULL);
            if ($course->add()) {
                self::initAddCourse();
                return MessageHandler::success("You added this course successfully");
            }
        }
    }

    public static function updateCourse($courseId, $courseName, $description, $price, $file, $upload) {
        if ($upload !== NULL) {
            if ($upload->checkUpload($file)) {
                $upload->fileUpload($file, "images/uploads/courses/", "course$courseId");
                $image = $upload->getFinallyName();
                $course = new Course($courseId, $courseName, $description, $price, $image);
                if ($course->update()) {
                    return MessageHandler::success("You updated this course successfully");
                }
            } else {
                $_POST["showUpdateCourseEdit"] = true;
                return MessageHandler::error($upload->getErrorMsg());
            }
        } else {
            if (empty($file)) {
                $file = NULL;
            }
            $course = new Course($courseId, $courseName, $description, $price, $file);
            if ($course->update()) {
                return MessageHandler::success("You updated this course successfully");
            }
        }
    }

    public static function addCourseAction() {
        global $showSchoolCounter;
        global $showCourseForm;

        if (isset($_POST["addCourse"])) {
            $showSchoolCounter = "none";
            $showCourseForm = "block";

            if (ValidationHandler::validateInputs($_POST["addCourseName"], "/^[a-zA-Z0-9 ]*$/")) {
                $courseName = ValidationHandler::testInput($_POST["addCourseName"]);
                $courseName = strtolower($courseName);
                if (SchoolHandler::checkIfExists("course", "Name", $courseName)) {
                    $errors[] = "This course name is already in used";
                }
            } else {
                $errors[] = "This name is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["addCourseDescription"], "/^[a-zA-Z0-9 -=:,.]*$/")) {
                $description = ValidationHandler::testInput($_POST["addCourseDescription"]);
            } else {
                $errors[] = "This description is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["addCoursePrice"], "/^[0-9]*$/")) {
                $price = ValidationHandler::testInput($_POST["addCoursePrice"]);
            } else {
                $errors[] = "This price is invalid";
            }

            if (!empty($_FILES["addCourseImage"]["name"])) {
                $file = $_FILES["addCourseImage"];
                $upload = new Upload();
            } else {
                $file = NULL;
                $upload = NULL;
            }

            if (empty($errors)) {
                return self::addCourse($courseName, $description, $price, $file, $upload);
            } else {
                return MessageHandler::error($errors);
            }
        }
    }

    public static function updateCourseAction() {
        if (isset($_POST["updateCourse"])) {
            $courseId = $_POST["courseId"];

            if (ValidationHandler::validateInputs($_POST["courseName"], "/^[a-zA-Z0-9 ]*$/")) {
                $courseName = ValidationHandler::testInput($_POST["courseName"]);
                $courseName = strtolower($courseName);
                if (!empty($_POST["nameExist"] && $_POST["nameExist"] === $_POST["courseName"])) {
                } else {
                    if (SchoolHandler::checkIfExists("course", "Name", $courseName)) {
                        $errors[] = "This course name is already in used";
                    }
                }
            } else {
                $errors[] = "This name is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["description"], "/^[a-zA-Z0-9 -=:,.]*$/")) {
                $description = ValidationHandler::testInput($_POST["description"]);
            } else {
                $errors[] = "This description is invalid";
            }

            if (ValidationHandler::validateInputs($_POST["price"], "/^[0-9]*$/")) {
                $price = ValidationHandler::testInput($_POST["price"]);
            } else {
                $errors[] = "This price is invalid";
            }

            if (!empty($_FILES["courseImage"]["name"])) {
                $file = $_FILES["courseImage"];
                $upload = new Upload();
            } else {
                $file = $_POST["imageExist"];
                $upload = NULL;
            }

            if (empty($errors)) {
                return self::updateCourse($courseId, $courseName, $description, $price, $file, $upload);
            } else {
                $_POST["showUpdateCourseEdit"] = true;
                return MessageHandler::error($errors);
            }
        }
    }

    public static function deleteCourseAction() {
        if (!empty($_POST["deleteCourse"])) {
            $courseId = $_POST["deleteCourse"];
            
            if (SchoolHandler::courseStudents($courseId)) {
                $_POST["courseId"] = $courseId;
                return MessageHandler::error("You cant delete this course");
            } else {
                $courseImage = SchoolHandler::getImageById("course", $courseId);
                if (!empty($courseImage)) {
                    unlink("images/uploads/courses/" . $courseImage);
                    return SchoolHandler::delete("course", $courseId);
                } else {
                    return SchoolHandler::delete("course", $courseId);
                } 
            }
        }
    }
    
    public static function initAddCourse() {
        $_POST["addCourseName"] = $_POST["addCourseDescription"] = $_POST["addCoursePrice"] = "";
    }

    public static function getCourseByIdAction() {
        global $showSchoolCounter;
        global $showCourseForm;
        global $showStudentForm;

        if (!empty($_POST["courseId"])) {
            $showSchoolCounter = "none";
            $showCourseForm = "none";
            $showStudentForm = "none";

            if (isset($_POST["showUpdateCourseEdit"])) {
                $showUpdateCourseEdit = "block";
                $showUpdateCourseInformation = "none";             
                $showUpdateCourseBtn = "block";             
            } else {
                $showUpdateCourseEdit = "none";
                $showUpdateCourseInformation = "block";
                $showUpdateCourseBtn = "none";           
            }
            
            $courseId = $_POST["courseId"];

            $course = DatabaseHandler::find("course", $courseId);

            $deleteBtn = "";

            if ($course) {
                $numberOfStudents = SchoolHandler::countStudentsInCourses($course->getId());

                if ($numberOfStudents == 1) {
                    $studentKeyword = "Student";
                } else if ($numberOfStudents > 1 || $numberOfStudents < 1) {
                    $studentKeyword = "Students";
                } 
                
                if ($numberOfStudents <= 0) {
                    $deleteBtn = "<button class='btn btn-danger text-light w-25 float-right' id='deleteCourseBtn' style='display:$showUpdateCourseBtn;'><i class='fa fa-trash'></i></button>
                    <input type='hidden' name='deleteCourse' value='{$course->getId()}'>";
                }

                if ($courseStudents = SchoolHandler::courseStudents($course->getId())) {
                    $students = 
                    "<h4 class='card-text text-left p-2'><span class='badge badge-danger'>Students</span></h4>
                    <ul class='text-left'>";
                    foreach ($courseStudents as $value) {
                        $students .= "<li>" . strtoupper($value->FullName) . "</li>";
                    }
                    $students .= "</ul>";
                } else {
                    $students = "<p class='card-text text-left alert alert-info'><span><i class='fa fa-exclamation-circle mr-2'></i></span>This course does not have students right now</p>";
                }

                if (AdministratorHandler::checkIfSales()) {
                    $pencilTrashForm = "";
                } else {
                    $pencilTrashForm =
                    "<div class='card-header'>
                        <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST'>
                            <button class='btn btn-warning text-light w-25' id='editCourseBtn'><i class='fa fa-pencil'></i></button>
                            $deleteBtn
                        </form>
                    </div>";
                }

                return
                "<div id='editCourseContainer'>
                    <div class='card'>
                        $pencilTrashForm
                        <div id='courseInformation' style='display:$showUpdateCourseInformation;'>
                            <div class='card-header text-center'>
                                <img src='{$course->getImage()}' alt='{$course->getName()}' height='150'>
                            </div>
                            <div class='card-body text-center'>
                                <h4 class='card-title'>{$course->getName()}</h4>
                                <p class='card-text'>{$course->getDescription()}</p>
                                <h4 class='card-text p-2'><span class='badge badge-dark'>{$course->getPrice()}</span></h4>
                                $students
                            </div>
                        </div>
                        <div class='card-body' id='updateCourse' style='display:$showUpdateCourseEdit;'>
                             <div class='jumbotron bg-info text-light p-2'>
                                <h3>Edit Course</h3>
                            </div>
                            <form action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' method='POST' enctype='multipart/form-data' autocomplete='off' id='updateCourseForm'>
                                <div class='form-group'>
                                    <input type='text' name='courseName' id='courseName' class='form-control' placeholder='Name'
                                    value='" . ValidationHandler::preserveValue("courseName", $course->getName()) . "' data-validation='required custom' data-validation-regexp='^[a-zA-Z0-9 ]*$'>
                                </div>
                                <div class='form-group'>
                                    <textarea rows='5' cols='60' name='description' class='form-control' placeholder='Description' data-validation='required'>" . ValidationHandler::preserveValue("description", $course->getDescription()) . "</textarea>
                                </div>
                                <div class='form-group'>
                                    <input type='number' name='price' id='price' class='form-control' placeholder='Price'
                                    value='" . ValidationHandler::preserveValue("price", $course->getPriceWithout()) . "' data-validation='required number'>
                                </div>
                                <div class='form-group none' id='previewUpdateCourseImage'>
                                    <img src='' alt='Preview Image' id='previewCourseImageSrcUpdate' class='w-100'>
                                </div>
                                <div class='form-group'>
                                    <label class='custom-file w-100'>
                                        <input type='file' name='courseImage' id='courseImageInpUpdate' class='custom-file-input form-control'
                                        data-validation='mime size' data-validation-allowing='jpg, jpeg, png' data-validation-max-size='1M'>
                                        <span class='custom-file-control'></span>
                                    </label>
                                </div>
                                <div class='form-group'>
                                    <div class='jumbotron p-2 m-0'>
                                        <h5 class='card-text'><span class='badge badge-dark mr-2'>$numberOfStudents</span>$studentKeyword</h5>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <input type='submit' value='Submit' name='updateCourse' class='form-control btn btn-success'>
                                    <input type='hidden' value='{$course->getId()}' name='courseId' class='none'>
                                    <input type='hidden' value='{$course->getImageWithout()}' name='imageExist' class='none'>
                                    <input type='hidden' value='{$course->getName()}' name='nameExist' class='none'>
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