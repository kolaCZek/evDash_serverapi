<?php
  header('Content-Type: application/json');
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  $result = new stdClass();

  if($_SERVER["CONTENT_TYPE"] != 'application/json') {
    $result->ret = 'err';
    $result->description = 'Unsupported content-type';
    http_response_code(400);
    die(json_encode($result));
  }

  require('api.class.php');
  $api = new Api();

  if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['register'])) {
    if($apikey = $api->register()){
      $result->ret = 'ok';
      $result->apikey = $apikey;
    } else {
      $result->ret = 'err';
      http_response_code(500);
    }
    die(json_encode($result));

  } else {
    $json = file_get_contents('php://input');

    if(!$jsondta = json_decode($json)) {
      $result->ret = 'err';
      $result->description = 'Can not parse json';
      http_response_code(400);
      die(json_encode($result));
    }

    if(!isset($jsondta->apikey)){
      $result->ret = 'err';
      $result->description = 'apikey is missing';
      http_response_code(401);
      die(json_encode($result));
    }

    if(!$api->getApiKeyUser($jsondta->apikey)) {
      $result->ret = 'err';
      $result->description = 'apikey does not exist';
      http_response_code(401);
      die(json_encode($result));
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      if(!$api->pushVals($jsondta)) {
        $result->ret = 'err';
      	$result->description = 'error saving values';
      	http_response_code(500);
        die(json_encode($result));
      }
      if(!$api->pushToAbrp($jsondta)) {
        $result->ret = 'err';
      	$result->description = 'error ABRP sending';
      	http_response_code(500);
        die(json_encode($result));
      }
      $result->ret = 'ok';
      die(json_encode($result));

    } elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
      if($values = $api->getVals($jsondta)) {
        $result->values = $values;
      } else {
        $result->ret = 'err';
	$result->description = 'error reading values';
	http_response_code(500);
      }
      die(json_encode($result));
    }
  }
?>
