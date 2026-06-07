<?php
//explain the rest API in PHP
//A REST API (Representational State Transfer Application Programming Interface) is a set of rules and
//conventions for building and interacting with web services. It is based on the principles of REST,
//which is an architectural style for designing networked applications. In a REST API, resources are 
//identified by URLs, and clients can perform operations on these resources using standard HTTP methods such
//as GET, POST, PUT, DELETE, etc. The API typically returns data in a format such as JSON or XML.
//REST APIs are stateless, meaning that each request from a client to the server must contain all the
//information needed to understand and process the request. This makes REST APIs scalable and easy to maintain.
//In PHP, you can create a REST API using frameworks like Laravel, Symfony, or by using plain PHP with
//routing and handling logic to manage the requests and responses.

//Example of a simple REST API in PHP
header("Content-Type: application/json"); // Set the content type to JSON
$requestMethod = $_SERVER["REQUEST_METHOD"]; // Get the HTTP method of the request
switch ($requestMethod) {
    case "GET":
        // Handle GET request to retrieve data
        $data = array("message" => "This is a GET request");
        echo json_encode($data);
        break;
    case "POST":
        // Handle POST request to create data
        $data = array("message" => "This is a POST request");
        echo json_encode($data);
        break;
    case "PUT":
        // Handle PUT request to update data
        $data = array("message" => "This is a PUT request");
        echo json_encode($data);
        break;
    case "DELETE":
        // Handle DELETE request to delete data
        $data = array("message" => "This is a DELETE request");
        echo json_encode($data);
        break;
    default:
        // Handle unsupported HTTP methods
        http_response_code(405); // Method Not Allowed
        $data = array("message" => "Method not allowed");
        echo json_encode($data);
        break;
}

//In this example, we set the content type of the response to JSON and then use a switch statement to handle different HTTP methods. Depending on the method of the request, we return a JSON-encoded message indicating which type of request was made. If an unsupported HTTP method is used, we return a 405 Method Not Allowed response with an appropriate message. This is a basic structure for a REST API in PHP, and you can expand it further by adding routing, authentication, database interactions, and more complex logic as needed for your application.

//Note: When building a REST API in PHP, it's important to follow best practices for API design, such as using meaningful resource names in your URLs, providing clear and consistent responses, and implementing proper error handling. Additionally, consider using tools like Postman or cURL to test your API endpoints and ensure that they are functioning correctly. You may also want to implement authentication and authorization mechanisms to secure your API and control access to your resources. Finally, consider documenting your API using tools like Swagger or API Blueprint to make it easier for other developers to understand how to use your API effectively.

//Example of a more complex REST API in PHP with routing and error handling
header("Content-Type: application/json");
$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = $_SERVER["REQUEST_URI"];
$routes = array(
    "/api/data" => array("GET" => "getData", "POST" => "createData"),
    "/api/data/{id}" => array("GET" => "getDataById", "PUT" => "updateData", "DELETE" => "deleteData")
);
function getData() {
    $data = array("message" => "This is a GET request for all data");
    echo json_encode($data);
}
function createData() {
    $data = array("message" => "This is a POST request to create data");
    echo json_encode($data);
}
function getDataById($id) {
    $data = array("message" => "This is a GET request for data with ID: $id");
    echo json_encode($data);
}
function updateData($id) {
    $data = array("message" => "This is a PUT request to update data with ID: $id");
    echo json_encode($data);
}
function deleteData($id) {
    $data = array("message" => "This is a DELETE request to delete data with ID: $id");
    echo json_encode($data);
}
$matchedRoute = false;
foreach ($routes as $route => $methods) {
    $pattern = preg_replace("/\{[a-zA-Z]+\}/", "([a-zA-Z0-9]+)", $route);
    if (preg_match("/^$pattern$/", $requestUri, $matches)) {
        $matchedRoute = true;
        $method = $methods[$requestMethod] ?? null;
        if ($method) {
            array_shift($matches); // Remove the full match
            call_user_func_array($method, $matches); // Call the appropriate function with parameters
        } else {
            http_response_code(405); // Method Not Allowed
            $data = array("message" => "Method not allowed for this endpoint");
            echo json_encode($data);
        }
        break;
    }
}
if (!$matchedRoute) {
    http_response_code(404); // Not Found
    $data = array("message" => "Endpoint not found");
    echo json_encode($data);
}
//In this example, we define a set of routes for our REST API and use regular expressions to match the incoming request URI to the appropriate route. We also implement error handling for unsupported HTTP methods and unmatched routes, returning appropriate HTTP status codes and messages in the response. This structure allows us to create a more robust and flexible REST API in PHP that can handle various endpoints and operations while providing clear feedback to clients when something goes wrong.

//example of a REST API with database interaction in PHP
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
try {    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password); // Create a PDO connection to the database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception
    echo "Connected successfully" ."<br>";
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT id, name, age FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
            break;
        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("INSERT INTO users (name, age) VALUES (:name, :age)");
            $stmt->bindParam(':name', $input['name']);
            $stmt->bindParam(':age', $input['age']);
            $stmt->execute();
            $data = array("message" => "User created successfully"); echo json_encode($data);
            break;
        case 'PUT':
            $input = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("UPDATE users SET name = :name, age = :age WHERE id = :id");
            $stmt->bindParam(':name', $input['name']);
            $stmt->bindParam(':age', $input['age']);
            $stmt->bindParam(':id', $input['id']);
            $stmt->execute();
            $data = array("message" => "User updated successfully"); echo json_encode($data);
            break;
        case 'DELETE':
            $input = json_decode(file_get_contents("php://input"), true);
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $input['id']);
            $stmt->execute();
            $data = array("message" => "User deleted successfully"); echo json_encode($data);
            break;
        default:
            $data = array("message" => "Invalid request method");
            echo json_encode($data);
            break;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
}
catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

//------------------------------------------------------------------------------

//Example of using cURL to consume a REST API in PHP
$apiUrl = "https://api.example.com/data";
$ch = curl_init(); // Initialize a cURL session
curl_setopt($ch, CURLOPT_URL, $apiUrl); // Set the URL to fetch
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
$response = curl_exec($ch); // Execute the cURL session
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
} else {
    $data = json_decode($response, true); // Decode the JSON response
    print_r($data); // Output: Data from the API
}
curl_close($ch); // Close the cURL session

//----------------------------------------------------------------------------  



?>