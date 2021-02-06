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
    if(!$iduser = $this->getApiKeyUser($json->apikey)) {
      return false; 
    }

    $query = $this->mysqli->prepare("INSERT INTO `data` (`user`, `IP`, `carType`, `ignitionOn`, `chargingOn`, `socPerc`, `socPercBms`, `sohPerc`, `batPowerKw`, `batPowerAmp`, `batVoltage`, `auxVoltage`, `auxAmp`, `batMinC`, `batMaxC`, `batInletC`, `batFanStatus`, `speedKmh`, `odoKm`, `cumulativeEnergyChargedKWh`, `cumulativeEnergyDischargedKWh`, `gpsLat`, `gpsLon`, `gpsAlt`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('isiiiddddddddddddddddddd', $iduser, $_SERVER['REMOTE_ADDR'], $json->carType, $json->ignitionOn, $json->chargingOn, $json->socPerc, $json->socPercBms, $json->sohPerc, $json->batPowerKw, $json->batPowerAmp, $json->batVoltage, $json->auxVoltage, $json->auxAmp, $json->batMinC, $json->batMaxC, $json->batInletC, $json->batFanStatus, $json->speedKmh, $json->odoKm, $json->cumulativeEnergyChargedKWh, $json->cumulativeEnergyDischargedKWh, $json->gpsLat, $json->gpsLon, $json->gpsAlt);

    if($query->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function getVals($json) {
    if(!$iduser = $this->getApiKeyUser($json->apikey)) {
      return false;
    }

    if(isset($json->timestampFrom) && isset($json->timestampTo)) {
      $query = $this->mysqli->prepare("SELECT `timestamp`, `carType`, `ignitionOn, `chargingOn`, `socPerc`, `socPercBms`, `sohPerc`, `batPowerKw`, `batPowerAmp`, `batVoltage`, `auxVoltage`, `auxAmp`, `batMinC`, `batMaxC`, `batInletC`, `batFanStatus`, `speedKmh`, `odoKm`. `cumulativeEnergyChargedKWh`, `cumulativeEnergyDischargedKWh`, `gpsLat`, `gpsLon`, `gpsAlt` FROM `data` WHERE `user` = ? AND `timestamp` >= ? AND `timestamp` <= ? ORDER BY `timestamp`");
      $query->bind_param('iss', $iduser, $json->timestampFrom, $json->timestampTo);
    } else {
      $query = $this->mysqli->prepare("SELECT `timestamp`, `carType`, `ignitionOn`, `chargingOn`, `socPerc`, `socPercBms`, `sohPerc`, `batPowerKw`, `batPowerAmp`, `batVoltage`, `auxVoltage`, `auxAmp`, `batMinC`, `batMaxC`, `batInletC`, `batFanStatus`, `speedKmh`, `odoKm`, `cumulativeEnergyChargedKWh`, `cumulativeEnergyDischargedKWh`, `gpsLat`, `gpsLon`, `gpsAlt` FROM `data` WHERE `user` = ? ORDER BY `timestamp` DESC LIMIT 1");
      $query->bind_param('i', $iduser);
    }

    if($query->execute()) {
      $result = $query->get_result();
      $return = array();
      while ($obj = $result->fetch_object()) {
        $return[] = $obj;
      }
      return $return;
    }
  }

}
?>
