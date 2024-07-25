<?php
session_start(); //Start the session
session_destroy(); //Destroy all session
header('Location: ../'); //Redirect to the login page
exit; //Ensure no further code is executed after the redirection
?>