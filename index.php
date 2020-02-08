<? 
/*
    Copyright (C) 2013-2020 xtr4nge [_AT_] gmail.com

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
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWiFi : WiFiRecon</title>
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../../../style.css" />

<script src="includes/scripts.js"></script>
<style>
.div0 {
        width: 350px;
        margin-top: 2px;
 }
.div1 {
        width: 120px;
        display: inline-block;
        text-align: left;
        margin-right: 10px;
}
.divName {
        width: 200px;
        display: inline-block;
        text-align: left;
        margin-right: 10px;
}
.divEnabled {
        width: 63px;
        color: lime;
        display: inline-block;
        font-weight: bold;
}
.divDisabled {
        width: 63px;
        color: red;
        display: inline-block;
        font-weight: bold;
}
.divAction {
        width: 80px;
        display: inline-block;
        font-weight: bold;
}
.divDivision {
        width: 16px;
        display: inline-block;
}
.divStartStopB {
        width: 34px;
}
.divBSSID {
        width: 140px;
        display: inline-block;
        text-align: left;
        margin-right: 10px;
}
.divSSID{
    width: 140px;
    display: inline-block;
    text-align: left;
    margin-right: 10px;
}
.divAP {
    background-color: #ECECEC;
    padding-left: 2px;
    padding-top: 4px;
    padding-bottom: 4px;
    border-radius: 5px;
}
.divCLIENT{
background-color: #FEFEFE;
padding-left: 2px;
padding-top: 4px;
padding-bottom: 4px;
border-radius: 5px;
}
.ui-widget-content{
border: 1px;
}

</style>
<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<? include "../menu.php"; ?>

<br>

<?
include "../../login_check.php";
include "../../config/config.php";
include "_info_.php";
include "../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_GET["logfile"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    regex_standard($_POST["service"], "msg.php", $regex_extra);
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];
$tempname = $_GET["tempname"];
$service = $_POST["service"];

// DELETE LOG
if ($logfile != "" and $action == "delete") {
    $exec = "$bin_rm ".$mod_logs_history.$logfile.".log";
    exec_fruitywifi($exec);
}

?>

<div class="rounded-top" align="left">&nbsp;<b><?=$mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp;version <?=$mod_version?><br>

     <?
    $ismoduleup = exec($mod_mon0);
    if ($ismoduleup != "") {
        echo "&nbsp;$mod_dep  <font color='lime'><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=$mod_name&action=stopmon&page=module'><b>stop</b></a>";
    } else { 
        echo "&nbsp;$mod_dep  <font color='red'><b>disabled</b></font>. | <a href='includes/module_action.php?service=$mod_name&action=startmon&page=module'><b>start</b></a>"; 
    }
    ?>
    
    <?
    $ismoduleup = exec($mod_isup);
    if ($ismoduleup != "") {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;$mod_alias  <font color='lime'><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=$mod_name&action=stop&page=module'><b>stop</b></a>";
    } else { 
        echo "&nbsp;&nbsp;&nbsp;&nbsp;$mod_alias  <font color='red'><b>disabled</b></font>. | <a href='includes/module_action.php?service=$mod_name&action=start&page=module'><b>start</b></a>"; 
    }
    ?>  

</div>

<br>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">


    <div id="result" class="module">
        <ul>
            <li><a href="#tab-output">Output</a></li>
            <li><a href="#tab-config">Config</a></li>
            <li><a href="#tab-filter">Filter</a></li>
            <li><a href="#tab-history">History</a></li>
            <li><a href="#tab-about">About</a></li>
        </ul>
        
        <!-- OUTPUT -->

        <div id="tab-output" class="history">
            
            <form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="index.php">
            <input type="submit" value="refresh">
            
            </form>
            <br>
            
            <div id="dialog" title="-">
                <br>
                <div>Filter</div>
                <div>
                    <a href='#' class='btn btn-default btn-sm' style='color: #000000;' role='button' onclick="addMAC()">Add MAC</a>
                    <a href='#' class='btn btn-default btn-sm' style='color: #000000;' role='button'>Remove MAC</a>
                </div>
                <hr>
                <div>Tracking</div>
                <div>
                    <a href='#' class='btn btn-default btn-sm' style='color: #000000;' role='button'>Add MAC</a>
                    <a href='#' class='btn btn-default btn-sm' style='color: #000000;' role='button'>Remove MAC</a>
                </div>
                <hr>
                <div>Deauth Client</div>
                <a href='#' class='btn btn-default btn-sm' style='color: #000000;' role='button'>Deauth</a>
            </div>
               
            
            <div id="ap">
                <div class='divBSSID'><b>BSSID</b></div>
                <div class='div1'><b>Channel</b></div>
                <div class='div1'><b>Signal</b></div>
                <div class='div1'><b>Security</b></div>
                <div class='divSSID'><b>SSID</b></div>
            </div>
            
            <br>
            
            <div id="client">
                <div class='divBSSID'><b>MAC</b></div>
            </div>
        </div>

        <!-- CONFIG -->

        <div id="tab-config" class="history">
            <div id="type">
                <label>TYPE</label>
                <br>
                <? $a_type = explode(",", $mod_pkt_type); ?>
                <input type="checkbox" name="type" id="type" value="0" onclick="setOptionCheckbox('type')" <? if (in_array("0", $a_type)) echo "checked"; ?> > [0] Management frames
                <br>
                <input type="checkbox" name="type" id="type" value="1" onclick="setOptionCheckbox('type')" <? if (in_array("1", $a_type)) echo "checked"; ?> > [1] Control frames
                <br>
                <input type="checkbox" name="type" id="type" value="2" onclick="setOptionCheckbox('type')" <? if (in_array("2", $a_type)) echo "checked"; ?> > [2] Data frames
                <br>
            </div>
            <br>
            <div id="subtype">
                <label>SUBTYPE</label>
                <br>
                <? $a_subtype = explode(",", $mod_pkt_subtype); ?>
                <input type="checkbox" name="subtype" value="0" onclick="setOptionCheckbox('subtype')" <? if (in_array("0", $a_subtype)) echo "checked"; ?> > [0] Association request
                <br>
                <input type="checkbox" name="subtype" value="1" onclick="setOptionCheckbox('subtype')" <? if (in_array("1", $a_subtype)) echo "checked"; ?> > [1] Association response
                <br>
                <input type="checkbox" name="subtype" value="2" onclick="setOptionCheckbox('subtype')" <? if (in_array("2", $a_subtype)) echo "checked"; ?> > [2] Reassociation request
                <br>
                <input type="checkbox" name="subtype" value="3" onclick="setOptionCheckbox('subtype')" <? if (in_array("3", $a_subtype)) echo "checked"; ?> > [3] Reassociation response
                <br>
                <input type="checkbox" name="subtype" value="4" onclick="setOptionCheckbox('subtype')" <? if (in_array("4", $a_subtype)) echo "checked"; ?> > [4] Probe request
                <br>
                <input type="checkbox" name="subtype" value="5" onclick="setOptionCheckbox('subtype')" <? if (in_array("5", $a_subtype)) echo "checked"; ?> > [5] Probe response
                <br>
                <input type="checkbox" name="subtype" value="8" onclick="setOptionCheckbox('subtype')" <? if (in_array("8", $a_subtype)) echo "checked"; ?> > [8] Beacon
                <br>
                <input type="checkbox" name="subtype" value="10" onclick="setOptionCheckbox('subtype')" <? if (in_array("10", $a_subtype)) echo "checked"; ?> > [10] Disassociate
                <br>
                <input type="checkbox" name="subtype" value="11" onclick="setOptionCheckbox('subtype')" <? if (in_array("11", $a_subtype)) echo "checked"; ?> > [11] Authentication
                <br>
                <input type="checkbox" name="subtype" value="12" onclick="setOptionCheckbox('subtype')" <? if (in_array("12", $a_subtype)) echo "checked"; ?> > [12] Deauthentication
                <br>
                <input type="checkbox" name="subtype" value="13" onclick="setOptionCheckbox('subtype')" <? if (in_array("13", $a_subtype)) echo "checked"; ?> > [13] Action frames
                <br>
            </div>
            <br>
            <div id="channel">
                <label>CHANNEL</label>
                <br>
                <? $a_channel = explode(",", $mod_pkt_channel); ?>
                <input type="checkbox" name="channel" value="1" onclick="setOptionCheckbox('channel')" <? if (in_array("1", $a_channel)) echo "checked"; ?> > 1
                
                <input type="checkbox" name="channel" value="2" onclick="setOptionCheckbox('channel')" <? if (in_array("2", $a_channel)) echo "checked"; ?> > 2
                
                <input type="checkbox" name="channel" value="3" onclick="setOptionCheckbox('channel')" <? if (in_array("3", $a_channel)) echo "checked"; ?> > 3
                
                <input type="checkbox" name="channel" value="4" onclick="setOptionCheckbox('channel')" <? if (in_array("4", $a_channel)) echo "checked"; ?> > 4
                
                <input type="checkbox" name="channel" value="5" onclick="setOptionCheckbox('channel')" <? if (in_array("5", $a_channel)) echo "checked"; ?> > 5
                
                <input type="checkbox" name="channel" value="6" onclick="setOptionCheckbox('channel')" <? if (in_array("6", $a_channel)) echo "checked"; ?> > 6
                
                <input type="checkbox" name="channel" value="7" onclick="setOptionCheckbox('channel')" <? if (in_array("7", $a_channel)) echo "checked"; ?> > 7
                
                <input type="checkbox" name="channel" value="8" onclick="setOptionCheckbox('channel')" <? if (in_array("8", $a_channel)) echo "checked"; ?> > 8
                
                <input type="checkbox" name="channel" value="9" onclick="setOptionCheckbox('channel')" <? if (in_array("9", $a_channel)) echo "checked"; ?> > 9
                
                <input type="checkbox" name="channel" value="10" onclick="setOptionCheckbox('channel')" <? if (in_array("10", $a_channel)) echo "checked"; ?> > 10
                
                <input type="checkbox" name="channel" value="11" onclick="setOptionCheckbox('channel')" <? if (in_array("11", $a_channel)) echo "checked"; ?> > 11
                
                <input type="checkbox" name="channel" value="12" onclick="setOptionCheckbox('channel')" <? if (in_array("12", $a_channel)) echo "checked"; ?> > 12
                
                <input type="checkbox" name="channel" value="13" onclick="setOptionCheckbox('channel')" <? if (in_array("13", $a_channel)) echo "checked"; ?> > 13
                <br>
                <input type="checkbox" name="channel" value="36" onclick="setOptionCheckbox('channel')" <? if (in_array("36", $a_channel)) echo "checked"; ?> > 36
                
                <input type="checkbox" name="channel" value="40" onclick="setOptionCheckbox('channel')" <? if (in_array("40", $a_channel)) echo "checked"; ?> > 40
                
                <input type="checkbox" name="channel" value="44" onclick="setOptionCheckbox('channel')" <? if (in_array("44", $a_channel)) echo "checked"; ?> > 44
                
                <input type="checkbox" name="channel" value="48" onclick="setOptionCheckbox('channel')" <? if (in_array("48", $a_channel)) echo "checked"; ?> > 48
                
                <input type="checkbox" name="channel" value="52" onclick="setOptionCheckbox('channel')" <? if (in_array("52", $a_channel)) echo "checked"; ?> > 52
                
                <input type="checkbox" name="channel" value="56" onclick="setOptionCheckbox('channel')" <? if (in_array("56", $a_channel)) echo "checked"; ?> > 56
                
                <input type="checkbox" name="channel" value="60" onclick="setOptionCheckbox('channel')" <? if (in_array("60", $a_channel)) echo "checked"; ?> > 60
                
                <input type="checkbox" name="channel" value="64" onclick="setOptionCheckbox('channel')" <? if (in_array("64", $a_channel)) echo "checked"; ?> > 64
            </div>
        </div>

        <!-- END CONFIG -->

        <!-- FILTER -->

        <div id="tab-filter" class="history">
            <b>Clients</b>
            <br>
            <input class="form-control input-sm" value="Allow" style="width: 100px; display: inline-block; " id="station-switch" type="text" disabled />
            <input id="add-mac-switch" class="btn btn-default btn-sm" type="submit" value="switch" onclick="switchStation()">
            <br>
            <select class="module-content" id="pool-station" multiple="multiple" style="width: 265px; height: 150px">

            </select>
            <br>
            <input class="form-control input-sm" placeholder="MAC Address" style="width: 200px; display: inline-block; " id="newMACText" type="text" />
            <input id="add" class="btn btn-default btn-sm" type="submit" value="+" onclick="addListStation();">
            <input id="remove" class="btn btn-default btn-sm" type="submit" value="-" onclick="removeListStation()">
            
            <hr>
            <b>SSID</b>
            <br>
            <input class="form-control input-sm" value="Blacklist" style="width: 100px; display: inline-block; " id="ssid-switch" type="text" disabled />
            <input id="add-ssid-switch" class="btn btn-default btn-sm" type="submit" value="switch" onclick="switchSSID()">
            <br>
            <select class="module-content" id="pool-ssid" multiple="multiple" style="width: 265px; height: 150px">

            </select>
            <br>
            <input class="form-control input-sm" placeholder="SSID" style="width: 200px; display: inline-block; " id="newSSIDText" type="text" />
            <input id="add-ssid" class="btn btn-default btn-sm" type="submit" value="+" onclick="addListSSID()">
            <input id="remove-ssid" class="btn btn-default btn-sm" type="submit" value="-" onclick="removeListSSID()">
            

        </div>

        <!-- END FILTER -->

        <!-- HISTORY -->

        <div id="tab-history" class="history">
            <input type="submit" value="refresh">
            <br><br>
            
            <?
            $logs = glob($mod_logs_history.'*.log');
            print_r($a);

            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=delete&tab=4'><b>x</b></a> ";
                echo $filename . " | ";
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=view'><b>view</b></a>";
                echo "<br>";
            }
            ?>
            
        </div>
        
        <!-- ABOUT -->

        <div id="tab-about" class="history">
            <? include "includes/about.php"; ?>
        </div>

        <!-- END ABOUT -->
        
    </div>

    <div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
        Loading...
    </div>

    <script>
        // BLOCK 3, 4, 5
        $('#loading').hide();
    </script>

    <?
    if ($_GET["tab"] == 1) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 0 });";
        echo "</script>";
    } else if ($_GET["tab"] == 2) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 1 });";
        echo "</script>";
    } else if ($_GET["tab"] == 3) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 2 });";
        echo "</script>";
    } else if ($_GET["tab"] == 4) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 3 });";
        echo "</script>";
    } 
    ?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

<script>

// BLOCK 6

// EXEC SCAN WiFi
scanRecon()

// EXEC LOAD POOL
loadPoolStation()
loadPoolSSID()


</script>

</body>
</html>
