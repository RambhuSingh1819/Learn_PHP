<?php
$file = fopen("example.txt", "w"); // Open a file for writing
fwrite($file, "Hello, World!"); // Write to the file
fclose($file); // Close the file
echo file_get_contents("example.txt") ."<br>"; // Read the contents of the file
echo file_exists("example.txt") ."<br>"; // Check if the file exists
echo filesize("example.txt") ."<br>"; // Get the size of the file
echo filemtime("example.txt") ."<br>"; // Get the last modified time of the file
echo filetype("example.txt") ."<br>"; // Get the type of the file
echo is_file("example.txt") ."<br>"; // Check if it is a file
echo is_dir("example.txt") ."<br>"; // Check if it is a directory
echo is_readable("example.txt") ."<br>"; // Check if the file is readable
echo is_writable("example.txt") ."<br>"; // Check if the file is writable
echo is_executable("example.txt") ."<br>"; // Check if the file is executable
echo unlink("example.txt") ."<br>"; // Delete the file
?>