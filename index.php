<?php
//mb_internal_encoding("UTF-8");

/**
 * Autoload class kontorlerů a modelů podle názvu
 * @param $class
 * @return void
 */
function autoload($class)
{
  if(preg_match('/Controller$/', $class))
    require("controllers/" . $class . ".php");
  else
    require("models/". $class . ".php");
}
spl_autoload_register("autoload");

/**
 * Načtení potřebných knihoven a config souborů
 */
require_once("config/config-app.php");
require 'libs/TimetableProcess.php';
require "libs/TimetableEventBatchPostFields.php";
require "libs/ErrorLib.php";

session_start();
$router = new RouteController();
$router->process(array($_SERVER['REQUEST_URI']));
$router->loadView();
