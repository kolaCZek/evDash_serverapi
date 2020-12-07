CREATE TABLE `users` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `apikey` varchar(12) NOT NULL,
  `IP` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  KEY `apikey` (`apikey`)
);

CREATE TABLE `data` (
  `iddata` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `IP` varchar(16) DEFAULT NULL,
  `carType` int(11) DEFAULT NULL,
  `socPerc` float DEFAULT NULL,
  `sohPerc` float DEFAULT NULL,
  `batPowerKw` float DEFAULT NULL,
  `batPowerAmp` float DEFAULT NULL,
  `batVoltage` float DEFAULT NULL,
  `auxVoltage` float DEFAULT NULL,
  `auxAmp` float DEFAULT NULL,
  `batMinC` float DEFAULT NULL,
  `batMaxC` float DEFAULT NULL,
  `batInletC` float DEFAULT NULL,
  `batFanStatus` float DEFAULT NULL,
  `speedKmh` float DEFAULT NULL,
  `cumulativeEnergyChargedKWh` float DEFAULT NULL,
  `cumulativeEnergyDischargedKWh` float DEFAULT NULL,
  PRIMARY KEY (`iddata`),
  KEY `timestamp` (`timestamp`),
  KEY `userid_fk_idx` (`user`),
  CONSTRAINT `userid_fk` FOREIGN KEY (`user`) REFERENCES `users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION
);
