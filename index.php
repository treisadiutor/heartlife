<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';
require_once 'config/constants.php';
require_once 'core/Router.php';
$router = new Router;

require_once 'routes/web.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__); 
$dotenv->load();

$router->handleRequest();