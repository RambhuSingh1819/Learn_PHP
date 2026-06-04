<?php

//explain the regular expression functions in PHP
//Regular expressions are a powerful tool for pattern matching and manipulation of strings. In PHP, there
//are several functions that allow you to work with regular expressions, including preg_match(),
//preg_match_all(), preg_replace(), preg_split(), and preg_grep().
//The preg_match() function is used to perform a regular expression match on a string. It
//returns true if the pattern matches the string, and false otherwise.

//The preg_match_all() function issimilar to preg_match(), but it returns an array of all
//matches found in the string.
//The preg_replace() function is used to perform a regular expression search and replace on a string
//The preg_split() function is used to split a string into an array based on a regular expression pattern.
//The preg_grep() function is used to filter an array of strings based on a regular expression pattern,
//returning only the elements that match the pattern.


//Regular expressions in PHP are based on the PCRE (Perl Compatible Regular Expressions) library
//and support a wide range of features, including character classes, quantifiers, anchors, and more.
//Regular expressions can be used for tasks such as validating input, extracting information from strings,
//and performing complex search and replace operations. It's important to note that regular expressions
//can be complex and may require some practice to master, but they are a powerful tool for working withstrings in PHP.

//Example of regular expression functions in PHP
$string = "The quick brown fox jumps over the lazy dog.";
$pattern = "/\b\w{5}\b/"; // Match words with exactly 5 characters
preg_match_all($pattern, $string, $matches);
print_r($matches[0]); // Output: Array ( [0] => quick [1] => brown [2] => jumps )

//Replace all occurrences of "fox" with "cat"
$replacedString = preg_replace("/fox/", "cat", $string);
echo $replacedString ."<br>"; // Output: The quick brown cat jumps over the lazy dog.

//Split the string into an array of words
$words = preg_split("/\s+/", $string);
print_r($words); // Output: Array ( [0] => The [1] => quick [2] => brown [3] => fox [4] => jumps [5] => over [6] => the [7] => lazy [8] => dog. )

//Filter an array of strings to include only those that contain the word "the"
$array = array("The quick brown fox", "jumps over the lazy dog", "Hello World");
$filteredArray = preg_grep("/the/i", $array);
print_r($filteredArray); // Output: Array ( [0] => The quick brown fox [1] => jumps over the lazy dog )

//The preg_grep() function is used to filter an array of strings based on a regular expression pattern, returning only the elements that match the pattern. In this example, we use the pattern "/the/i" to match any string that contains the word "the", regardless of case (due to the "i" modifier). The resulting filtered array includes only the strings that contain "the".
//The preg_grep() function is particularly useful for filtering arrays based on complex patterns, allowing you to easily extract relevant data from larger datasets.

//Note: When working with regular expressions in PHP, it's important to properly escape special characters in your patterns to avoid unintended matches or errors. You can use the preg_quote() function to escape special characters in a string that you want to use as a literal pattern in your regular expression. Additionally, be mindful of the performance implications of using complex regular expressions, as they can be computationally expensive and may impact the performance of your application if not used judiciously.
//Example of using preg_quote() function in PHP
$searchString = "Hello, World!";
$escapedString = preg_quote($searchString, "/");
$pattern = "/$escapedString/";
if (preg_match($pattern, $string)) {
    echo "Match found!";
} else {
    echo "No match found.";
}
//In this example, we use the preg_quote() function to escape any special characters in the $searchString variable, which allows us to safely use it as a literal pattern in our regular expression. The second argument to preg_quote() specifies the delimiter that will be used in the regular expression, which in this case is "/". By escaping the special characters, we ensure that the regular expression will match the exact string "Hello, World!" without interpreting any special characters as part of the regular expression syntax.

//Example of using regular expressions for input validation in PHP
$email = "user@example.com";
$emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
if (preg_match($emailPattern, $email)) {
    echo "Valid email address.";
} else {
    echo "Invalid email address.";
}

//In this example, we use a regular expression pattern to validate an email address.
//The pattern checks for a valid format of the email address, including allowed characters before
//the "@" symbol, a valid domain name, and a valid top-level domain. By using preg_match() with this pattern,
//we can determine whether the provided email address is valid or not. Regular expressions are commonly used
//for input validation in PHP to ensure that user input meets specific criteria before processing it further
//in the application.

//Example of using regular expressions for data extraction in PHP
$string = "The price of the item is $19.99.";
$pricePattern = "/\$\d+\.\d{2}/";
if (preg_match($pricePattern, $string, $matches)) {
    echo "Price found: " . $matches[0];
} else {    echo "Price not found.";
}

//In this example, we use a regular expression pattern to extract the price from a string. The pattern looks for a dollar sign followed by one or more digits, a decimal point, and exactly two digits after the decimal point. By using preg_match() with this pattern, we can find and extract the price from the string. The extracted price is stored in the $matches array, and we can access it using $matches[0]. Regular expressions are a powerful tool for data extraction in PHP, allowing you to easily retrieve specific information from larger strings based on defined patterns.
//Note: When using regular expressions for input validation or data extraction, it's important to ensure that your patterns are well-defined and tested to avoid unintended matches or security vulnerabilities. Regular expressions can be complex, and it's crucial to thoroughly test them with various input scenarios to ensure they behave as expected. Additionally, consider the performance implications of using regular expressions, especially with large datasets or complex patterns, as they can be computationally expensive.

//Example of using regular expressions for search and replace in PHP
$string = "The quick brown fox jumps over the lazy dog.";
$pattern = "/\bfox\b/";
$replacement = "cat";
$replacedString = preg_replace($pattern, $replacement, $string);
echo $replacedString ."<br>"; // Output: The quick brown cat jumps over the lazy dog.






?>