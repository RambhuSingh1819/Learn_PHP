<?php

//function in PHP
//1. function without parameters
//2. function with parameters
//3. function with return value
//4. function with default parameters
//5. function with variable number of parameters
//6. recursive function
//7. anonymous function
//8. arrow function
//9. variable scope
//10. global variable
//11. static variable
//12. pass by value
//13. pass by reference
//14. function with type hinting
//15. function with return type declaration
//16. function with variable variables
//17. randomnumber funtion generator

//Example of function without parameters in PHP
function sayHello() {
    echo "Hello, World!";
}
sayHello();


//------------------------------------------------------------------------------

//Example of function with parameters in PHP
function greet($name) {
    echo "Hello, " . $name . "!";

}
greet("Rambhu Singh");
//Output: Hello, Rambhu Singh!

//------------------------------------------------------------------------------

//Example of function with return value in PHP
function add($a, $b) {
    return $a + $b;
}
$result = add(5, 10);
echo $result ."<br>";
//Output: 15

//------------------------------------------------------------------------------

//Example of function with default parameters in PHP
function greetWithDefault($name = "Guest") {
    echo "Hello, " . $name . "!";
}
greetWithDefault(); // Output: Hello, Guest!
greetWithDefault("Bob"); // Output: Hello, Bob!

//------------------------------------------------------------------------------

//pass by value in PHP
//Example of pass by value in PHP
function increment($num) {
    $num++;
    echo "Inside function: " . $num ."<br>";
}
$value = 5;
increment($value); // Output: Inside function: 6
echo "Outside function: " . $value ."<br>"; // Output: Outside function: 5

//------------------------------------------------------------------------------

//pass argument  by reference in PHP
//Example of pass by reference in PHP
function incrementByReference(&$num) {
    $num++;
    echo "Inside function: " . $num ."<br>";
}
$value = 5;
incrementByReference($value); // Output: Inside function: 6
echo "Outside function: " . $value ."<br>"; // Output: Outside function: 6

//------------------------------------------------------------------------------


//Example of function with variable number of parameters in PHP
function sum(...$numbers) {
    $total = 0;
    foreach ($numbers as $number) {
        $total += $number;
    }
    return $total;
}

echo sum(1, 2, 3) ."<br>"; // Output: 6
echo sum(4, 5, 6, 7) ."<br>"; // Output: 22
echo sum(7,6, 7) ."<br>"; // Output: 20

//------------------------------------------------------------------------------

//Recursive function in PHP

//Example of recursive function in PHP
function factorial($n) {
    if ($n == 0) {
        return 1;
    } else {
        return $n * factorial($n - 1);
    }
}
echo factorial(5) ."<br>"; // Output: 120
echo factorial(0) ."<br>"; // Output: 1


//------------------------------------------------------------------------------

//Anonymous function in PHP
//Example of anonymous function in PHP
$greet = function($name) {
    echo "Hello, " . $name . "!";
};
$greet("Charlie"); // Output: Hello, Charlie!


//------------------------------------------------------------------------------

//Arrow function in PHP
//Example of arrow function in PHP
$greetArrow = fn($name) => "Hello, $name ". "!<br>";
echo $greetArrow("Dave") ."<br>"; // Output: Hello, Dave!

//------------------------------------------------------------------------------

//Variable scope in PHP
//Example of variable scope in PHP
function testScope() {
    $localVar = "I am a local variable";
    echo $localVar ."<br>";
}
testScope();
//Output: I am a local variable

//------------------------------------------------------------------------------

//Global variable in PHP
//Example of global variable in PHP
$globalVar = "I am a global variable";
function testGlobal() {
    global $globalVar;
    echo $globalVar ." this is global variable" ."<br>";
}
testGlobal();
//output: I am a global variable this is global variable

//Static variable in PHP
//Example of static variable in PHP

//------------------------------------------------------------------------------
function testStatic() {
    static $count = 0;
    $count++;
    echo "Count: " . $count ."<br>";
}
testStatic(); // Output: Count: 1
testStatic(); // Output: Count: 2
testStatic(); // Output: Count: 3

//------------------------------------------------------------------------------

//Function with type hinting in PHP
//Example of function with type hinting in PHP
//explaining type hinting in PHP
//Type hinting allows you to specify the expected data type of function parameters and return values.
//This helps to catch errors early and improve code readability.
//In PHP, you can use type hinting for scalar types (int, float, string, bool), arrays, callable, and class/interface types.
//You can also specify return types for functions. If a function is called with
//an argument that does not match the specified type hint, a TypeError will be thrown.
function addWithTypeHinting(int $a, int $b): int {
    return $a + $b;
}
echo addWithTypeHinting(5, 10) ."<br>"; // Output: 15


//------------------------------------------------------------------------------

//Function with return type declaration in PHP
//Example of function with return type declaration in PHP
function greetWithReturnType(string $name): string {
    return "Hello, " . $name . "!";
}
echo greetWithReturnType("Eve") ."<br>"; // Output: Hello, Eve!

//------------------------------------------------------------------------------

//Function with variable variables in PHP
//Example of function with variable variables in PHP
function variableVariables($varName) {
    $$varName = "This is a variable variable";
    echo $$varName ."<br>";
}
variableVariables("myVar"); // Output: This is a variable variable

//genetaing random number in PHP
//Example of generating random number in PHP
$randomNumber = rand(1, 100);
echo "Random number between 1 and 100: " . $randomNumber ."<br>";

//------------------------------------------------------------------------------
//Example of XML functions in PHP
$xmlData = "<person><name>John</name><age>30</age><city>New York</city></person>";
$xml = simplexml_load_string($xmlData); // Load XML string into a SimpleXMLElement object
echo $xml->name ."<br>"; // Output: John
echo $xml->age ."<br>"; // Output: 30
echo $xml->city ."<br>"; // Output: New York
$xml->name = "Jane"; // Modify XML data
echo $xml->asXML() ."<br>"; // Output: <person><name>Jane</name><age>30</age><city>New York</city></person>




?>