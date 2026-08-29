<?php
//what is cookies in PHP?
//A cookie is a small piece of data that is stored on the user's computer by the web server.
//Cookies are commonly used to store user preferences, session information,
//and other data that needs to persist across multiple page requests.
//In PHP, you can set a cookie using the setcookie() function, which takes
//several parameters including the name of the cookie, its value, and its expiration time.
//You can access the value of a cookie using the $_COOKIE superglobal array.
//To delete a cookie, you can set its expiration time to a past date.
//Cookies are sent to the server with every HTTP request, so they can be used for
//tracking user behavior and maintaining state in web applications.


//Example of cookie functions in PHP
setcookie("username", "JohnDoe", time() + 3600); // Set a cookie that expires in 1 hour
echo $_COOKIE["username"] ."<br>"; // Get a cookie
setcookie("username", "", time() - 3600); // Delete a cookie
echo isset($_COOKIE["username"]) ."<br>"; // Check if a cookie is set

//Note: When you set a cookie using the setcookie() function, the cookie is not
//immediately available in the $_COOKIE superglobal array. The cookie will only
//be available in the $_COOKIE array on subsequent page requests after it has been set.
//This is because cookies are sent to the server with every HTTP request, and the $_COOKIE array is
//populated with the cookies that are sent by the client in the current request. 
//Therefore, if you try to access a cookie immediately after setting it, it will
//not be available in the $_COOKIE array until the next page request.



?>