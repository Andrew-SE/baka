<?php

/**
 * Kořenová classa pro všechny Controllery
 */
abstract class Controller
{
  protected $data = array();
  protected $view = "";
  protected $header = array('title' => '', 'key_words' => '', 'description' => '');


    /**
     * Funkce která se spustí po načtení kontroleru
     * @param $parameters
     * @return mixed
     */
    abstract function process($parameters);

    /**
     * načtení určitého pohledu/šablony
     * @return void
     */
  public function loadView(){
    if ($this->view) {
      extract($this->xssPrevention($this->data));
      extract($this->data, EXTR_PREFIX_ALL, "_");
      require("views/". $this->view . ".php");
    }
  }

    /**
     * přesměrování na jinou adresu/ jiný kontroler
     * @param $url
     * @return void
     */
  public function redirect($url)
  {

    header("Location: ". REDIRECT_PATH . $url);
    header("Connection: close");
    exit;
  }

    /**
     * Zkontrolování všech proměných které se vypisují
     * @param $x
     * @return array|mixed|string|null
     */
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
