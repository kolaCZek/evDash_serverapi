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
		$query = $this->mysqli->prepare("SELECT `timestamp`, `carType`, `ignitionOn`, `chargingOn`, `socPerc`, `sohPerc`, `batPowerKw`, `batPowerAmp`, `batVoltage`, `auxVoltage`, `batMinC`, `batMaxC`, `batInletC`, `batFanStatus`, `cumulativeEnergyChargedKWh`, `cumulativeEnergyDischargedKWh` FROM `data` WHERE `user` = ? ORDER BY `timestamp` DESC LIMIT 1");

		$query->bind_param('i', $uid);

		if($query->execute()) {
			return $query->get_result()->fetch_object();      
		}
	}

	public function getSettings($uid) {
		$query = $this->mysqli->prepare("SELECT `timezone`, `notifications` FROM `users` WHERE `iduser` = ?");
		$query->bind_param('i', $uid);

		if($query->execute()) {
			return $query->get_result()->fetch_object();      
		}
	}

	public function setSettings($uid, $timezone, $notifications) {
	    $query = $this->mysqli->prepare("UPDATE `users` SET `timezone` = ?, `notifications` = ? WHERE `iduser` = ?");
	    $query->bind_param('sii', $timezone, $uid, $notifications);

	    if($query->execute()) {
	      return true;
	    } else {
	      return false;
	    }
	}
}
?>