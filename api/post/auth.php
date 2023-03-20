<?php 

error_reporting(E_ALL);
ini_set('display_error',1);

//Headers

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST');

//Including reuired files
include_once('../../config/DataBase.php');
include_once('../../models/Post.php');

//Connnecting database
$database = new DataBase;
$db = $database->connect();

$token = new Post($db);

if(!$token->auth()){
    http_response_code(404);
    echo json_encode([
        'message'=>'Could not generate token, contact Admin'
    ]);
}else{
    http_response_code(200);
    echo json_encode($token->auth());
}