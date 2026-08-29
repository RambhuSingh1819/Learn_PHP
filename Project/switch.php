<?php
//switch statement in PHP
//The switch statement is used to perform different actions based on different conditions.
//Example of switch statement in PHP
$day = "Monday";
$month = "December";

switch ($day) {
    case "Monday":
        echo "Today is Monday";
        break;
    case "Tuesday":
        echo "Today is Tuesday";
        break;
    case "Wednesday":
        echo "Today is Wednesday";
        break;
    default:
        echo "Invalid day";
}
echo "<br>";
switch ($month) {
    case "January":
        echo "This month is January";
        break;
    case "February":
        echo "This month is February";
        break;
    case "March":
        echo "This month is March";
        break;
    default:
        echo "Invalid month";
}
echo "<br>";
//Nested switch statement in PHP
//Example of nested switch statement in PHP
$day = "Monday";
$month = "March";
switch ($day) {
    case "Monday":
        switch ($month) {
            //case "January":  case "April": case "May": case "June": case "July": case "August": case "September": case "October": case "November": case "December":
            case "January" :  case "April" : case "May" : case "June" : case "July" : case "August" : case "September" : case "October" : case "November" : case "December":
                echo "Today is Monday in January";
                break;
            case "February":
                echo "Today is Monday in February";
                break;
            case "March":
                echo "Today is Monday in March";
                break;
            default:
                echo "Invalid month";
        }
        break;
    case "Tuesday":
        switch ($month) {
            case "January" :  case "April" : case "May" : case "June" : case "July" : case "August" : case "September" : case "October" : case "November" : case "December":
                echo "Today is Tuesday in January";
                break;
            case "February":
                echo "Today is Tuesday in February";
                break;
            case "March":
                echo "Today is Tuesday in March";
                break;
            default:
                echo "Invalid month";
        }
        break;
    default:
        echo "Invalid day";
}

?>