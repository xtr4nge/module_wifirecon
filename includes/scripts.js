// BLOCK 1
/*
$(function() {
  $( "#dialog" ).dialog();
});
*/

$(function() {
$( "#dialog" ).dialog({
    autoOpen: false,
    resizable: false,
    height: 320,
    width: 350,
    modal: true,
    /*
    buttons: {
        "Action": function() {
          $( this ).dialog( "close" );
        },
        Cancel: function() {
        $( this ).dialog( "close" );
        }
    },
    */
    show: {
      effect: "fade",
      duration: 500
    },
    hide: {
      effect: "fade",
      duration: 500
    },
});

$( "#opener" ).click(function() {
    $( "#dialog" ).dialog( "open" );
});
});

function openDialog(data) {
    
    //alert($(id).html())
    //$( "#dialog" ).html(data);                
    $( "#dialog" ).dialog( { title: data } );
    $( "#dialog" ).dialog( "open" );
}

function addMAC() {
    
    value = $( "#dialog" ).dialog( "option", "title" )
    //$("#pool-station").append( value );
    addMAC2Select(value);
    
    
}

function removeMAC() {
    
    value = $( "#dialog" ).dialog( "option", "title" )
    $("#mac-remove").append( value );
}
			

// BLOCK 2

function loadPoolStation() 
{
    $.ajax({
        type: 'GET',
        url: '../ap/includes/ws_action.php',
        data: 'api=/pool/station',
        dataType: 'json',
        success: function (data) {
            //console.log(data);

            $.each(data, function (index, value) {
                //console.log(value);
                
                //value = value.toUpperCase()
                
                // ACTION START
                if (checkValue(value) != true) {
                    $('<option/>').attr('value',value).text(value).appendTo('#pool-station');
                }
                
                if ( document.getElementById("check_"+value) ) {
                    document.getElementById("check_"+value).checked = true;
                }
                
                // ACTION END
                
            });
        }
    });
}

function setPoolStation(value)
{
    $.ajax({
        type: 'GET',
        url: '../ap/includes/ws_action.php',
        data: 'api=/pool/station/'+value,
        dataType: 'json',
        success: function (data) {
            console.log(data);
        }
    });
}

function delPoolStation(value)
{
    $.ajax({
        type: 'GET',
        url: '../ap/includes/ws_action.php',
        data: 'api=/pool/station/'+value+"/del",
        dataType: 'json',
        success: function (data) {
            console.log(data);
        }
    });
}

function loadPoolSSID(args) {
    $.ajax({
        type: 'GET',
        url: '../ap/includes/ws_action.php',
        data: 'api=/pool/ssid',
        dataType: 'json',
        success: function (data) {
            //console.log(data);

            $.each(data, function (index, value) {
                //console.log(value);
                
                // ACTION START
                if (checkValue(value) != true) {
                    $('<option/>').attr('value',value).text(value).appendTo('#pool-ssid');
                }
                
                // ACTION END
                
            });
        }
    });
}

function setPoolSSID(value)
{
    $.ajax({
        type: 'GET',
        url: '../ap/includes/ws_action.php',
        data: 'api=/pool/ssid/'+value,
        dataType: 'json',
        success: function (data) {
            console.log(data);
        }
    });
}

function delPoolSSID(value)
{
    $.ajax({
        type: 'GET',
        //url: '../api/includes/ws_action.php',
        url: '../ap/includes/ws_action.php',
        data: 'api=/pool/ssid/'+value+"/del",
        dataType: 'json',
        success: function (data) {
            console.log(data);
        }
    });
}

function addMAC2Select(value) {
    if (checkValue(value) != true && value != "") {
        $('<option/>').attr('value',value).text(value).appendTo('#pool-station');
    }
}

// REF: https://www.safaribooksonline.com/library/view/jquery-cookbook/9780596806941/ch10s07.html

function addListStation() {
    var value = $('#newMACText').val();
    
    if (checkValue(value) != true && value != "") {
        $('<option/>').attr('value',value).text(value).appendTo('#pool-station');
        setPoolStation(value);
    }
}

function removeListStation() {
    value = $('option:selected',$select).text();
    
    var $select = $('#pool-station');
    $('option:selected',$select).remove();
    
    delPoolStation(value);
}

function addListSSID() {    
    var value = $('#newSSIDText').val();
    
    if (checkValue(value) != true && value != "") {
        $('<option/>').attr('value',value).text(value).appendTo('#pool-ssid');
        setPoolSSID(value);
    }
}

function removeListSSID() {
    value = $('option:selected',$select).text();
    
    var $select = $('#pool-ssid');
    $('option:selected',$select).remove();
    
    delPoolSSID(value);
}

function checkValue(MAC) {
    var exists = false; 
    $('#pool-station option').each(function(){
        //alert(this.text)
        //if (this.value == MAC) {
        if (this.text == MAC) {
            //alert(this.text)
            exists = true;
        }
    });
    return exists
}

