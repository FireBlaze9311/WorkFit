<?php

// Login
// Supply via POST: useremail, password

/* Parameters ------------------------------------------- */
$key_expire_s = 180;                                       // Nach wie vielen Sekunden der Code abläuft
/* Parameters end */

// Connect to database
$db_host_name = 'db5010643227.hosting-data.io';
$database = 'dbs9005828';
$db_user_name = 'dbu1417295';
$db_password = 'C5YVJVudH55u';
$pdo = new PDO("mysql:host=$db_host_name;dbname=$database",
                $db_user_name,
                $db_password);

require_once 'jwt_utils.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// get posted data
	$data = json_decode(file_get_contents("php://input", true));
	
	//$statement = "SELECT * FROM User WHERE email = '" . mysqli_real_escape_string($dbConn, $data->username) . "' AND password = '" . mysqli_real_escape_string($dbConn, $data->password) . "' LIMIT 1";
	$statement = $pdo->prepare("SELECT * FROM User WHERE email = ?");
    $statement->execute(array($data->useremail));
    $row = $statement->fetch();
	
	if($statement->rowCount() <= 0 || !password_verify($data->password, $row['password'])) {
		echo json_encode(array('error' => 'Invalid user')); // User existerit nicht oder Passwort falsch
	} else {
        //$statement = $pdo->prepare("SELECT Codes.enabled FROM Codes INNER JOIN User ON Codes.userId = User.userId
        //                            WHERE User.email = ? AND Codes.code = ?;");
        $statement = $pdo->prepare("SELECT Codes.enabled FROM Codes INNER JOIN User ON Codes.userId = User.userId
                                    WHERE User.email = ?;"); // Code not needed, can be derived from userId
        $statement->execute(array($data->useremail));
        $row = $statement->fetch();
        if(!isset($row['enabled']) || $row['enabled'] != 1) {
            echo json_encode(array('error' => "Code invalid or disabled"));
        }
        else {
            $headers = array('alg'=>'HS256','typ'=>'JWT');
            $payload = array('email'=>$row['email'], 'exp'=>(time() + $key_expire_s));

            $jwt = generate_jwt($headers, $payload);
            
            echo json_encode(array('token' => $jwt));
        }
	}
}
