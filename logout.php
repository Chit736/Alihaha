 <?php 
    session_start();
    session_unset();
    session_destroy();
    header("location: login.php? success=".urlencode("You have been logged out successfully."));
    exit();


?>