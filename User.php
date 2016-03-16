<?php


class User{
  private $_db,
          $_data,
          $sessionName,
          $isLoggedIn;
  public function __construct($user = null){
     $this->_db = DB::getInstance();
     $this->sessionName = Config::get("session/session_name");
     if (!$user) {
       if (Session::exists($this->sessionName)) {
         $user = Session::get($this->sessionName);
         if ($this->find($user)) {
           $this->isLoggedIn = true;
         }else {
           // log out
         }
       }
     }else {
       $this->find($user);
     }
  }
  public function create($fields = array()){
    if (!$this->_db->insert("users",$fields)) {
      throw new Exception("there was a problem creating an account");

    }
  }
  public function find($user = null){
    if($user){
      $field = (is_numeric($user)) ? 'id' : 'username';
      $data = $this->_db->get("users",array($field,"=",$user));
      if ($data->count()) {
        $this->_data = $data->first();
        return true;
      }
    }
    return false;
  }
  public function login($username = null,$password = null){
      $user = $this->find($username);
      if ($this->data()->password === Hash::make($password,$this->data()->salt)) {
        Session::put($this->sessionName,$this->data()->id);
        return true;
      }
      return false;
  }
  public function data(){
    return $this->_data;
  }
  public function isLoggedIn(){
    return $this->isLoggedIn;
  }
}
