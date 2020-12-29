SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `archive` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hc1_state_1` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_state_2` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_feedtemp_set` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_feedtemp_act` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_roomtemp_set` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_roomtemp_act` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_pump` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_curve_p10` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_curve_0` tinyint(4) UNSIGNED DEFAULT NULL,
  `hc1_curve_m10` tinyint(4) UNSIGNED DEFAULT NULL,
  `ww_state_1` tinyint(4) UNSIGNED DEFAULT NULL,
  `ww_state_2` tinyint(4) UNSIGNED DEFAULT NULL,
  `ww_temp_set` tinyint(4) UNSIGNED DEFAULT NULL,
  `ww_temp_act` tinyint(4) UNSIGNED DEFAULT NULL,
  `ww_state_pumps` tinyint(4) UNSIGNED DEFAULT NULL,
  `conf_amb_temp` tinyint(4) DEFAULT NULL,
  `conf_amb_temp_filtered` tinyint(4) DEFAULT NULL,
  `boiler_temp_set` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_temp_act` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_burner_on` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_burner_off` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_errors` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_state_1` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_burner_state_1` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_hours1_1` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_hours1_2` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_hours1_3` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_burner_state_2` tinyint(4) UNSIGNED DEFAULT NULL,
  `boiler_state_2` tinyint(4) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rawlog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `length` tinyint(3) UNSIGNED NOT NULL,
  `raw_telegram` varchar(10) DEFAULT NULL,
  `telegram_byte1` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte2` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte3` int(10) DEFAULT NULL,
  `telegram_byte4` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte5` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte6` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte7` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte8` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte9` int(10) UNSIGNED DEFAULT NULL,
  `telegram_byte10` int(10) UNSIGNED DEFAULT NULL,
  `decoded` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `iTIME` (`time`);

ALTER TABLE `rawlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kDecoded` (`decoded`),
  ADD KEY `kAddress` (`telegram_byte1`,`telegram_byte2`);
