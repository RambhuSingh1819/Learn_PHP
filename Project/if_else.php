
<form method = "post">
    Enter  Marks:
    <input type = "number" name = "marks">
    <input type = "submit" value = "Done"
</form>
<?php
//If-Else Statements in PHP
//1. if statement
//2. if-else statement
//3. if-elseif-else statement
//Example of If-Else Statements in PHP
//Using if-else statement to compare two numbers


//Example of if-else statement in PHP
$a = 10;
$b = 10;
if ($a == $b) {
    echo "a is equal to b";
} elseif ($a > $b) {
    echo "a is greater than b";
} else {
    echo "a is less than b";
}
//xor operator in PHP
//The xor operator returns true if either of the operands is true, but not both.
//Example of xor operator in PHP
$a = true;
$b = false;
if ($a == $b) {
    echo "Both a and b are true";
} elseif ($a xor $b) {
    echo "Either a or b is true, but not both";
}else {
    echo "Both a and b are false";
}

$a = false;
$b = false;
if ($a xor $b) {
    echo "Either a or b is true, but not both";
} else {
    echo "Both a and b are false";
}


//program for finding the  percentage of the marks obtained by a student and grade according to the percentage

if(isset($_POST['marks'])) {
    $marks = $_POST['marks'];
    if($marks < 0 || $marks > 100) {
        echo "Please enter a valid marks between 0 and 100.";
        exit;
    }
    echo "<br>";
    $totalMarks = 100;
    $percentageCalculator = ($marks / $totalMarks) * 100;
    if($percentageCalculator >= 95) echo "Grade: A+";
    else if($percentageCalculator >= 90) echo "Grade: A";
    else if($percentageCalculator >= 80) echo "Grade: B";
    else if($percentageCalculator >= 70) echo "Grade: C";
    else if($percentageCalculator >= 60) echo "Grade: D";
    else echo "Grade: F";
}
?>