<?php
//loop statements in PHP
//1. while loop
//2. do-while loop
//3. for loop
//4. foreach loop

//---------------------------------------------------------------

//Example of while loop in PHP
//The while loop is used to execute a block of code as long as a specified condition is true.
// The condition is evaluated before the execution of the loop body, 
//so if the condition is false at the beginning, the loop body will not be executed at all.
//Output: 2, 3, 4, 5, 6
$i = 1;
while ($i <= 5) {
    $i++;
    echo $i ."<br>";
}

//---------------------------------------------------------------
//Example of do-while loop in PHP
//The do-while loop is similar to the while loop, but it guarantees that the loop
//body will be executed at least once, even if the condition is false at the beginning.

//In a do-while loop, the condition is evaluated after the loop body is executed,
//so the loop will always execute at least once, even if the condition is false at the beginning.
//Output: 2, 3, 4, 5, 6
$j = 1;
do {
    $j++;
    echo $j ."<br>";
} while ($j <= 5);
echo $j ."<br>";

//---------------------------------------------------------------
//Example of for loop in PHP
for ($k = 1; $k <= 5; $k++) {
    $k++;
    echo $k ."<br>";
}
//Output: 2, 4, 6, 8, 10
echo $j ."<br>";

//---------------------------------------------------------------
//Example of foreach loop in PHP
$fruits = array("apple", "banana", "orange");
foreach ($fruits as $fruit) {
    echo $fruit ."<br>";
}
echo $fruits[""] ."<br>";

//Output: apple, banana, orange

//----------------------------------------------------------------

//Nested loop in PHP
//Example of nested loop in PHP
for ($i = 1; $i <= 3; $i++) {
    for ($j = 1; $j <= 3; $j++) {
        echo "i = " . $i . ", j = " . $j ."<br>";
    }
}
echo $fruits[""] ."<br>";
//Output: i = 1, j = 1
//        i = 1, j = 2
//        i = 1, j = 3
//        i = 2, j = 1
//        i = 2, j = 2
//        i = 2, j = 3
//        i = 3, j = 1
//        i = 3, j = 2
//        i = 3, j = 3

//------------------------------------------------------------------------------

//Break statement in PHP
//The break statement is used to exit a loop or switch statement before it has completed its normal
for ($i = 1; $i <= 3; $i++) {
    if ($i == 2) {
        break;
    }
    echo "i = " . $i ."<br>";
}
echo $fruits[""] ."<br>";
//Output: i = 1

//------------------------------------------------------------------------------

//Continue statement in PHP
//The continue statement is used to skip the current iteration of a loop and continue with the next
for ($i = 1; $i <= 3; $i++) {
    if ($i == 2) {
        continue;
    }
    echo "i = " . $i ."<br>";
}
echo $fruits[""] ."<br>";
//Output: i = 1
//        i = 3

//------------------------------------------------------------------------------

//Goto statement in PHP
//The goto statement is used to jump to a specific label in the code.
goto label;
echo "This will be skipped";
label:
echo "This will be executed";

//Nested goto statement in PHP
goto label1;
echo "This will be skipped";
label1:
goto label2;
echo "This will be skipped";
label2:
echo "This will be executed";



//Example of goto statement in PHP
$number = 5;
if ($number < 0) {
    goto negative;
} elseif ($number == 0) {
    goto zero;
} else {
    goto positive;
}


//Negative label
negative:
echo "The number is negative";
goto end;
//Zero label
zero:
echo "The number is zero";
goto end;
//Positive label
positive:
echo "The number is positive";
goto end;
//End label
end:
echo "This is the end of the program";
goto end;


?>