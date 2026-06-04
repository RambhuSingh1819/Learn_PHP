<?php

//Operators in PHP
//1. Arthimetic Operators
//2. Assignment Operators
//3. Comparison Operators
//4. Logical Operators
//5. Increment and Decrement Operators
//6. String Operators
//7. Array Operators
//8. Bitwise Operators
//9. Error Control Operators
//10. Execution Operators
//11. Conditional Ternary Operators
//12. Spaceship Operator
//13. Null Coalescing Operator

//Assignment Operators in PHP
//1. = assignment operator
//2. += addition assignment operator
//3. -= subtraction assignment operator
//4. *= multiplication assignment operator
//5. /= division assignment operator
//6. %= modulus assignment operator
//7. **= exponentiation assignment operator
//Example of Assignment Operators in PHP

$a = 5;
$a += 10; // equivalent to $a = $a + 10
echo $a ."<br>"; // Output: 15
$a -= 3; // equivalent to $a = $a - 3
echo $a ."<br>"; // Output: 12
$a *= 2; // equivalent to $a = $a * 2
echo $a ."<br>"; // Output: 24
$a /= 4; // equivalent to $a = $a / 4
echo $a ."<br>"; // Output: 6
$a %= 5; // equivalent to $a = $a % 5
echo $a ."<br>"; // Output: 1
$a **= 3; // equivalent to $a = $a ** 3
echo $a ."<br>"; // Output: 1

//-------------------------------------------------------

//Comparison Operators in PHP
//1. == equal to operator
//2. === identical operator
//3. != not equal to operator
//4. !== not identical operator
//5. > greater than operator
//6. < less than operator
//7. >= greater than or equal to operator
//8. <= less than or equal to operator
//Example of Comparison Operators in PHP
$a = 5;
$b = 10;
//Using the comparison operators to compare $a and $b

//check onequality of $a and $b
echo ($a == $b) ."<br>"; // Output: false

//check the value and type of $a and $b
echo ($a === $b) ."<br>"; // Output: false

//check inequality of $a and $b
echo ($a != $b) ."<br>"; // Output: true
echo ($a <> $b)."<br>"; // Output: true (alternative to !=)

//check non-identity of $a and $b
//check if $a is not equal to $b or if they are of different types
echo ($a !== $b) ."<br>"; // Output: true

//check if $a is greater than $b
echo ($a > $b) ."<br>"; // Output: false

//check if $a is less than $b
echo ($a < $b) ."<br>"; // Output: true
echo ($a >= $b) ."<br>"; // Output: false
echo ($a <= $b) ."<br>"; // Output: true

//spaceship operator in PHP
//The spaceship operator (<=>) is used to compare two expressions and returns -1, 0, or 1 depending on whether the left expression is less than, equal to, or greater than the right expression.
echo ($a <=> $b) ."<br>"; // Output: -1 (spaceship operator, returns -1 if $a is less than $b, 0 if they are equal, and 1 if $a is greater than $b)


//Logical Operators in PHP
//1. && and operator
//2. || or operator
//3. ! not operator
//Example of Logical Operators in PHP
$a = true;
$b = false;
echo ($a && $b) ."<br>"; // Output: false
echo ($a || $b) ."<br>"; // Output: true
echo (!$a) ."<br>"; // Output: false
echo (!$b) ."<br>"; // Output: true

//-------------------------------------------------------

//Increment and Decrement Operators in PHP
//1. ++ increment operator
//2. -- decrement operator
//Example of Increment and Decrement Operators in PHP
$a = 5;
echo $a++ ."<br>"; // Output: 5 (post-increment)
echo $a ."<br>"; // Output: 6
echo ++$a ."<br>"; // Output: 7 (pre-increment)
echo $a ."<br>"; // Output: 7
echo $a-- ."<br>"; // Output: 7 (post-decrement)
echo $a ."<br>"; // Output: 6
echo --$a ."<br>"; // Output: 5 (pre-decrement)
echo $a ."<br>"; // Output: 5

