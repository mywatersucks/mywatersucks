<?php

function get_id($location){
  return 1;
}

include_once("config.php");

$db = new Zebra_Database();
$db->debug = false;
$db->connect($host, $user, $password, $dbname);
$db->set_charset();

$data = $_POST['user'];
$data['location_id'] = get_id($_POST['location']);

$db->insert('users', $data);

$db->close();
$db->show_debug_console();

include_once("pwconfig.php");

$db1 = new Zebra_Database();
$db1->debug = false;
$db1->connect($pwhost, $pwuser, $pwpwd, $pwdbname);
$db1->set_charset();

$pw_hash = crypt($_POST['password'], '$2a$07$rhokwaterhackathonrandomsalt$');

$db1->insert('authentications',
  array('uname' => $_POST['user']['uname'], 'pwhash' => $pw_hash));

$db1->close();
$db1->show_debug_console();

?>
