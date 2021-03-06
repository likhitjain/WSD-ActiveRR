<?php


ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'lvj5');
define('USERNAME', 'lvj5');
define('PASSWORD', 'mNoRgZ79');
define('CONNECTION', 'sql2.njit.edu');

class dbConn{
    
    protected static $db;
    private function __construct() {
        try {

            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }
    public static function getConnection() {
        if (!self::$db) {
            new dbConn();
        }
        return self::$db;
    }
}
class collection {
    static public function create() {
        $model = new static::$modelName;
        return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();        
        $sql = "SELECT * FROM " . $tableName . " WHERE id =" . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;     
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model {

    protected $tableName;
    static $columnString;
    static $valueString;

    public function save()
    {

        if ($this->id == '') {
            $sql = $this->insert();

        } else {
            $sql = $this->update();
        }
 

        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
      
    }
    private function insert() {
        $sql = "INSERT INTO " .$this->tableName." (" . static::$columnString . ") VALUES (" . static::$valueString . ")";
        return $sql;
    }
    private function update() {
        $sql = " UPDATE " .$this->tableName." SET password='9999' WHERE id=".$this->id;
        return $sql;
    }
    public function delete($id) {
        $db = dbConn::getConnection();
        $sql = 'DELETE FROM '.$this->tableName.' WHERE id='.$id; 
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
}
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    static $columnString='id, email, fname, lname, phone, birthday, gender, password';
    static $valueString= '1, lj@njit.edu, Rob, Holding, 8882349999, NULL, male, 2222';
    public function __construct()
    {
        $this->tableName = 'accounts';
    }
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    static $columnString='id, owneremail, ownerid, createddate, duedate, message, isdone';
    static $valueString= '6, "vj@njit.edu", 2, "2017-05-30", "2017-06-15", "Activerecord", 0';
    public function __construct()
    {
        $this->tableName = 'todos';
    
    }
}

 class displaytable
 {
    static public function showtable($result)
    {
  
  echo '<table border="1"><tr>';
  $i=0;
  foreach ($result as $row) 
  {
    echo "<tr>";
    foreach($row as $key => $value)
    {
      if($i==0)
      {
        echo "<th>".$key."</th>";
      }   
    }
    $i = $i + 1;
    echo "</tr><tr>";
    foreach ($row as $field) 
    {
      echo "<td>".$field."</td>";
    }
    echo "</tr>";
  }
  echo "</table><br><br>";
  }
}


echo '<h1>Select all records from Accounts table</h1>';
$record = accounts::findAll();
displaytable::showtable($record);

echo '<h1>Select all records from Todos table</h1>';
$record = todos::findAll();
displaytable::showtable($record);

echo '<h1>Selecting an id from Accounts Table where id is 2 <h1>';
$record = accounts::findOne(2);
displaytable::showtable($record);

echo '<h1>Selecting an id from Todos Table where id is 7 <h1>';
$record = todos::findOne(7);
displaytable::showtable($record);

echo '<h1>Insert a record in Todos Table<h1>';
$record = todos::create();
$record->delete(6);
$record->save();
$result = todos::findAll();
displaytable::showtable($result);

echo '<h1>Update password in Accounts Table where id is 12 <h1>';
$record = accounts::create();
$record->id=12;
$record->save();
$result = accounts::findAll();
displaytable::showtable($result);

echo '<h1>Delete id 13 from Account Table <h1>';
$record = accounts::create();
$record->delete(13);
$result = accounts::findAll();
displaytable::showtable($result);


?>