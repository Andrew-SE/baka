<?php

abstract class Controller
{
  protected $data = array();
  protected $view = "";
  protected $header = array('title' => '', 'key_words' => '', 'description' => '');


  abstract function process($parameters);

  public function loadView(){
    if ($this->view) {
      extract($this->xssPrevention($this->data));
      extract($this->data, EXTR_PREFIX_ALL, "_");
      require("views/". $this->view . ".php");
    }
  }
  public function redirect($url)
  {
    header("Location: /$url");
    header("Connection: close");
    exit;
  }

  public function xssPrevention($x = null){
      if (!isset($x))
          return null;
      else if (is_string($x))
          return htmlentities($x, ENT_QUOTES);
      else if (is_array($x)){
          foreach ($x as $k =>$v){
              $x[$k] = $this->xssPrevention($v);
          }
          return $x;
      }
      else return $x;
  }
}
