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

$post = new Post($db);
$data = json_decode(file_get_contents("php://input"));

if(count(($_POST))){
    //creating new post from user input

    $params = [
        'title' => $_POST['title'],
        'category_id' => $_POST['category_id'],
        'description' => $_POST['description'],
    ];
    $newPost = $post->createPosts($params);
    if($newPost){
        echo json_encode(['message'=>'Post Added Successfully']);
    }else{
        echo json_encode(['message'=>'Post Adding Error']);
    }
}else if(isset($data))
{
    $params = [
        'title' => $data->title,
        'category_id' => $data->category_id,
        'description' => $data->description,
    ];

    if($post->createPosts($params)){
        echo json_encode(['message'=>'Post Added Successfully']);
    }else{
        echo json_encode(['message'=>'Post Adding Error']);
    }
}
