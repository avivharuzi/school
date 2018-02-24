<?php

require_once("auth/config.php");

$showSchoolCounter = "block";
$showCourseForm ="none";
$showStudentForm ="none";

$schoolEditActions = SchoolHandler::schoolEditActions();
$schoolShowActions = SchoolHandler::schoolShowActions();

$title = "School";

?>

<?php require_once("layout/header.php"); ?>
<div class="row mt-5">
    <div class="col-lg-3">
        <?php echo CourseHandler::getCoursesData(); ?>
    </div>
    <div class="col-lg-3">
        <?php echo StudentHandler::getStudentsData(); ?>
    </div>
    <div class="col-lg-6">
        <?php 
        echo $schoolEditActions;
        echo $schoolShowActions; 
        ?>
        <div id="schoolCounter" style="display:<?php echo $showSchoolCounter ?>">
            <?php echo SchoolHandler::schoolCounter(); ?>
        </div>
        <div id="addCourseContainer" style="display:<?php echo $showCourseForm ?>">
            <div class="jumbotron bg-info text-light p-2">
                <h3>Add Course</h3>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" autocomplete="off" id="addCourseForm">
                <div class="form-group">
                    <input type="text" name="addCourseName" id="addCourseName" class="form-control" placeholder="Name"
                    value="<?php echo ValidationHandler::preserveValue("addCourseName"); ?>" data-validation="required custom" data-validation-regexp="^[a-zA-Z0-9 ]*$">
                </div>
                <div class="form-group">
                    <textarea rows="5" cols="60" name="addCourseDescription" class="form-control" placeholder="Description"
                    data-validation="required"><?php echo ValidationHandler::preserveValue("addCourseDescription"); ?></textarea>
                </div>
                <div class="form-group">
                    <input type="number" name="addCoursePrice" id="addCoursePrice" class="form-control" placeholder="Price"
                    value="<?php echo ValidationHandler::preserveValue("addCoursePrice"); ?>" data-validation="required number">
                </div>
                <div class="form-group none" id="previewAddCourseImage">
                    <img src="" alt="Preview Image" id="previewCourseImageSrcAdd" class="w-100">
                </div>
                <div class="form-group">
                    <label class="custom-file w-100">
                        <input type="file" name="addCourseImage" id="courseImageInpAdd" class="custom-file-input form-control"
                        data-validation="mime size" data-validation-allowing="jpg, jpeg, png" data-validation-max-size="1M">
                        <span class="custom-file-control"></span>
                    </label>
                </div>
                <div class="form-group">
                    <input type="submit" value="Submit" name="addCourse" class="form-control btn btn-success">
                </div>
            </form>
        </div>
        <div id="addStudentContainer" style="display:<?php echo $showStudentForm ?>">
            <div class="jumbotron bg-info text-light p-2">
                <h3>Add Student</h3>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" autocomplete="off" id="addStudentForm">
                <div class="form-group">
                    <input type="text" name="addStudentFullName" id="addStudentFullName" class="form-control" placeholder="Full Name"
                    value="<?php echo ValidationHandler::preserveValue("addStudentFullName"); ?>" data-validation="required custom" data-validation-regexp="^[a-zA-Z ]*$">
                </div>
                <div class="form-group">
                    <input type="text" name="addStudentEmail" id="addStudentEmail" class="form-control" placeholder="Email"
                    value="<?php echo ValidationHandler::preserveValue("addStudentEmail"); ?>" data-validation="required email">
                </div>
                <div class="form-group">
                    <input type="text" name="addStudentPhone" id="addStudentPhone" class="form-control phone" placeholder="Phone"
                    value="<?php echo ValidationHandler::preserveValue("addStudentPhone"); ?>" data-validation="required length" data-validation-length="min12">
                </div>
                <div class="form-group">
                    <?php echo CourseHandler::getCoursesNames();  ?>
                </div>
                <div class="form-group none" id="previewAddStudentImage">
                    <img src="" alt="Preview Image" id="previewStudentImageSrcAdd" class="w-100">
                </div>
                <div class="form-group">
                    <label class="custom-file w-100">
                        <input type="file" name="addStudentImage" id="studentImageInpAdd" class="custom-file-input form-control"
                        data-validation="mime size" data-validation-allowing="jpg, jpeg, png" data-validation-max-size="1M">
                        <span class="custom-file-control"></span>
                    </label>
                </div>
                <div class="form-group">
                    <input type="submit" value="Submit" name="addStudent" class="form-control btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once("layout/footer.php"); ?>