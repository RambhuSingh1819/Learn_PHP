<?php

    //Constants in PHP
    //A constant is an identifier (name) for a simple value. The value cannot be changed during the script. By convention, constant identifiers are always uppercase. A valid constant name starts with a letter or underscore, followed by any number of letters, numbers, or underscores.
    //Defining a constant in PHP using define() function
    define("PI", 3.14);
    echo PI ."<br>"; // Output: 3.14

    //Defining a constant in PHP using const keyword
    const GRAVITY = 9.8;
    echo GRAVITY ."<br>"; // Output: 9.8

    //Constants are global and can be accessed from anywhere in the script
    function calculateCircumference($radius) {
        return 2 * PI * $radius;
    }
    echo calculateCircumference(5) ."<br>"; // Output: 31.400000000000002
    //Constants cannot be changed once defined
    //PI = 3.14159; // This will cause an error because constants cannot
    //be redefined or changed
    //Constants can also be defined using the define() function with an optional third parameter to specify case sensitivity
    define("SPEED_OF_LIGHT", 299792458, true); // Case-insensitive
    echo SPEED_OF_LIGHT ."<br>"; // Output: 299792458
    echo SPEED_OF_LIGHT ."<br>"; // Output: 299792458 (case-insensitive)
    //Constants can also be defined using the const keyword inside a class
    class Physics {
        const PLANCK_CONSTANT = 6.62607015e-34;
    }
    echo Physics::PLANCK_CONSTANT ."<br>"; // Output: 6.62607015E-34
    