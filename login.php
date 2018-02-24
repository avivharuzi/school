<?php

require_once("auth/config.php");

$loginAction = AdministratorHandler::loginAction();

$title = "Login";

?>

<?php require_once("layout/header.php"); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="jumbotron bg-dark text-light text-center mt-5">
                <h3>Login</h3>
            </div>
            <?php echo $loginAction; ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" autocomplete="off">
                <div class="form-group">
                    <input type="text" name="email" id="email" class="form-control" placeholder="Email" data-validation="required email" autofocus>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" data-validation="required">
                </div>
                <div class="form-group">
                    <input type="submit" name="login" class="btn btn-primary btn-block" value="Submit">
                </div>
            </form>    
        </div>
    </div>
</div>
<?php require_once("layout/footer.php"); ?>