function checkBox(data) {
    
    value = data.id.replace("check_", "")
    
    if (data.checked) {
        //addMAC2Select(value)
        if (checkValue(value) != true) {
            $('<option/>').attr('value',value).text(value).appendTo('#pool-station');
            setPoolStation(value);
        }
    } else {
        $("#pool-station option[value='"+value+"']").remove();
        delPoolStation(value);
    }
    
    //alert(data)
    //alert($('#'+data).attr('checked', true));
    //alert(data.checked)
}

function switchStation() {
    value = $("#station-switch").val();
    if (value == "Allow") {
        $("#station-switch").val("Deny");
    } else {
        $("#station-switch").val("Allow");
    }
}

function switchSSID() {
    value = $("#ssid-switch").val();
    if (value == "Blacklist") {
        $("#ssid-switch").val("Whitelist");
    } else {
        $("#ssid-switch").val("Blacklist");
    }
}


// BLOCK 3

$('#formLogs').submit(function(event) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'includes/ajax.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            console.log(data);

            $('#output').html('');
            $.each(data, function (index, value) {
                $("#output").append( value ).append("\n");
            });
            
            $('#loading').hide();
        }
    });
    
    $('#output').html('');
    $('#loading').show()

});

//$('#loading').hide();
    
// BLOCK 4

$('#form1').submit(function(event) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'includes/ajax.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            console.log(data);

            $('#output').html('');
            $.each(data, function (index, value) {
                if (value != "") {
                    $("#output").append( value ).append("\n");
                }
            });
            
            $('#loading').hide();

        }
    });
    
    $('#output').html('');
    $('#loading').show()

});

//$('#loading').hide();
    
// BLOCK 5

$('#formInject2').submit(function(event) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'includes/ajax.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            console.log(data);

            $('#inject').html('');
            $.each(data, function (index, value) {
                $("#inject").append( value ).append("\n");
            });
            
            $('#loading').hide();
            
        }
    });
    
    $('#output').html('');
    $('#loading').show()

});

// STORE OPTIONS (Checkbox)
function setOptionCheckbox(id) {
    data = []
    $("#"+id+" input:checked").each(function() {                    
        //console.log($(this).attr('value'))
        data.push($(this).attr('value'))
        })
    
    value = data.join(",")
    console.log(value)
    
    //value = $("#"+id).val()
    console.log(id + "|" + value)
    
    $.getJSON('../api/includes/ws_action.php?api=/config/module/wifirecon/mod_pkt_'+id+'/'+value, function(data) {});
    
}

//$('#loading').hide();
    
// BLOCK 6

function scanRecon() 
{
    $.ajax({
        type: 'GET',
        //url: '../api/includes/ws_action.php',
        url: 'includes/ws_action.php',
        data: 'api=/scan/recon',
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            //$('#output').html('');
            $.each(data, function (index, value) {
                console.log(value);
                
                v_mac = value[0]
                v_ssid = value[1]
                v_channel = value[2]
                v_security = value[3]
                v_signal = value[4]
                if (value[5]) {
                    v_client = value[5].split("|")
                } else {
                    v_client = ""
                }
                
                if (v_mac != "") {
                    
                    //data_mac = sanitize(v_mac.toUpperCase());
                    //data_ssid = sanitize(v_ssid);
                    
                    content = "<div class='divAP'>"
                    content = content + "<div class='divBSSID'><b>"+ v_mac +"</b></div>"
                    content = content + "<div class='div1'>"+v_channel+"</div>"
                    content = content + "<div class='div1'>"+v_signal+"</div>"
                    content = content + "<div class='div1'>"+v_security+"</div>"
                    content = content + "<div class='divSSID'>"+ v_ssid +"</div>"
                    content = content + "</div>"
                    $("#ap").append(content)
                    
                    content = "<div>"
                    for(i = 0; i < v_client.length; i++)
                    {
                        //value = v_client[i];
                        
                        //value = sanitize(v_client[i].toUpperCase());
                        data = v_client[i];
                        if (data != "") {
                            content = content + "<input id='check_"+data+"' type='checkbox' onclick='checkBox(this)'> ";
                            content = content + "<div class='divCLIENT' style='display: inline-block;' onclick='return false; openDialog(this.innerHTML)'>" + data + "</div><br>";
                        }
                    }
                    content = content + "</div>"
                    $("#ap").append(content)
                } else {
                    content = "<div>"
                    for(i = 0; i < v_client.length; i++)
                    {
                        //value = sanitize(v_client[i].toUpperCase());
                        data = v_client[i];
                        if (data != "") {
                            content = content + "<input id='check_"+data+"' type='checkbox' onclick='checkBox(this)'> "
                            content = content + "<div class='divCLIENT' style='display: inline-block;' onclick='return false; openDialog(this.innerHTML)'>" + data + "</div><br>";
                        }
                    }
                    content = content + "</div>"
                    $("#client").append(content)
                }
            });
        }
    });

}