<?php

//Example of date and time functions in PHP
echo date("Y-m-d") ."<br>"; // Output: Current date in YYYY-MM-DD format
echo date("H:i:s") ."<br>"; // Output: Current time in HH:MM:SS format
echo date("l") ."<br>"; // Output: Current day of the week
echo date("F") ."<br>"; // Output: Current month
echo date("Y") ."<br>"; // Output: Current year
echo date("D, d M Y H:i:s") ."<br>"; // Output: Current date and time in a specific format
echo time() ."<br>"; // Output: Current Unix timestamp
echo strtotime("next Monday") ."<br>"; // Output: Unix timestamp for the next Monday
echo date("Y-m-d H:i:s", strtotime("next Monday")) ."<br>"; // Output: Date and time for the next Monday
echo date("Y-m-d H:i:s", strtotime("+1 week")) ."<br>"; // Output: Date and time for one week from now
echo date("Y-m-d H:i:s", strtotime("-1 month")) ."<br>"; // Output: Date and time for one month ago
echo date("Y-m-d H:i:s", strtotime("last year")) ."<br>"; // Output: Date and time for last year
echo date("Y-m-d H:i:s", strtotime("first day of this month")) ."<br>"; // Output: Date and time for the first day of the current month
echo date("Y-m-d H:i:s", strtotime("last day of this month")) ."<br>"; // Output: Date and time for the last day of the current month
echo date("Y-m-d H:i:s", strtotime("first day of next month")) ."<br>"; // Output: Date and time for the first day of the next month
echo date("Y-m-d H:i:s", strtotime("last day of next month")) ."<br>"; // Output: Date and time for the last day of the next month
echo date("Y-m-d H:i:s", strtotime("first day of last month")) ."<br>"; // Output: Date and time for the first day of the last month
echo date("Y-m-d H:i:s", strtotime("last day of last month")) ."<br>"; // Output: Date and time for the last day of the last month
echo date("Y-m-d H:i:s", strtotime("first day of this year")) ."<br>"; // Output: Date and time for the first day of the current year
echo date("Y-m-d H:i:s", strtotime("last day of this year")) ."<br>"; // Output: Date and time for the last day of the current year
echo date("Y-m-d H:i:s", strtotime("first day of next year")) ."<br>"; // Output: Date and time for the first day of the next year
echo date("Y-m-d H:i:s", strtotime("last day of next year")) ."<br>"; // Output: Date and time for the last day of the next year
echo date("Y-m-d H:i:s", strtotime("first day of last year")) ."<br>"; // Output: Date and time for the first day of the last year
echo date("Y-m-d H:i:s", strtotime("last day of last year")) ."<br>"; // Output: Date and time for the last day of the last year



?>