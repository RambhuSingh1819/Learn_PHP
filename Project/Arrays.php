<?php
    //Arrays in PHP
    //1. Indexed Arrays
    //2. Associative Arrays
    //3. Multidimensional Arrays


    //Example of indexed array in PHP
    //Indexed arrays use numeric indices to access the values in the array.
    //The indices start from 0 and increase by 1 for each element in the array.

    $fruits = array("Apple", "Banana", "Orange");
    echo $fruits[0] ."<br>"; // Output: Apple
    echo $fruits[1] ."<br>"; // Output: Banana
    echo $fruits[2] ."<br>"; // Output: Orange
    echo $fruits[3] ."<br>";// Output: Undefined offset: 3
    echo $fruits[4] ."<br>";// Output: Undefined offset: 4
    echo $fruits[5] ."<br>";// Output: Undefined offset: 5

//------------------------------------------------------------------------------

    //Example of associative array in PHP
    //Associative arrays use named keys that you assign to them. This allows you
    //to access the values in the array using the keys instead of numeric indices.
    $person = array("name" => "John", "age" => 30, "city" => "New York");
    echo $person["name"] ."<br>"; // Output: John
    echo $person["age"] ."<br>"; // Output: 30
    echo $person["city"] ."<br>"; // Output: New York
    echo $person["country"] ."<br>"; // Output: Undefined index: country

//------------------------------------------------------------------------------

    //Example of multidimensional array in PHP
    //Multidimensional arrays are arrays that contain other arrays as their elements.

    $students = array(
        array("name" => "Alice", "age" => 20, "grade" => "A"),
        array("name" => "Bob", "age" => 22, "grade" => "B"),
        array("name" => "Charlie", "age" => 21, "grade" => "A")
    );
    echo $students[0]["name"] ."<br>"; // Output: Alice
    echo $students[1]["age"] ."<br>"; // Output: 22
    echo $students[2]["grade"] ."<br>"; // Output: A
    echo $students[3]["name"] ."<br>"; // Output: Undefined index: 3
    echo $students[4]["age"] ."<br>"; // Output: Undefined index: 4
    echo $students[5]["grade"] ."<br>"; // Output: Undefined index: 5
    echo $students[0]["country"] ."<br>"; // Output: Undefined index: country
    echo $students[1]["city"] ."<br>"; // Output: Undefined index: city
    echo $students[2]["state"] ."<br>"; // Output: Undefined index: state

//------------------------------------------------------------------------------

    //Array functions in PHP
    //1. count() - counts the number of elements in an array
    // sizeof() - counts the number of elements in an array (alias of count())
    //2. array_push() - adds one or more elements to the end of an array
    //3. array_pop() - removes the last element from an array
    //4. array_shift() - removes the first element from an array
    //5. array_unshift() - adds one or more elements to the beginning of an array
    //6. array_merge() - merges one or more arrays into one array
    //7. array_slice() - extracts a portion of an array
    //8. array_splice() - removes a portion of an array and replaces it with something else
    //9. array_search() - searches an array for a specific value and returns the key if found
    //10. in_array() - checks if a value exists in an array
    //
    //Example of array functions in PHP
    $numbers = array(1, 2, 3, 4, 5);
    echo count($numbers) ."<br>"; // Output: 5

    //count() function is used to count the number of elements in the $numbers array, which is 5 in this case. The sizeof() function can also be used as an alias for count(), so sizeof($numbers) would also return 5.
    echo sizeof($numbers) ."<br>"; // Output: 5

    // The array_push() function adds one or more elements to the end of an array. It takes the array as the first argument and the elements to be added as subsequent arguments. The function returns the new number of elements in the array after the elements have been added.
    array_push($numbers, 6, 7);
    // After this operation, the $numbers array will contain the elements 1, 2, 3, 4, 5, 6, and 7. The function will return the new count of the array, which is 7 in this case.
    echo count($numbers) ."<br>"; // Output: 7

    print_r($numbers); // Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 [6] => 7 )

    array_pop($numbers);
    print_r($numbers); // Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 )

    array_shift($numbers);
    print_r($numbers); // Output: Array ( [0] => 2 [1] => 3 [2] => 4 [3] => 5 [4] => 6 )

    array_unshift($numbers, 1);
    print_r($numbers); // Output: Array ( [0] => 1 [1] => 2 [2] => 3 [3] => 4 [4] => 5 [5] => 6 )

    $array1 = array("a" => "apple", "b" => "banana");
    $array2 = array("c" => "orange", "d" => "grape");
    $mergedArray = array_merge($array1, $array2);
    print_r($mergedArray); // Output: Array ( [a] => apple [b] => banana [c] => orange [d] => grape )

    $slicedArray = array_slice($numbers, 2, 3);
    print_r($slicedArray); // Output: Array ( [0] => 3 [1] => 4 [2] => 5 )

    array_splice($numbers, 2, 3, array(10, 11, 12));
    print_r($numbers); // Output: Array ( [0] => 1 [1] => 2 [2] => 10 [3] => 11 [4] => 12 [5] => 6 )

    echo array_search(10, $numbers) ."<br>"; // Output: 2

    echo in_array(5, $numbers) ."<br>"; // Output: false
    
    echo in_array(12, $numbers) ."<br>"; // Output: true

