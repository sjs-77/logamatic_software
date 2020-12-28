<?php

# PHP script to transfer status data in regular intervals from the status table to an archive database table
#
# License: CC-BY-SA 3.0
# Author: Sebastian Suchanek

  # Turn on error reporting
  error_reporting(E_ALL);

  # Set all necessary data for sending status mails - adjust to personal requirements
  $email_from = 'localpart@domain.tld';
  $email_from_name = 'Buderus Logamatic 2107';
  $email_to[] = 'Some User <localpart@domain.tld>';
  $email_to[] = 'Some Other User <localpart@domain.tld>';
  $threshold_data_age = 10*60;

  # Initialise database connection to database with status table - adjust credentials as needed 
  $mysql_pi = new mysqli('SERVER', 'USER', 'PASSWORD', 'DATABASE');
  if ($mysql_pi->connect_error) {
    die('Connect Error (' . $mysql_pi->connect_errno . ') '. $mysql_pi->connect_error);
  }
  $mysql_pi->set_charset("utf8");

  # Initialise database connection to database with archive table - adjust credentials as needed 
  $mysql_server = new mysqli('SERVER', 'USER', 'PASSWORD', 'DATABASE');
  if ($mysql_server->connect_error) {
    die('Connect Error (' . $mysql_server->connect_errno . ') '. $mysql_server->connect_error);
  }
  $mysql_server->set_charset("utf8");

  # Send status and error mails to specified recipients
  function send_mail($email_to, $message) {
    global $email_from;
    global $email_from_name;
    foreach($email_to as $to) {
      $subject = 'Logamatic Status Update';
      $headers = "From: ".$email_from_name." <".$email_from.">\n";
      $headers .= "Return-Path: <".$email_from.">\r\n";
      mail($to, $subject, $message, $headers);
    }
  }

  # Get current status from status table
  $sql = 'SELECT lasttime, hc1_state_1, hc1_state_2, hc1_feedtemp_set, hc1_feedtemp_act, hc1_roomtemp_set, hc1_roomtemp_act, hc1_pump, hc1_curve_p10, hc1_curve_0, hc1_curve_m10, ww_state_1, ww_state_2, ww_temp_set, ww_temp_act, ww_state_pumps, conf_amb_temp, conf_amb_temp_filtered, boiler_temp_set, boiler_temp_act, boiler_burner_on, boiler_burner_off, boiler_errors, boiler_state_1, boiler_burner_state_1, boiler_hours1_1, boiler_hours1_2, boiler_hours1_3, boiler_burner_state_2, boiler_state_2 FROM current_state';
  $result_pi = $mysql_pi->query($sql)->fetch_array();

  $sql = 'SELECT boiler_errors, boiler_state_1 FROM archive ORDER BY `time` DESC LIMIT 1';
  $last_server_data = $mysql_server->query($sql)->fetch_array();

  # Check if retreived data is older then configured threshold and send a corresponding warning mail if necessary
  $date_data = strtotime($result_pi['lasttime']);
  $date_now = time();
  $data_age = $date_now - $date_data;
  if ($data_age > $threshold_data_age) {
    send_mail($email_to, 'WARNING: Last data received from heating unit is older than '.$data_age.'s ('.round($data_age/60).'min)!');
  }

  # Write status to archive database table
  $date = date('Y-m-d H:i:00');

  $sql = 'INSERT INTO archive (`time`, hc1_state_1, hc1_state_2, hc1_feedtemp_set, hc1_feedtemp_act, hc1_roomtemp_set, hc1_roomtemp_act, hc1_pump, hc1_curve_p10, hc1_curve_0, hc1_curve_m10, '
                              .'ww_state_1, ww_state_2, ww_temp_set, ww_temp_act, ww_state_pumps, '
                              .'conf_amb_temp, conf_amb_temp_filtered, '
                              .'boiler_temp_set, boiler_temp_act, boiler_burner_on, boiler_burner_off, boiler_errors, boiler_state_1, boiler_burner_state_1, boiler_hours1_1, boiler_hours1_2, boiler_hours1_3, boiler_burner_state_2, boiler_state_2) '
        .'VALUES ('
        .'"'.$date.'", ';
  if (is_null($result_pi['hc1_state_1'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_state_1'].', ';}
  if (is_null($result_pi['hc1_state_2'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_state_2'].', ';}
  if (is_null($result_pi['hc1_feedtemp_set'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_feedtemp_set'].', ';}
  if (is_null($result_pi['hc1_feedtemp_act'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_feedtemp_act'].', ';}
  if (is_null($result_pi['hc1_roomtemp_set'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_roomtemp_set'].', ';}
  if (is_null($result_pi['hc1_roomtemp_act'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_roomtemp_act'].', ';}
  if (is_null($result_pi['hc1_pump'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_pump'].', ';}
  if (is_null($result_pi['hc1_curve_p10'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_curve_p10'].', ';}
  if (is_null($result_pi['hc1_curve_0'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_curve_0'].', ';}
  if (is_null($result_pi['hc1_curve_m10'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['hc1_curve_m10'].', ';}
  if (is_null($result_pi['ww_state_1'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['ww_state_1'].', ';}
  if (is_null($result_pi['ww_state_2'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['ww_state_2'].', ';}
  if (is_null($result_pi['ww_temp_set'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['ww_temp_set'].', ';}
  if (is_null($result_pi['ww_temp_act'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['ww_temp_act'].', ';}
  if (is_null($result_pi['ww_state_pumps'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['ww_state_pumps'].', ';}
  if (is_null($result_pi['conf_amb_temp'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['conf_amb_temp'].', ';}
  if (is_null($result_pi['conf_amb_temp_filtered'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['conf_amb_temp_filtered'].', ';}
  if (is_null($result_pi['boiler_temp_set'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_temp_set'].', ';}
  if (is_null($result_pi['boiler_temp_act'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_temp_act'].', ';}
  if (is_null($result_pi['boiler_burner_on'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_burner_on'].', ';}
  if (is_null($result_pi['boiler_burner_off'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_burner_off'].', ';}
  if (is_null($result_pi['boiler_errors'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_errors'].', ';}
  if (is_null($result_pi['boiler_state_1'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_state_1'].', ';}
  if (is_null($result_pi['boiler_burner_state_1'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_burner_state_1'].', ';}
  if (is_null($result_pi['boiler_hours1_1'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_hours1_1'].', ';}
  if (is_null($result_pi['boiler_hours1_2'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_hours1_2'].', ';}
  if (is_null($result_pi['boiler_hours1_3'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_hours1_3'].', ';}
  if (is_null($result_pi['boiler_burner_state_2'])) {$sql .= 'NULL, ';} else {$sql .= $result_pi['boiler_burner_state_2'].', ';}
  if (is_null($result_pi['boiler_state_2'])) {$sql .= 'NULL)';} else {$sql .= $result_pi['boiler_state_2'].')';}

  $result_server = $mysql_server->query($sql);

  # Send status mails in various special cases
  if (!($last_server_data['boiler_state_1'] & 1) && ($result_pi['boiler_state_1'] & 1)) {
    send_mail($email_to, 'INFO: emission test started.');
  }
  if (($last_server_data['boiler_state_1'] & 1) && !($result_pi['boiler_state_1'] & 1)) {
    send_mail($email_to, 'INFO: emission test finished.');
  }
  if (!($last_server_data['boiler_errors'] & 1) && ($result_pi['boiler_errors'] & 1)) {
    send_mail($email_to, 'ERROR: Burner fault!');
  }
  if (($last_server_data['boiler_errors'] & 1) && !($result_pi['boiler_errors'] & 1)) {
    send_mail($email_to, 'INFO: Burner fault cleared.');
  }

?>
