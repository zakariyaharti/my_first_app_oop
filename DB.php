<?php

class DB{
  private static $_instance = null;
  private $_pdo,
          $query,
          $error = false,
          $_result,
          $_count = 0;
  private function __construct(){
    try {
      $this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));
    } catch (PDOException $e) {
      die($e->getmessage());
    }

  }
  public static function getInstance(){
    if(!isset(self::$_instance)){
      self::$_instance = new DB();
    }
    return self::$_instance;
  }
  public function query($sql,$params = array()){
    $this->error = false;
    if($this->query = $this->_pdo->prepare($sql)){
      $x = 1;
      if (count($params)) {
        foreach ($params as $param) {
          $this->query->bindValue($x,$param);
          $x++;
        }
      }
    }
    if($this->query->execute()){
      $this->_result = $this->query->fetchAll(PDO::FETCH_OBJ);
      $this->_count = $this->query->rowCount();
    }else {
      $this->error = true;
    }
    return $this;
  }
  public function error(){
    return $this->error;
  }
  public function action($action,$table,$where = array()){
    if (count($where) === 3) {
      $operators = array("=",">","<","<=",">=");

      $field = $where[0];
      $operator = $where[1];
      $value = $where[2];

      if (in_array($operator,$operators)) {
        $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
        if (!$this->query($sql,array($value))->error()) {
          return $this;
        }
      }
      return false;
    }
  }
  public function get($table,$where){
    return $this->action("SELECT *",$table,$where);
  }
  public function count(){
    return $this->_count;
  }
  public function results(){
    return $this->_result;
  }
  public function insert($table,$fields = array()){
    if (count($fields)) {
      $keys = array_keys($fields);
      $value = '';
      $x = 1;

      foreach ($fields as $field) {
        $value .= '?';
        if ($x < count($fields)) {
          $value .= ' ,';
        }
        $x++;
      }

      $sql = "INSERT INTO users(`". implode('`,`',$keys) ."`) VALUES({$value})";
      if (!$this->query($sql,$fields)->error()) {
        return true;
      }
    }
    return false;
  }
  public function update($table,$id,$fields){
    $set = '';
    $x = 1;
    foreach ($fields as $name => $value) {
      $set .= "{$name} = ?";
      if ($x < count($fields)) {
        $set .= ' , ';
      }
      $x++;
    }

    $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
    print $sql;
    if (!$this->query($sql,$fields)->error()) {
      return true;
    }
      return false;
    }
    public function first(){
      return $this->results()[0];
    }
}