//-------------------------------------------------------

//String Operators in PHP
//1. . concatenation operator
//2. .= concatenation assignment operator
//Example of String Operators in PHP
$str1 = "Hello";
$str2 = "World";
echo $str1 . " " . $str2 ."<br>"; // Output: Hello
$str1 .= " " . $str2; // equivalent to $str1 = $str1 . " " . $str2
echo $str1 ."<br>"; // Output: Hello World
//---------------------------------------------------------------

//Array Operators in PHP
//1. + union operator
//2. == equality operator
//3. === identity operator
//4. != inequality operator
//5. !== non-identity operator
//Example of Array Operators in PHP
$array1 = array("a" => "apple", "b" => "banana");
$array2 = array("b" => "grape", "c" => "orange");
$result = $array1 + $array2; // union of $array1 and $array
print_r($result); // Output: Array ( [a] => apple [b] => banana [c] => orange )
echo ($array1 == $array2) ."<br>"; // Output: false
echo ($array1 === $array2) ."<br>"; // Output: false
//check if $array1 and $array2 are not equal
echo ($array1 != $array2) ."<br>"; // Output: true
//check if $array1 and $array2 are not identical
echo ($array1 !== $array2) ."<br>"; // Output: true

//---------------------------------------------------------------

//Bitwise Operators in PHP
//1. & bitwise AND operator
//2. | bitwise OR operator
//3. ^ bitwise XOR operator
//4. ~ bitwise NOT operator
//5. << left shift operator
//6. >> right shift operator
//Example of Bitwise Operators in PHP
$a = 5; // In binary: 0101
$b = 3; // In binary: 0011
echo ($a & $b) ."<br>"; // Output: 1 (In binary
echo ($a | $b) ."<br>"; // Output: 7 (In binary: 0111)
echo ($a ^ $b) ."<br>"; // Output: 6 (In binary
echo (~$a) ."<br>"; // Output: -6 (In binary: 1010)
echo ($a << 1) ."<br>"; // Output: 10 (In binary
echo ($a >> 1) ."<br>"; // Output: 2 (In binary: 0010)

//------------------------------------------------------------------------------

//Error Control Operators in PHP
//1. @ error control operator
//Example of Error Control Operators in PHP
// Suppresses the error message when trying to read a non-existent file
$result = @file_get_contents("nonexistentfile.txt");
if ($result === false) {
// Output: Error occurred while trying to read the file.
// Display a custom error message instead of the default error message
// Output: Error occurred while trying to read the file.
    echo "Error occurred while trying to read the file.";
} else {
// If the file is read successfully, display its contents
// Output: (contents of the file)
// Note: Since the file does not exist, this block will not be executed
// Output: (contents of the file)
    echo $result;
}

//Execution Operators in PHP
//1. ` backticks operator
//Example of Execution Operators in PHP
$output = `ls -l`; // Executes the command and captures the output
echo "<pre>$output</pre>"; // Displays the output of the command

//------------------------------------------------------------------

//logical Operators in PHP
echo $output ."<br>";
echo "Logical Operators in PHP<br> ";
echo ($a && $b) ."<br>"; // Output: false
echo ($a || $b) ."<br>"; // Output: true
echo (!$a) ."<br>"; // Output: false
echo (!$b) ."<br>"; // Output: true
echo ($a || $b) ."<br>";
$result = @file_get_contents("");
if ($result === false) {
    echo "Error occurred while trying to read the file.";
} else {
    echo $result;
}
$output = "";
echo "<pre>$output</pre>";


//----------------------------------------------------------------------
//contitional Ternary Operators in PHP
//1. ? : ternary operator
//Example of Ternary Operators in PHP
$a = 5;
$result = ($a > 10) ? "a is greater than 10" : "a is not greater than 10";
echo $result ."<br>"; // Output: a is not greater than 10

$check = ($a % 2 == 0) ? "a is even" : "a is odd";
echo $check ."<br>"; // Output: a is odd

//------------------------------------------------------


?>