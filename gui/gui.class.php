<?php
class Gui {
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

	public function signIn($apikey) {
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

	public function getLastRecord($uid) {
		$query = $this->mysqli->prepare("SELECT `timestamp`, `carType`, `ignitionOn`, `chargingOn`, `socPerc`, `socPercBms`, `sohPerc`, `batPowerKw`, `batPowerAmp`, `batVoltage`, `auxVoltage`, `batMinC`, `batMaxC`, `batInletC`, `extTemp`, `batFanStatus`, `cumulativeEnergyChargedKWh`, `cumulativeEnergyDischargedKWh` FROM `data` WHERE `user` = ? ORDER BY `timestamp` DESC LIMIT 1");

		$query->bind_param('i', $uid);

		if($query->execute()) {
			return $query->get_result()->fetch_object();
		}
	}

	public function getSettings($uid) {
		$query = $this->mysqli->prepare("SELECT `timezone`, `notifications`, `abrp_enabled`, `abrp_token` FROM `users` WHERE `iduser` = ?");
		$query->bind_param('i', $uid);

		if($query->execute()) {
			return $query->get_result()->fetch_object();
		}
	}

	public function setSettings($uid, $timezone, $notifications, $abrp_enabled, $abrp_token) {
	    $query = $this->mysqli->prepare("UPDATE `users` SET `timezone` = ?, `notifications` = ?, `abrp_enabled` = ?, `abrp_token` = ? WHERE `iduser` = ?");
	    $query->bind_param('siisi', $timezone, $notifications, $abrp_enabled, $abrp_token, $uid);

	    if($query->execute()) {
	      return true;
	    } else {
	      return false;
	    }
	}

	public function getChargingList($uid, $date_from = null, $date_to = null, $lat = null, $lon = null) {
		if(!isset($date_from, $date_to)) {
			$date_from = date('Y-m-d h:m:s', mktime(0, 0, 0, date("m"), 1, date("Y")));
			$date_to = date('Y-m-d h:m:s', mktime(23, 59, 59, date("m")+1, 0, date("Y")));
		}

		if(isset($lat, $lon)) {
			$query = $this->mysqli->prepare("SELECT `iddata`, `timestamp`, `carType`, `socPerc`, `batPowerKw`, `cumulativeEnergyChargedKWh`, `gpsLat`, `gpsLon` FROM `data` WHERE `user` = ? AND `gpsLat` < ? + 0.02 AND `gpsLat` > ? + -0.02 AND `gpsLon` < ? + 0.02 AND `gpsLon` > ? + -0.02 AND `chargingOn` = 1 AND `timestamp` >= ? AND `timestamp` <= ? ORDER BY `timestamp` DESC");
			$query->bind_param('iddddss', $uid, $lat, $lat, $lon, $lon, $date_from, $date_to);
		} else {
			$query = $this->mysqli->prepare("SELECT `iddata`, `timestamp`, `carType`, `socPerc`, `batPowerKw`, `cumulativeEnergyChargedKWh`, `gpsLat`, `gpsLon` FROM `data` WHERE `user` = ? AND `chargingOn` = 1 AND `timestamp` >= ? AND `timestamp` <= ? ORDER BY `timestamp` DESC");
			$query->bind_param('iss', $uid, $date_from, $date_to);
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
