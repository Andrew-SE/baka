<?php

/**
 *
 */
class RouteController extends Controller
{
  protected $controller;

  public function process($parameters)
  {

    $parsedUrl = $this->parseUrl($parameters[0]);

    /*
    var_dump( $parsedUrl);
      echo "<br>";
    echo array_shift($parsedUrl);
    echo "<br>";
    */

    if (empty($parsedUrl[0]))
        $this->redirect('baka');
        //echo "empty - baka";
        //echo "<br>";
    $controllerClass = $this->textEditFormat(array_shift($parsedUrl))."Controller";
    if(file_exists('controllers/'. $controllerClass .'.php'))
        $this->controller = new $controllerClass;
        //echo "if - " . $this->textEditFormat(array_shift($parsedUrl))."Controller";
    else
        $this->redirect('error');
        //echo "else - error";

    $this->controller->process($parsedUrl);
    $this->data['title']='Bakateam';
    $this->data['description']='bakateam';

    $this->view='default';
  }

  public function parseUrl($url)
  {
    $parsedURL = parse_url($url);
    $parsedURL['path']=ltrim($parsedURL['path'],"/");
    $parsedURL['path']=ltrim($parsedURL['path']);
    $separatedUrl = explode("/",$parsedURL['path']);
      array_shift($separatedUrl);
    return $separatedUrl;
  }

  //Edit path text format from "main-page" to "MainPage" -> MainPageController
  public function textEditFormat($text){
      $text = str_replace('-',' ', $text);
      $text = ucwords($text);
      $text = str_replace(' ','', $text);

      return $text;
  }
}
