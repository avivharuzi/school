<?php

require_once("auth/config.php");

$showAdministratorCounter = "block";
$showAdministratorForm ="none";

$administratorActions = AdministratorHandler::administratorActions();
$administratorShowActions = AdministratorHandler::administratorShowActions();

$title = "Administration";

?>

<?php require_once("layout/header.php"); ?>
<div class="row mt-5">
    <div class="col-lg-6">
        <?php echo AdministratorHandler::getAdministratorsData(); ?>
    </div>
    <div class="col-lg-6">
        <?php 
        echo $administratorActions;
        echo $administratorShowActions;
        ?>
        <div id="administratorCounter" style="display:<?php echo $showAdministratorCounter ?>">
            <?php echo AdministratorHandler::administratorCounter(); ?>
        </div>
        <div id="addAdministratorContainer" style="display:<?php echo $showAdministratorForm ?>">
            <div class="jumbotron bg-info text-light p-2">
                <h3>Add Administrator</h3>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" autocomplete="off" id="addAdministratorForm">
                <div class="form-group">
                    <input type="text" name="addAdministratorFullName" id="addAdministratorFullName" class="form-control" placeholder="Full Name"
                    value="<?php echo ValidationHandler::preserveValue("addAdministratorFullName"); ?>" data-validation="required custom" data-validation-regexp="^[a-zA-Z ]*$">
                </div>
                <div class="form-group">
                    <input type="text" name="addAdministratorEmail" id="addAdministratorEmail" class="form-control" placeholder="Email"
                    value="<?php echo ValidationHandler::preserveValue("addAdministratorEmail"); ?>" data-validation="required email">
                </div>
                <div class="form-group">
                    <input type="text" name="addAdministratorPhone" id="addAdministratorPhone" class="form-control phone" placeholder="Phone"
                    value="<?php echo ValidationHandler::preserveValue("addAdministratorPhone"); ?>" data-validation="required length" data-validation-length="min12">
                </div>
                <div class="form-group">
                    <input type="password" name="addAdministratorPassword" id="addAdministratorPassword" class="form-control" placeholder="Password"
                    value="<?php echo ValidationHandler::preserveValue("addAdministratorPassword"); ?>" data-validation="required custom" data-validation-regexp="^[A-Za-z0-9!@#$%^&*()_]{6,20}$">
                </div>
                <div class="form-group">
                    <input type="password" name="addAdministratorPasswordConfirm" id="addAdministratorPasswordConfirm" class="form-control" placeholder="Password Confirmation"
                    value="<?php echo ValidationHandler::preserveValue("addAdministratorPasswordConfirm"); ?>"  data-validation="required custom confirmation"
                    data-validation-regexp="^[A-Za-z0-9!@#$%^&*()_]{6,20}$" data-validation-confirm="addAdministratorPassword">
                </div>
                <?php echo AdministratorHandler::selectRoles(); ?>
                <div class="form-group none" id="previewAddAdministratorImage">
                    <img src="" alt="Preview Image" id="previewAdministratorImageSrcAdd" class="w-100">
                </div>
                <div class="form-group">
                    <label class="custom-file w-100">
                        <input type="file" name="addAdministratorImage" id="administratorImageInpAdd" class="custom-file-input form-control"
                        data-validation="mime size" data-validation-allowing="jpg, jpeg, png" data-validation-max-size="1M">
                        <span class="custom-file-control"></span>
                    </label>
                </div>
                <div class="form-group">
                    <input type="submit" value="Submit" name="addAdministrator" class="form-control btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once("layout/footer.php"); ?>