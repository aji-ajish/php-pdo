<?php 

error_reporting(E_ALL);
ini_set('display_error',1);

//Headers

header('Access-Control-Allow-Origin: *');
header('Content-Typr: application/json');
header('Access-Control-Allow-Method: POST');

//Including reuired files
include_once('../../config/DataBase.php');
include_once('../../models/Post.php');

//Connnecting database
$database = new DataBase;
$db = $database->connect();

$post = new Post($db);
$data = json_decode(file_get_contents("php://input"));

if(isset($data)){
    $params = [
        'id' => $data->id,
        'title' => $data->title,
        'category_id' => $data->category_id,
        'description' => $data->description,
    ];

    if($post->updatePost($params)){
        echo json_encode(['message'=>'Post Update Successfully']);
    }else{
        echo json_encode(['message'=>'Post update Error']);
    }
}