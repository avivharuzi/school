<?php

class SchoolHandler {
    private function __construct() {
    }

    public static function schoolCounter() {
        $students = DatabaseHandler::count("student");
        $courses = DatabaseHandler::count("course");

        return
        "<div class='jumbotron bg-info text-light'>
            <h3>COURSES<span class='badge badge-light ml-3 counter'>$courses</span></h3>
        </div>
        <div class='jumbotron bg-info text-light'>
            <h3>STUDENTS<span class='badge badge-light ml-3 counter'>$students</span></h3>
        </div>";
    }

    public static function checkIfExists($table, $field, $value) {
        $result = DatabaseHandler::whereOne($table, $field, $value);

        if ($result) {
            return true;
        } else {
            return false;
        }    
    }

    public static function insertCoursesForStudents($studentId, $courses) {
        $sql = "SELECT * FROM student_course WHERE StudentId = $studentId";
        $result = DatabaseHandler::full($sql);

        if ($result) {
            DatabaseHandler::delete("student_course", "StudentId", $studentId);
        }

        if ($courses !== NULL) {
            foreach ($courses as $value) {
                $sql = "INSERT INTO student_course (StudentId, CourseId) VALUES ($studentId, $value)";
                DatabaseHandler::insert($sql);
            }
        }
    }

    public static function studentCourses($studentId) {
        $sql = 
        "SELECT student_course.CourseId AS CourseId, course.Name AS CourseName FROM student_course
        LEFT JOIN student ON student_course.StudentId = student.Id
        LEFT JOIN course ON student_course.CourseId = course.Id
        WHERE student_course.StudentId = $studentId";

        return DatabaseHandler::full($sql);
    }

    public static function courseStudents($courseId) {
        $sql = 
        "SELECT student.FullName as FullName FROM student_course
        LEFT JOIN student ON student_course.StudentId = student.Id
        LEFT JOIN course ON student_course.CourseId = course.Id
        WHERE student_course.CourseId = $courseId";

        return DatabaseHandler::full($sql);
    }

    public static function countStudentsInCourses($courseId) {
        $sql = 
        "SELECT count(*) as NumberOfStudents FROM student_course
        LEFT JOIN student ON student_course.StudentId = student.Id
        LEFT JOIN course ON student_course.CourseId = course.Id
        WHERE student_course.CourseId = $courseId LIMIT 1";

        $result = DatabaseHandler::single($sql);

        if ($result) {
            if ($result->NumberOfStudents > 0) {
                return $result->NumberOfStudents;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public static function getImageById($table, $id) {
        $result = DatabaseHandler::find($table, $id);

        if ($result) {
            return $result->getImageWithout();
        } else {
            return "";
        }
    }

    public static function updateImage($table, $id, $image) {
        $sql = "UPDATE $table SET Image = '$image' WHERE Id = $id";
        return DatabaseHandler::update($sql);
    }

    public static function delete($table, $id) {
        $result = DatabaseHandler::delete($table, "Id", $id);

        if ($result) {
            return MessageHandler::success("This $table deleted successfully");
        } else {
            return MessageHandler::error("There was a problem please try again");
        }
    }

    public static function schoolEditActions() {
        $addCourse = CourseHandler::addCourseAction();
        $addStudent = StudentHandler::addStudentAction();
        $updateCourse = CourseHandler::updateCourseAction();
        $updateStudent = StudentHandler::updateStudentAction();
        $deleteCourse = CourseHandler::deleteCourseAction();
        $deleteStudent = StudentHandler::deleteStudentAction();

        if (!empty($addCourse)) {
            return $addCourse;
        }

        if (!empty($addStudent)) {
            return $addStudent;
        }

        if (!empty($updateCourse)) {
            return $updateCourse;
        }

        if (!empty($updateStudent)) {
            return $updateStudent;
        }

        if (!empty($deleteCourse)) {
            return $deleteCourse;
        }

        if (!empty($deleteStudent)) {
            return $deleteStudent;
        }
    }

    public static function schoolShowActions() {
        $getCourseById = CourseHandler::getCourseByIdAction();
        $getStudentById = StudentHandler::getStudentByIdAction();
        
        if (!empty($getCourseById)) {
            return $getCourseById;
        }

        if (!empty($getStudentById)) {
            return $getStudentById;
        }
    }
}

?>