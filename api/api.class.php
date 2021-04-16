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

  public function getSettings($iduser) {
		$query = $this->mysqli->prepare("SELECT `timezone`, `notifications`, `abrp_enabled`, `abrp_token` FROM `users` WHERE `iduser` = ?");
		$query->bind_param('i', $iduser);

		if($query->execute()) {
			return $query->get_result()->fetch_object();
		}
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

  public function pushToAbrp($json) {
    if(!$iduser = $this->getApiKeyUser($json->apikey)) {
      return false;
    }

    if(!$settings = $this->getSettings($iduser)) {
      return false;
    }

    if($settings->abrp_enabled == 0) {
      return true;
    }

    if(empty($settings->abrp_token)) {
      return false;
    }

    require('config.php');

    if(!isset($abrp_api_key) || empty($abrp_api_key)) {
      return false;
    }

    $abrp_json = new stdClass();

    switch ($json->carType) {
      case 0:
        $abrp_json->car_model = "kia:niro:19:64:other";
        $abrp_json->capacity = 64;
        break;
      case 1:
        $abrp_json->car_model = "hyundai:kona:19:64:other";
        $abrp_json->capacity = 64;
        break;
      case 2:
        $abrp_json->car_model = "hyundai:ioniq:17:28:other";
        $abrp_json->capacity = 28;
        break;
      case 3:
        $abrp_json->car_model = "kia:niro:19:39:other";
        $abrp_json->capacity = 39;
        break;
      case 4:
        $abrp_json->car_model = "hyundai:kona:19:39:other";
        $abrp_json->capacity = 39;
        break;
      case 5:
        $abrp_json->car_model = "renault:zoe:r240:22:other";
        $abrp_json->capacity = 22;
        break;
      case 7:
        $abrp_json->car_model = "bmw:i3:14:22:other";
        $abrp_json->capacity = 22;
        break;
      case 8:
        $abrp_json->car_model = "kia:soul:19:64:other";
        $abrp_json->capacity = 64;
        break;
      default:
        return false;
    }

    $abrp_json->utc = time();
    $abrp_json->soc = $json->socPerc;
    $abrp_json->power = $json->batPowerKw * -1;
    $abrp_json->speed = $json->speedKmh;
    $abrp_json->is_charging = $json->chargingOn;
    $abrp_json->lat = $json->gpsLat;
    $abrp_json->lon = $json->gpsLon;
    $abrp_json->elevation = $json->gpsAlt;
    $abrp_json->kwh_charged = $json->cumulativeEnergyChargedKWh;
    $abrp_json->soh = $json->sohPerc;
    $abrp_json->batt_temp = ($json->batMinC + $json->batMaxC) / 2;
    $abrp_json->voltage = $json->batVoltage;
    $abrp_json->current = $json->batPowerAmp * -1;
    $abrp_json->odometer = $json->odoKm;

    $abrp_dta_post = [
        'api_key' => $abrp_api_key,
        'token' => $settings->abrp_token,
        'tlm' => json_encode($abrp_json)
      ];

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'http://api.iternio.com/1/tlm/send');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($abrp_dta_post));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);

    if (!curl_errno($curl)) {
      if ($info['http_code'] == 200) {
        return true;
      }
    }

    return false;
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
