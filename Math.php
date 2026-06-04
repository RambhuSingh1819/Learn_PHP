<?php
//Example of math functions in PHP
echo abs(-5) ."<br>"; // Output: 5
echo ceil(4.3) ."<br>"; // Output: 5
echo floor(4.7) ."<br>"; // Output: 4
echo round(4.5) ."<br>"; // Output: 5
echo round(4.4) ."<br>"; // Output: 4
echo max(1, 5, 3) ."<br>"; // Output: 5
echo min(1, 5, 3) ."<br>"; // Output: 1
echo pow(2, 3) ."<br>"; // Output: 8
echo sqrt(16) ."<br>"; // Output: 4

echo rand(1, 100) ."<br>"; // Output: Random number between 1 and 100
echo random_int(1, 100) ."<br>"; // Output: Random integer between 1 and 100 using a cryptographically secure algorithm
echo mt_rand(1, 100) ."<br>"; // Output: Random number between 1 and 100 using Mersenne Twister algorithm

echo pi() ."<br>"; // Output: 3.1415926535898
echo exp(1) ."<br>"; // Output: 2.718281828459
echo log(10) ."<br>"; // Output: 2.302585092994
echo log(10, 10) ."<br>"; // Output: 1
echo log10(100) ."<br>"; // Output: 2
echo log1p(0.5) ."<br>"; // Output: 0.405465108108
echo expm1(0.5) ."<br>"; // Output: 0.6487212707
echo cos(pi() / 3) ."<br>"; // Output: 0.5
echo sin(pi() / 3) ."<br>"; // Output: 0.8660254038
echo tan(pi() / 4) ."<br>"; // Output: 1
echo atan(1) ."<br>"; // Output: 0.785398163397
echo atan2(1, 1) ."<br>"; // Output: 0.785398163397
echo cosh(1) ."<br>"; // Output: 1.54308063482
echo sinh(1) ."<br>"; // Output: 1.17520119364
echo tanh(1) ."<br>"; // Output: 0.761594155956
echo acosh(2) ."<br>"; // Output: 1.31695789692
echo asinh(1) ."<br>"; // Output: 0.881373587019
echo atanh(0.5) ."<br>"; // Output: 0.549306144334

?>