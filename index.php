<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'lvj5');
define('USERNAME', 'lvj5');
define('PASSWORD', 'mNoRgZ79');
define('CONNECTION', 'sql2.njit.edu');

class dbConn{
    //variable to hold connection object.
    protected static $db;
    //private construct - class cannot be instatiated externally.
    private function __construct() {
        try {
            // assign PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            echo "Connection Successful</br>";
        }
        catch (PDOException $e) {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
        }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) {
            //new connection object.
            new dbConn();
        }
        //return connection.
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
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet[0];
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

    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        $tableName = get_called_class();
        $array = get_object_vars($this);
        $columnString = implode(',', $array);
        $valueString = ":".implode(',:', $array);
       // echo "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";
        echo 'I just saved record: ' . $this->id;
    }
    private function insert() {
        $sql = "INSERT INTO $tableName (" . $columnString . ") VALUES (" . $valueString . ")</br>";
        echo $sql;
        return $sql;
    }
    private function update() {
        $sql = "anytihng";
        return $sql;
        echo 'I just updated record' . $this->id;
    }
    public function delete() {
        $db = dbConn::getConnection();
        $sql = 'DELETE FROM '.$this->tableName.' WHERE id='.$this->id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        echo 'I just deleted record: ' . $this->id .'<br>';
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
    public static function table()
    {
        $table->tableName = 'accounts';
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

    foreach ($result as $row) {
        echo "<tr>";
    foreach ($row as $column) {
        echo "<td>$column</td>";
   }
    echo "</tr>";
  }    
    echo "</table>";
 } 
}



$records = accounts::findAll();
print_r($records);
displaytable::showtable($records);
/*$records = todos::findAll();
$record = todos::findOne(1);
$record = new todo();
$record->message = 'some task';
$record->isdone = 0;
print_r($record);
$record = todos::create();
print_r($record);*/

?>