//------------------------------------------------------------------------------

    //Example of array iteration in PHP
    //1. foreach loop
    //2. for loop
    //3. while loop
    //4. do-while loop
    $fruits = array("Apple", "Banana", "Orange");
    //Example of foreach loop in PHP
    foreach ($fruits as $fruit) {
        echo $fruit ."<br>";
    }
    //Output: Apple, Banana, Orange

    //Example of for loop in PHP
    for ($i = 0; $i < count($fruits); $i++) {
        echo $fruits[$i] ."<br>";
    }
//Output: Apple, Banana, Orange

//------------------------------------------------------------------------------
    
    //Example of while loop in PHP
    $i = 0;
    while ($i < count($fruits)) {
        echo $fruits[$i] ."<br>";
        $i++;
    }
    //Output: Apple, Banana, Orange

//------------------------------------------------------------------------------    

//use of foreach in associative array
    $person = array("name" => "John", "age" => 30, "city" => "New York");
    foreach ($person as $key => $value) {
        echo $key . ": " . $value ."<br>";
    }
    //Output: name: John, age: 30, city: New York

//Example of do-while loop in PHP
    $j = 1;
    do {
        echo $j ."<br>";
        $j++;
    } while ($j <= 5);
    //Output: 1, 2, 3, 4, 5

//------------------------------------------------------------------------------    

//use of foreach in multidimensional array
    $students = array(
        array("name" => "Alice", "age" => 20, "grade" => "A"),
        array("name" => "Bob", "age" => 22, "grade" => "B"),
        array("name" => "Charlie", "age" => 21, "grade" => "A")
    );
    foreach ($students as $student) {
        echo "Name: " . $student["name"] . ", Age: " . $student["age"] . ", Grade: " . $student["grade"] ."<br>";
    }
    //Output: Name: Alice, Age: 20, Grade: A
    //        Name: Bob, Age: 22, Grade: B
    //        Name: Charlie, Age: 21, Grade: A


//------------------------------------------------------------------------------

    //multidimensional associative array
    $students = array(
        "student1" => array("name" => "Alice", "age" => 20, "grade" => "A"),
        "student2" => array("name" => "Bob", "age" => 22, "grade" => "B"),
        "student3" => array("name" => "Charlie", "age" => 21, "grade" => "A")
    );
    foreach ($students as $studentKey => $student) {
        echo "Student Key: " . $studentKey . ", Name: " . $student["name"] . ", Age: " . $student["age"] . ", Grade: " . $student["grade"] ."<br>";
    }
    //Output: Student Key: student1, Name: Alice, Age: 20, Grade: A
    //        Student Key: student2, Name: Bob, Age: 22, Grade: B
    //        Student Key: student3, Name: Charlie, Age: 21, Grade: A


//------------------------------------------------------------------------------


//List funtion in PHP
//The list() function is used to assign a list of variables in one operation. It is often used with the array() function to assign values from an array to a list of variables.
//Example of list() function in PHP
$fruits = array("Apple", "Banana", "Orange");
list($fruit1, $fruit2, $fruit3) = $fruits;
echo $fruit1 ."<br>"; // Output: Apple
echo $fruit2 ."<br>"; // Output: Banana
echo $fruit3 ."<br>"; // Output: Orange

//Example of list() function with associative array in PHP
$person = array("name" => "John", "age" => 30, "city" => "New York");
list("name" => $name, "age" => $age, "city" => $city) = $person;
echo $name ."<br>"; // Output: John
echo $age ."<br>"; // Output: 30
echo $city ."<br>"; // Output: New York

//Example of list() function with multidimensional array in PHP
$students = array(
    array("name" => "Alice", "age" => 20, "grade" => "A"),
    array("name" => "Bob", "age" => 22, "grade" => "B"),
    array("name" => "Charlie", "age" => 21, "grade" => "A")
);
foreach ($students as $student) {
    list("name" => $name, "age" => $age, "grade" => $grade) = $student;
    echo "Name: " . $name . ", Age: " . $age . ", Grade: " . $grade ."<br>";
}
//Output: Name: Alice, Age: 20, Grade: A
//        Name: Bob, Age: 22, Grade: B
//        Name: Charlie, Age: 21, Grade: A

//------------------------------------------------------------------------------




?>