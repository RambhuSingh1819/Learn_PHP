<?php
//section 1: Introduction of session in PHP
//A session is a way to store information (in variables) to be used across multiple pages.
//Unlike cookies, the information is not stored on the user's computer. A session is started with the 
//session_start() function. Session variables are stored in the $_SESSION superglobal array.
//You can set and get session variables using this array. To end a session, you can use the session_destroy()
//function, which destroys all data registered to a session. You can also use unset() to remove specific
//session variables. Sessions are commonly used for user authentication, shopping carts,
//and other scenarios where you need to maintain state across multiple pages.

//Example of session functions in PHP
session_start(); // Start a new session or resume an existing session
$_SESSION["username"] = "JohnDoe"; // Set a session variable
echo $_SESSION["username"] ."<br>"; // Get a session variable
unset($_SESSION["username"]); // Unset a session variable
echo isset($_SESSION["username"]) ."<br>"; // Check if a session variable is set
session_destroy(); // Destroy the session

//Note: When you call session_destroy(), it destroys all data registered to a session,
//but it does not unset any of the global variables associated with the session,
//nor does it unset the session cookie. To completely remove the session, you should also unset the session
//variables and delete the session cookie.

//Example of completely removing a session in PHP
session_start(); // Start a new session or resume an existing session
$_SESSION["username"] = "JohnDoe"; // Set a session variable
echo $_SESSION["username"] ."<br>"; // Get a session variable
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
setcookie(session_name(), '', time() - 3600); // Delete the session cookie

//Example of using sessions to maintain user authentication in PHP
session_start(); // Start a new session or resume an existing session
if (isset($_POST["username"]) && isset($_POST["password"])) {
    // In a real application, you would validate the username and password against a database
    if ($_POST["username"] === "admin" && $_POST["password"] === "password") {
        $_SESSION["loggedin"] = true; // Set a session variable to indicate the user is logged in
        $_SESSION["username"] = "admin"; // Set a session variable to store the username
        echo "Login successful! Welcome, " . $_SESSION["username] ."<br>";
    } else {
        echo "Invalid username or password.<br>";
    }
} else {
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        echo "Welcome back, " . $_SESSION["username"] ."<br>";
    } else {
        echo "Please log in.<br>";
    }
}

?>