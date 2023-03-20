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
$data = $post->readPosts();
// if ther is post in database

if($data->rowCount()){
    $posts = [];
    // re-arrange the post data

    while($row = $data->fetch(PDO::FETCH_OBJ)){
       $posts[$row->id] = [
            'id' => $row->id,
            'category' => $row->category,
            'title' => $row->title,
            'description' => $row->description,
            'created_at' => $row->created_at, 
       ];
    }
    echo json_encode($posts);
}else{
    echo json_encode(['message'=>'no posts found']);
}

