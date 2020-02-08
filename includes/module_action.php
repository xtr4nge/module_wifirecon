<? 
/*
    Copyright (C) 2020 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];


function killRegex($regex){
	
	$exec = "ps aux|grep -E '$regex' | grep -v grep | awk '{print $2}'";
	exec($exec,$output);
	
	if (count($output) > 0) {
		$exec = "kill " . $output[0];
		exec_fruitywifi($exec);
	}
	
}

function copyLogsHistory() {
	
	global $bin_cp;
	global $bin_mv;
	global $mod_logs;
	global $mod_logs_history;
	global $bin_echo;
	
	if ( 0 < filesize( $mod_logs ) ) {
		$exec = "$bin_cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
		exec_fruitywifi($exec);
		
		$exec = "$bin_echo '' > $mod_logs";
		//exec_fruitywifi($exec);
	}
}


if ($action == "start") {
	
	$exec = "$bin_echo '' > $mod_logs";
	exec_fruitywifi($exec);
	
	if ($mod_pkt_type != "") $options_type = "-t $mod_pkt_type";
	if ($mod_pkt_subtype != "") $options_subtype = "-s $mod_pkt_subtype";
	if ($mod_pkt_channel != "") $options_channel = "-c $mod_pkt_channel";
	
	$exec = "screen -d -m python scan-recon.py -i mon0 $options_type $options_subtype $options_channel -l $mod_logs";
	exec_fruitywifi($exec);

} else if($action == "stop") {

	killRegex("scan-recon");
	killRegex("scan-recon");
	
	// LOGS COPY
	copyLogsHistory();
	
}

if ($action == "startmon") {
	 start_monitor_mode($io_in_iface_extra);	
} else if($action == "stopmon") {
	 stop_monitor_mode($io_in_iface_extra);		
}



if ($install == "install_$mod_name") {

    $exec = "chmod 755 install.sh";
    exec_fruitywifi($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_fruitywifi($exec);

    header("Location: ../../install.php?module=$mod_name");
    exit;
}

if ($page == "status") {
    header("Location: ../../../action.php");
} else {
    header("Location: ../../action.php?page=$mod_name");
}

?>
