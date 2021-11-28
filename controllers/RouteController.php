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

    if (empty($parsedUrl[0]))
        $this->redirect('baka');
    $controllerClass = $this->textEditFormat(array_shift($parsedUrl))."Controller";
    if(file_exists('controllers/'. $controllerClass .'.php'))
        $this->controller = new $controllerClass;
    else
        $this->redirect('error');

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
