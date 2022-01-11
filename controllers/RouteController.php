<?php

/**
 *  Směrování pomocí url
 *  Url zlab.cz/cesta/paramert/...
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

  //Zpracovani url do pole potrebneho pro smerovani
  public function parseUrl($url)
  {
    $parsedURL = parse_url($url);

    if (isset($parsedURL['path'])){
        $parsedURL['path']=ltrim($parsedURL['path'],"/");
        $parsedURL['path']=ltrim($parsedURL['path']);
        $separatedUrl = explode("/",$parsedURL['path']);
    }
    else{
        $parsedURL['host']=ltrim($parsedURL['host'],"/");
        $parsedURL['host']=ltrim($parsedURL['host']);
        $separatedUrl = explode("/",$parsedURL['host']);
    }

    $app_path = trim(ROUTE_PATH, "/");
    $app_path = ltrim($app_path);

    if(!empty(explode("/", $app_path)[0])){
        // localhost/slozka/slozka/kontroler/ -> {slozka, konrtoler, parametr, ..} -> jenomze to ma vypadat takto {kontroler, parametr, ..}
        // Vymaze z pole slozku ve ktere aplikace je, pri zpracovavani url bude tato slozka vadit a appka nebude spravne smerovat
        foreach (explode("/", $app_path) as $item)
            array_shift($separatedUrl);
    }

    return $separatedUrl;
  }

  //Zmena formatu textu pro nacteni kontroleru "main-page" to "MainPage" -> MainPageController
  public function textEditFormat($text){
      $text = str_replace('-',' ', $text);
      $text = ucwords($text);
      $text = str_replace(' ','', $text);

      return $text;
  }

  public static function errorRed(){

  }

}
