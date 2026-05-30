<?php
//Data types in PHP
//1. String
$x = "Hello World";
//2. Integer
$y = 23;
//3. Float
$z = 3.14;
//4. Boolean
$w = true;

//var_dump() function is used to display the data type and value of a variable
// var_dump($x)."<br>";
// var_dump($y)."<br>";
// var_dump($z)."<br>";
// var_dump($w)."<br>";

var_dump($x);
echo "<br>";
var_dump($y);
echo "<br>";
var_dump($z);
echo "<br>";
var_dump($w);
echo "<br>";


//5. Array
$arr = array("apple", "banana", "cherry");
//6. Object
class Car {
    public $color;
    public function __construct($color) {
        $this->color = $color;
    }
}
//7. NULL
$u = NULL;

//8. Resource
$r = fopen("file.txt", "r");

//9. Callable
function myFunction() {
    echo "Hello from myFunction!";
    }
$callable = 'myFunction';
echo $callable(); // Output: Hello from myFunction!

//10. Iterable
$iterable = [1, 2, 3, 4, 5];
foreach ($iterable as $value) {
    echo $value . "<br>";
}

//11. Mixed
$mixed = "This can be any type of data";


//12. Void
function voidFunction(): void {
    echo "This function does not return anything.";
}
$callableVoid = 'voidFunction';
echo $callableVoid(); // Output: This function does not return anything.


//13. Never
//This function will never return a value and will always throw an exception
//Example of never return type in PHP

function neverFunction(): never {
    throw new Exception("This function will never return.");
}

//14. Object
//Example of object data type in PHP

class Person {
    public $name;
    public function __construct($name) {
        $this->name = $name;
    }
}

echo "<br>";

$person = new Person("Rambhu Singh");
echo $person->name ."<br>"; // Output: Rambhu Singh



?>