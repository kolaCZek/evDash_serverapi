<?php
class Api {
  private $mysqli;

  public function __construct() {
    require('config.php');

    $this->mysqli = new mysqli($dbhost, $dbname, $dbpass, $dbname);
    
    if($this->mysqli->connect_error) {
      throw new Exception('Error connecting to MySQL: '.$this->mysqli->connect_error);
    }
  }

  public function __destruct() {
    $this->mysqli->close();
  }

  public function register() {
    $apikey = substr(uniqid(), 0, 12);

    $query = $this->mysqli->prepare("INSERT INTO `users` (`apikey`, `IP`) VALUES (?, ?)");
    $query->bind_param('ss', $apikey, $_SERVER['REMOTE_ADDR']);
    
    if($query->execute()) {
      return $apikey;
    } else {
      return false;
    }
  }

  public function getApiKeyUser($apikey) {
    $query = $this->mysqli->prepare("SELECT `iduser` FROM `users` WHERE `apikey` =  ?");
    $query->bind_param('s', $apikey);

    if($query->execute()) {
      $query->bind_result($iduser);
      $query->fetch();

      if($iduser) {
        return $iduser;
      }
    }

    return false;
  }

  public function pushVals($json) {
    if(!$iduser = $this->getApiKeyUser($json->akey)) {
      return false; 
    }

    $query = $this->mysqli->prepare("INSERT INTO `data` (`user`, `IP`, `soc`, `soh`, `batK`, `batA`, `batV`, `auxV`, `MinC`, `MaxC`, `InlC`, `fan`, `cumCh`, `cumD`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('isdddddddddddd', $iduser, $_SERVER['REMOTE_ADDR'], $json->soc, $json->soh, $json->batK, $json->batA, $json->batV, $json->auxV, $json->MinC, $json->MaxC, $json->InlC, $json->fan, $json->cumCh, $json->cumD);

    if($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function getVals($json) {
    if(!$iduser = $this->getApiKeyUser($json->akey)) {
      return false;
    }

    $query = $this->mysqli->prepare("SELECT `timestamp`, `soc`, `soh`, `batK`, `batA`, `batV`, `auxV`, `MinC`, `MaxC`, `InlC`, `fan`, `cumCh`, `cumD` FROM `data` WHERE `user` =  ?");
    $query->bind_param('i', $iduser);

    if($query->execute()) {
      $obj = $query->get_result()->fetch_object();
      return $obj;
    }
  }

}
?>
