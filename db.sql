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
  `soc` float DEFAULT NULL,
  `soh` float DEFAULT NULL,
  `batK` float DEFAULT NULL,
  `batA` float DEFAULT NULL,
  `batV` float DEFAULT NULL,
  `auxV` float DEFAULT NULL,
  `MinC` float DEFAULT NULL,
  `MaxC` float DEFAULT NULL,
  `InlC` float DEFAULT NULL,
  `fan` float DEFAULT NULL,
  `cumCh` float DEFAULT NULL,
  `cumD` float DEFAULT NULL,
  PRIMARY KEY (`iddata`),
  KEY `timestamp` (`timestamp`),
  KEY `userid_fk_idx` (`user`),
  CONSTRAINT `userid_fk` FOREIGN KEY (`user`) REFERENCES `users` (`iduser`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

