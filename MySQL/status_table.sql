SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `current_state` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lasttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

ALTER TABLE `current_state`
  ADD PRIMARY KEY (`id`);
