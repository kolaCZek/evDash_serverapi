<?php
  header('Content-Type: application/json');

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
    }
    die(json_encode($result));

  } else {
    $json = file_get_contents('php://input');
    
    if(!$jsondta = json_decode($json)) {
      $result->ret = 'err';
      $result->description = 'Can not parse json';
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

      if($api->pushVals($jsondta)) {
        $result->ret = 'ok';
      } else {
        $result->ret = 'err';
	$result->description = 'error saving values';
	http_response_code(500);
      }
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
