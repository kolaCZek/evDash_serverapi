<?php
class Api {
  private $mysqli;

  public function __construct() {
    require('config.php');

		$this->mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

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
    $query = $this->mysqli->prepare("SELECT `iduser` FROM `users` WHERE `apikey` = ?");
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

  private function getLastOdo($iduser) {
    $query = $this->mysqli->prepare("SELECT `odoKm` FROM `data` WHERE `user` = ? AND `odoKm` > 0 ORDER BY `timestamp` DESC LIMIT 1");
    $query->bind_param('i', $iduser);

    if($query->execute()) {
      $query->bind_result($odoKm);
      $query->fetch();

      if($odoKm) {
        return $odoKm;
      }
    }

    return false;
  }

  public function pushVals($json) {
    if(!$iduser = $this->getApiKeyUser($json->apikey)) {
      return false;
    }

    if($json->odoKm <= 0) {
      if($lastOdoKm = $this->getLastOdo($iduser)) {
        $json->odoKm = $lastOdoKm;
      }
    }

    $query = $this->mysqli->prepare("INSERT INTO `data` (`user`, `IP`, `carType`, `ignitionOn`, `chargingOn`, `socPerc`, `socPercBms`, `sohPerc`, `batPowerKw`, `batPowerAmp`, `batVoltage`, `auxVoltage`, `auxAmp`, `batMinC`, `batMaxC`, `batInletC`, `extTemp`, `batFanStatus`, `speedKmh`, `odoKm`, `cumulativeEnergyChargedKWh`, `cumulativeEnergyDischargedKWh`, `gpsLat`, `gpsLon`, `gpsAlt`, `gpsSpeed`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('isiiiddddddddddddddddddddd', $iduser, $_SERVER['REMOTE_ADDR'], $json->carType, $json->ignitionOn, $json->chargingOn, $json->socPerc, $json->socPercBms, $json->sohPerc, $json->batPowerKw, $json->batPowerAmp, $json->batVoltage, $json->auxVoltage, $json->auxAmp, $json->batMinC, $json->batMaxC, $json->batInletC, $json->extTemp, $json->batFanStatus, $json->speedKmh, $json->odoKm, $json->cumulativeEnergyChargedKWh, $json->cumulativeEnergyDischargedKWh, $json->gpsLat, $json->gpsLon, $json->gpsAlt, $json->gpsSpeed);

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
      case 9:
        $abrp_json->car_model = "volkswagen:id3:20:45:sr";
        $abrp_json->capacity = 45;
        break;
      case 10:
        $abrp_json->car_model = "volkswagen:id3:20:58:mr";
        $abrp_json->capacity = 58;
        break;
      case 11:
        $abrp_json->car_model = "volkswagen:id3:20:77:lr";
        $abrp_json->capacity = 77;
        break;
      case 12:
        $abrp_json->car_model = "hyundai:ioniq5:22:58";
        $abrp_json->capacity = 58;
        break;
      case 13:
        $abrp_json->car_model = "hyundai:ioniq5:22:74";
        $abrp_json->capacity = 72;
        break;
      case 14:
        $abrp_json->car_model = "hyundai:ioniq5:22:77";
        $abrp_json->capacity = 77;
        break;
      case 15:
        $abrp_json->car_model = "peugeot:e208:20:50";
        $abrp_json->capacity = 50;
        break;
      case 16:
        $abrp_json->car_model = "kia:ev6:22:58";
        $abrp_json->capacity = 58;
        break;
      case 17:
        $abrp_json->car_model = "kia:ev6:22:77";
        $abrp_json->capacity = 77;
        break;
      case 18:
        $abrp_json->car_model = "skoda:enyaq:21:52:meb";
        $abrp_json->capacity = 55;
        break;
      case 19:
        $abrp_json->car_model = "skoda:enyaq:21:58:meb";
        $abrp_json->capacity = 62;
        break;
      case 20:
        $abrp_json->car_model = "skoda:enyaq:21:77:meb";
        $abrp_json->capacity = 82;
        break;
      case 23:
        $abrp_json->car_model = "volkswagen:id4:21:77";
        $abrp_json->capacity = 77;
        break;
      case 24:
        $abrp_json->car_model = "audi:q4:21:52:meb";
        $abrp_json->capacity = 35;
        break;
      case 25:
        $abrp_json->car_model = "audi:q4:21:77:meb";
        $abrp_json->capacity = 40;
        break;
      default:
        return false;
    }

    $abrp_json->utc = time();
    $abrp_json->soc = $json->socPerc;
    if($json->ignitionOn == 1 && $json->chargingOn == 0) {
      $abrp_json->is_parked = 0;
    } else {
      $abrp_json->is_parked = 1;
    }
    $abrp_json->power = $json->batPowerKw * -1;
    if ($json->speedKmh >= 5) {
      $abrp_json->speed = $json->speedKmh;
    } else {
      $abrp_json->speed = 0;
    }
    $abrp_json->is_charging = $json->chargingOn;
    if($json->chargingOn == 1 && $json->batPowerKw > 22) {
      $abrp_json->is_dcfc = 1;
    } elseif($json->chargingOn == 1) {
      $abrp_json->is_dcfc = 0;
    }
    $abrp_json->lat = $json->gpsLat;
    $abrp_json->lon = $json->gpsLon;
    $abrp_json->elevation = $json->gpsAlt;
    $abrp_json->kwh_charged = $json->cumulativeEnergyChargedKWh;
    $abrp_json->soh = $json->sohPerc;
    $abrp_json->batt_temp = ($json->batMinC + $json->batMaxC) / 2;
    $abrp_json->ext_temp = $json->extTemp;
    $abrp_json->voltage = $json->batVoltage;
    $abrp_json->current = $json->batPowerAmp * -1;
    if ($json->odoKm > 0) {
      $abrp_json->odometer = $json->odoKm;
    } else {
      if($lastOdoKm = $this->getLastOdo($iduser)) {
        $abrp_json->odometer = $lastOdoKm;
      }
    }

    $abrp_dta_post = [
        'api_key' => $abrp_api_key,
        'token' => $settings->abrp_token,
        'tlm' => json_encode($abrp_json)
      ];

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, 'https://api.iternio.com/1/tlm/send');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($abrp_dta_post));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);

    if ($info['http_code'] == 200) {
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

  public function getChargingInMonth($json) {
    if(!$iduser = $this->getApiKeyUser($json->apikey)) {
      return false;
    }

		if(!isset($date_from, $date_to)) {
			$date_from = date('Y-m-d h:m:s', mktime(0, 0, 0, $json->month, 1, $json->year));
			$date_to = date('Y-m-d h:m:s', mktime(23, 59, 59, $json->month+1, 0, $json->year));
		}

		if(isset($json->gpsLat, $json->gpsLon)) {
			$query = $this->mysqli->prepare("SELECT `iddata`, `timestamp`, `carType`, `socPerc`, `batPowerKw`, `cumulativeEnergyChargedKWh`, `gpsLat`, `gpsLon` FROM `data` WHERE `user` = ? AND `gpsLat` < ? + 0.02 AND `gpsLat` > ? + -0.02 AND `gpsLon` < ? + 0.02 AND `gpsLon` > ? + -0.02 AND `chargingOn` = 1 AND `timestamp` >= ? AND `timestamp` <= ? ORDER BY `timestamp` DESC");
			$query->bind_param('iddddss', $iduser, $json->gpsLat, $json->gpsLat, $json->gpsLon, $json->gpsLon, $date_from, $date_to);
		} else {
			$query = $this->mysqli->prepare("SELECT `iddata`, `timestamp`, `carType`, `socPerc`, `batPowerKw`, `cumulativeEnergyChargedKWh`, `gpsLat`, `gpsLon` FROM `data` WHERE `user` = ? AND `chargingOn` = 1 AND `timestamp` >= ? AND `timestamp` <= ? ORDER BY `timestamp` DESC");
			$query->bind_param('iss', $iduser, $date_from, $date_to);
		}

		if(!$query->execute()) {
			return false;
		}
    $result = $query->get_result();
    $return = array();

		$last_timestamp = 0;
		$max_kwh = 0;
		$last_kwh = 0;
		$max_perc = 0;
		$last_perc = 0;
		$is_dc = false;
		$i = 0;
    $return[$i] = new \stdClass();

    while ($obj = $result->fetch_object()) {
			if(!$max_kwh || !$max_perc) {
				$max_kwh = $obj->cumulativeEnergyChargedKWh;
				$max_perc = $obj->socPerc;
			}
			if($last_timestamp > strtotime($obj->timestamp) + 1800) {
				$return[$i]->kwh = $max_kwh - $last_kwh;
				$return[$i]->max_perc = $max_perc;
				$return[$i]->carType = $obj->carType;
				$return[$i]->is_dc = $is_dc;
				$i++;
        $return[$i] = new \stdClass();
				$max_kwh = $obj->cumulativeEnergyChargedKWh;
				$max_perc = $obj->socPerc;
				$is_dc = false;
			}
			if($obj->batPowerKw > 22) {
				$is_dc = true;
			}
			$return[$i]->timestamp = $obj->timestamp;
			$last_kwh = $obj->cumulativeEnergyChargedKWh;
			$return[$i]->min_perc = $obj->socPerc;
			$return[$i]->gps_lat = $obj->gpsLat;
			$return[$i]->gps_lon = $obj->gpsLon;
			$return[$i]->iddata = $obj->iddata;
			$last_timestamp = strtotime($obj->timestamp);
    }

    return $return;
  }

}
?>
