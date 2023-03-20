<?php 

error_reporting(E_ALL);
ini_set('display_error',1);

require_once($_SERVER['DOCUMENT_ROOT'].'/php/php-pdo/vendor/autoload.php');
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @OA\Info(title="PDP PDO REST API", version="1.0")
 *      @OA\SecurityScheme(
 *          type="http",
 *          description="Authorisation with JWT generated tokens",
 *          name="Authorization",
 *          in="header",
 *          scheme="bearer",
 *          bearerFormat="JWT",
 *          securityScheme="bearerToken",
 *      ),
 */
class Post{

    //Post Properties

    public $id;
    public $title;
    public $category_id;
    public $description;
    public $created_at;
    protected $key = '0123456789!@#$%^&*()_+=';


    private $connection;
    private $table = 'posts';
    
    public function __construct($db){
        $this->connection = $db;
    }

    /**
     * @OA\Get(
     *   path="/php/php-pdo/api/post/auth.php",
     *   summary="Generates Tokens for Validation",
     *   tags={"Secutiry"},
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     * )
     */

public function auth()
{
  try{
    $issueDate = time();
    $expirationDate = time()*3600; //hour
    $payload = [
        'iss' => 'http://localhost/php/php-pdo',
        'aud' => 'http://localhost',
        'iat' => $issueDate,
        'exp' => $expirationDate,
        'userName' => 'Ajish'
    ];
    $jwtGeneratedToken = JWT::encode($payload, $this->key, 'HS256');
    return [
        'token' => $jwtGeneratedToken,
        'expries' => $expirationDate,
    ];
  }
  catch(PDOException $e){
    echo $e->getMessage();
  }
}

    /**
     * @OA\Get(
     *   path="/php/php-pdo/api/post/posts.php",
     *   summary="method to read all the saved posts from database",
     *   tags={"Posts"},
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     *   security={ {"bearerToken":{}} }
     * )
     */
    public function readPosts()
    {
        try{
            $header = apache_request_headers();
            if(isset($header["Authorization"])){
                $token =str_ireplace('Bearer ', '', $header["Authorization"]);
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                if(isset($decoded->userName) && $decoded->userName == "Ajish"){

                    //query for reading posts from table.
                    $query='SELECT category.name as category, posts.id,posts.title,
                    posts.created_at,posts.description,posts.category_id
                    FROM '.$this->table.' posts LEFT JOIN category ON posts.category_id = category.id
                    ORDER BY posts.id ASC'; 
                    $post = $this->connection->prepare($query);
                    $post->execute();
                    return $post;
                }else{
                    return false;
                }
           
            }else{
                return false;
            }
        }
        catch(PDOException $e){
          echo $e->getMessage();
        }
        
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
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *             @OA\Property(
     *               property="title",
     *               type="string",
     *               description="Title",
     *             ),
     *              @OA\Property(
     *               property="category_id",
     *               type="integer",
     *               description="Category Id",
     *             ),
     *              @OA\Property(
     *               property="description",
     *               type="string",
     *               description="Description",
     *             ),
     *          ),
     *      ),
     *   ),
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

    
    /**
     * @OA\Put(
     *   path="/php/php-pdo/api/post/update.php",
     *   summary="method for update post",
     *   tags={"Posts"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="json",
     *          @OA\Schema(
     *            @OA\Property(
     *               property="id",
     *               type="integer",
     *               description="id",
     *             ),
     *             @OA\Property(
     *               property="title",
     *               type="string",
     *               description="Title",
     *             ),
     *              @OA\Property(
     *               property="category_id",
     *               type="integer",
     *               description="Category Id",
     *             ),
     *              @OA\Property(
     *               property="description",
     *               type="string",
     *               description="Description",
     *             ),
     *          ),
     *      ),
     *   ),
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     * )
     */
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

    
    /**
     * @OA\Delete(
     *   path="/php/php-pdo/api/post/delete.php",
     *   summary="method to delete post from database",
     *   tags={"Posts"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="json",
     *          @OA\Schema(
     *             @OA\Property(
     *               property="id",
     *               type="integer",
     *               description="id",
     *             ),
     *          ),
     *      ),
     *   ),
     *   @OA\Response(response=200,description="found"),
     *   @OA\Response(response=404,description="not found"),
     * )
     */
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