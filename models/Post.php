<?php 

error_reporting(E_ALL);
ini_set('display_error',1);

/**
 * @OA\Info(title="PDP PDO REST API", version="1.0")
 */
class Post{

    //Post Properties

    public $id;
    public $title;
    public $category_id;
    public $description;
    public $created_at;
    private $connection;
    private $table = 'posts';
    public function __construct($db){
        $this->connection = $db;
    }


    /**
     * @OA\Get(
     *   path="/php/php-pdo/api/post/posts.php",
     *   summary="method to read all the saved posts from database",
     *   tags={"Posts"},
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     * )
     */
    public function readPosts()
    {
       //query for reading posts from table.
       $query='SELECT category.name as category, posts.id,posts.title,
       posts.created_at,posts.description,posts.category_id
       FROM '.$this->table.' posts LEFT JOIN category ON posts.category_id = category.id
       ORDER BY posts.id ASC'; 
       $post = $this->connection->prepare($query);
       $post->execute();
       return $post;
    }

    
    /**
     * @OA\Get(
     *   path="/php/php-pdo/api/post/single.php",
     *   summary="method for reading single post",
     *   tags={"Posts"},
     *   @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
     *      description="id",
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     * )
     */
    public function readSinglePosts($id)
    {
        $this->id = $id;
       //query for reading posts from table.
       $query='SELECT category.name as category, posts.id,posts.title,
       posts.created_at,posts.description,posts.category_id
       FROM '.$this->table.' posts LEFT JOIN category ON posts.category_id = category.id
       WHERE posts.id=? LIMIT 0,1'; 
       $post = $this->connection->prepare($query);
    //    $post->bindValue('id', $this->id,PDO::PARAM_INT);
    //    $post->execute();
    //    $post->execute([$this->id]);
        $post->bindValue(1, $this->id,PDO::PARAM_INT);
        $post->execute();

       return $post;
    }

    /**
     * @OA\Post(
     *   path="/php/php-pdo/api/post/insert.php",
     *   summary="method for create post",
     *   tags={"Posts"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          
     *      )
     *   )
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     * )
     */
    public function createPosts($params)
    {
       try{
            // Assigning Values
            $this->title = $params['title'];
            $this->category_id = $params['category_id'];
            $this->description = $params['description'];
            //Query to store new  post in database
            $query = 'INSERT INTO '.$this->table.'
            SET 
                title = :title,
                category_id = :category_id,
                description = :description';
                $post = $this->connection->prepare($query);
                $post->bindValue('title',$this->title );
                $post->bindValue('category_id',$this->category_id );
                $post->bindValue('description',$this->description );
                
                if($post->execute()){
                    return true;
                }
                return false;
       }
       catch(PDOException $e){
            echo $e->getMessage();
       }
    }

    // method for update post

    public function updatePost($params)
    {
        try{
            // Assigning Values
            $this->id = $params['id'];
            $this->title = $params['title'];
            $this->category_id = $params['category_id'];
            $this->description = $params['description'];

            //Query to update post in database
            $query = 'UPDATE '.$this->table.' 
            SET 
                title = :title,
                category_id = :category_id,
                description = :description
                WHERE id = :id';
                $post = $this->connection->prepare($query);

                $post->bindValue('id',$this->id );
                $post->bindValue('title',$this->title );
                $post->bindValue('category_id',$this->category_id );
                $post->bindValue('description',$this->description );
                
                if($post->execute()){
                    return true;
                }
                return false;
            
       }
       catch(PDOException $e){
            echo $e->getMessage();
       }
    }

    // method to delete post from database

    public function deletePost($id)
    {
        try{
            // Assigning Values
            $this->id = $id;
            //Query to update post in database
            $query = 'DELETE FROM '.$this->table.' 
                WHERE id = :id';
                $post = $this->connection->prepare($query);

                $post->bindValue('id',$this->id );
                
                if($post->execute()){
                    return true;
                }
                return false;
            
       }
       catch(PDOException $e){
            echo $e->getMessage();
       }
    }

}