<?php

class Validate{
  private $_db = null,
          $_errors = array(),
          $_passed = false;
  public function __construct(){
    $this->_db = DB::getInstance();
  }
  public function check($source,$items = array()){
    foreach ($items as $item => $rules) {
      foreach ($rules as $rule => $rule_value) {
        $value = $source[$item];
        if ($rule === 'required' && empty($value)) {
          $this->addError("{$item} is required");
        }else if (!empty($value)) {
          switch ($rule) {
            case 'min':
              if (strlen($value) < $rule_value) {
                $this->addError("{$item} must be a minmum of {$rule_value} characters");
              }
              break;
            case 'max':
              if (strlen($value) > $rule_value) {
                 $this->addError("{$item} must be a maximum of {$rule_value} characters");
              }
              break;
            case 'matches':
                if ($value != $source[$rule_value]) {
                  $this->addError("{$rule_value} must matche {$item}");
                }
                break;
            case 'unique':
                $check = $this->_db->get($rule_value,array($item,'=',$value));
                if ($check->count()) {
                  $this->addError("{$item} already exists");
                }
                break;

          }
        }
      }

    }
    if (empty($this->error())) {
      return $this->_passed = true;
    }
    return $this;
  }
  private function addError($error){
    return $this->_errors[] = $error;
  }
  public function passed(){
    return $this->_passed;
  }

  public function error(){
    return $this->_errors;
  }



  }
