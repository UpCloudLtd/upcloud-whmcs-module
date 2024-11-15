<link href="modules/servers/upCloudVps/templates/assets/css/layout.css" rel="stylesheet">
<link href="modules/servers/upCloudVps/templates/assets/css/theme.css" rel="stylesheet">
<div id="custom-alert" style="display:none;position:fixed;top:0px; right: 0px; max-width: 800px;z-index:9999;
border-radius:0px" class="alert alert-danger collapse alertUpCloud">
     <button type="button" id="close-custom-alert" class="close" style="margin-left:5px;" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
    <span id="custom-alert-content"></span>

</div>
<div id="custom-loader" class="loader" style='display:none; margin:-20px;'></div>
<div id="uc-wrapper" class="main-tab">
  <div class="module-header" style="text-align: justify;">
      <i class="icon-header icon-vps-details"></i>
      <h1>{$_LANG.basicdetails}</h1>
      <p>{$_LANG.basicdescription}</p>
  </div>
        <table class='table ' style=''>
                <caption>{$_LANG['overviewdetails']}</caption>
        <tbody>
        <tr> <th>{$_LANG['Hostname']}</th> <td id="vmhostname">{$vm['details']['hostname']}</td> </tr>
        <tr> <th>{$_LANG['IPAddress']}</th> <td>{$vm['details']['ip']}</td> </tr>
        <tr> <th>{$_LANG['username']}</th> <td>{$username}</td> </tr>
        <tr> <th>{$_LANG['password']}</th> <td class="col-sm-7 text-center" style="text-align: center;">
              <input type="password" id="password-field" value="{$password}" disabled>
              <i id="pass-status" class="fa fa-eye" aria-hidden="true" onClick="viewPassword()"></i>
          </td> </tr>
        <tr> <th>{$_LANG['OS']}</th> <td>{$vm['details']['template']}</td> </tr>
        <tr> <th>{$_LANG['Status']}</th> <td>
        <span id="vmStatus" style="font-size: 12px;" class="label label-{if $vm['details']['status'] == 'started'}success{else if $vm['details']['status'] == 'stopped'}danger{else}warning{/if}">{$vm['details']['status']|upper}</span>
        <div id="custom-loader2" class="loader-small" style='display:none;margin-top:3px; margin-left:7px;'></div>
        </td> </tr>
        <tr> <th>{$_LANG['Location']}</th> <td>{$vm['details']['location']}</td> </tr>
        </tbody>
        </table>

        <div class="well buttons-content">

                <div class="row">
                    <h4 class="text-center mb-20">{$_LANG.vps.control_panel}</h4>
                    <div class="col-lg-4 col-xs-6">
                        <a class="big-button upcloud-confirm" href="#" data-subaction="StartServer" >
                            <div class="button-wrapper">
                                <i class="icon-btn fa fa-power-off" style="font-size: 35px; color: green;"></i>
                                <span>{$_LANG.vps.boot}</span>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-4 col-xs-6">
                            <a class="big-button upcloud-confirm" href="#" data-subaction="RestartServer" >
                                <div class="button-wrapper">
                                    <i class="icon-btn fa fa-undo" style="font-size: 35px; color: orange;"></i>
                                    <span>{$_LANG.vps.reboot}</span>
                                </div>
                            </a>
                        </div>
                    <div class="col-lg-4 col-xs-6">
                            <a class="big-button upcloud-confirm" href="#" data-subaction="StopServer" >
                                <div class="button-wrapper">
                                    <i class="icon-btn fa fa-power-off" style="font-size: 35px; color: red;"></i>
                                    <span>{$_LANG.vps.stop}</span>
                                </div>
                            </a>
                        </div>
                </div>
            </div>

            <!-- Vnc -->
  <div class="well buttons-content">
    <div class="module-header">
  <div style="text-align: justify;">
    <i class="icon-header icon-vnc"></i>
    <h1 style="text-align: left; display: inline;">{$_LANG['vnc']['title']}</h1>
    <p style="text-align: left;">{$_LANG['vnc']['description']}</p>
  </div>

  <div style="float: right;">
    <label for="vnc_status" class="control-label" style="font-size: 12px;">
      {$_LANG['vnc']['status']}
    </label>
    <span id="vncStatus" class="label label-{if $vm['details']['vnc'] == 'on'}success{else}danger{/if}">
      {if $vm['details']['vnc'] == 'on'}
        {$_LANG['vnc']['on']}
      {else}
        {$_LANG['vnc']['off']}
      {/if}
    </span>
  </div>
</div>


  </br>

    <div class="input-group">
      <div class="custom-input-group" style="text-align: justify;">

        <label for="vnc_host" class="control-label">
          {$_LANG['vnc']['address']}
        </label>
        <input type="text" id="vnc_host" value="{$vm['details']['vnc_host']}" class="form-control" readonly style="max-width: 200px;">

        <label for="vnc_port" class="control-label">
          {$_LANG['vnc']['port']}
        </label>
        <input type="text" id="vnc_port" value="{$vm['details']['vnc_port']}" class="form-control" readonly style="max-width: 200px;">

        <label for="vnc_password" class="control-label">
          {$_LANG['vnc']['password']}
        </label>
        <input type="text" name="vnc_password" id="vnc_password" value="{$vm['details']['vnc_password']}" class="form-control" style="max-width: 200px;" maxlength="8">

      </div>

      <br>

      <button class="btn btn-primary" id="saveConfiguration">
        {$_LANG['vnc']['save']}
      </button>

      {if $vm['details']['vnc'] == 'on'}
        <button class="btn btn-danger" id="disableVNC"> {$_LANG['vnc']['disable']}</button>
        <button class="btn btn-success" id="enableVNC" style="display: none;">{$_LANG['vnc']['enable']}</button>
      {else}
        <button class="btn btn-success" id="enableVNC">{$_LANG['vnc']['enable']}</button>
        <button class="btn btn-danger" id="disableVNC" style="display: none;"> {$_LANG['vnc']['disable']}</button>
      {/if}
    </div>
  </div>

<!--Vnc-->

<!-- Network Information-->

<div class="modal fade" id="editPtrModal" tabindex="-1" role="dialog" aria-labelledby="editPtrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        <h4 class="modal-title" id="editPtrModalLabel">{$_LANG.ip.editRdn}<span id="ptrIp"></span></h4>
      </div>
      <div class="modal-body" style="text-align: justify;">
          <div id="ptrGroup" class="form-group">
            <label for="ptrRecord" class="col-form-label" style="font-weight: bold;">{$_LANG.ip.rdn}:</label>
            <input type="text" class="form-control" id="ptrRecord" value="" required>
          </div>
          <input type="hidden" id="ptrIpVal" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{$_LANG.ip.close}</button>
        <button type="button" id="saveChanges" class="btn btn-primary">{$_LANG.ip.save}</button>
      </div>
    </div>
  </div>
</div>

<div class="well buttons-content">
<div id="uc-wrapper" class="main-tab">
    <div class="module-header">
      <div style="text-align: justify;">
        <i class="icon-header icon-network"></i>
        <h1>{$_LANG.network.title}</h1>
        <p>{$_LANG.network.description}</p>
    </div>
      </div>

    <table id="ipAddresses" class="table upCloudTable display nowrap" role="grid" style="width:100%;">
            <caption>{$_LANG["IPAddresses"]}</caption>
        <thead >
          <tr >
            <th >{$_LANG['Family']}</th>
            <th >{$_LANG['IPAddress']}</th>
            <th >{$_LANG['reversePTR']}</th>
            <th >{$_LANG['Action']}</th>
          </tr>
        </thead>
        <tbody>
    </tbody>
    </table>
</div>
</div>

<!-- Network Information-->

<!--Configuration Change -->
<div class="well buttons-content">
<div id="uc-wrapper" class="main-tab">
    <div class="module-header">
      <div style="text-align: justify;">
        <i class="icon-header icon-editvm"></i>
        <h1>{$_LANG.server.title}</h1>
        <p>{$_LANG.server.description}</p>
    </div>
    </div>

    <div style="margin-top:20px; text-align: justify;">

    <label for="hostname" class="control-label" style="display:inline;float:none;" >{$_LANG.server.hostname}</label>
    <input type="text" name="hostname" id="hostname" value="{$vm['details']['hostname']}" class="form-control" style="max-width: 400px;"/>

    <label for="displayAdapter" class="control-label" style="display:inline;float:none;" >{$_LANG.server.display}</label>
    <select name="displayAdapter" id="displayAdapter" style="max-width: 400px;" class="form-control">
        <option value="vga" {if $vm['details']['video_model'] == 'vga'} selected {/if}>Standard VGA card with VESA 2.0 VBE extensions</option>
        <option value="cirrus" {if $vm['details']['video_model'] == 'cirrus'} selected {/if}>Cirrus Logic GD5446</option>

    </select>

    <label for="networkAdapter" class="control-label" style="display:inline;float:none;" >{$_LANG.server.network}</label>
    <select name="networkAdapter" id="networkAdapter" style="max-width: 400px;" class="form-control">
        <option value="e1000" {if $vm['details']['nic_model'] == 'e1000'} selected {/if}>Intel E1000 emulation</option>
        <option value="virtio" {if $vm['details']['nic_model'] == 'virtio'} selected {/if}>VirtIO</option>
        <option value="rtl8139" {if $vm['details']['nic_model'] == 'rtl8139'} selected {/if}>RealTek RTL8139 emulation</option>
    </select>

    <label for="timezone" class="control-label" style="display:inline;float:none;" >{$_LANG.server.timezone}</label>
    <select name="timezone" id="timezone" style="max-width: 400px;" class="form-control">
        {foreach $vm['timezones'] as $timezone}
        <option value="{$timezone}" {if $vm['details']['timezone'] == $timezone} selected {/if}>{$timezone}</option>
        {/foreach}
    </select>

    <label for="bootOrder" class="control-label" style="display:inline;float:none;" >{$_LANG.server.boot}</label>
    <select name="bootOrder" id="bootOrder" style="max-width: 400px;" class="form-control">
        <option value="disk" {if $vm['details']['boot_order'] == 'disk'} selected {/if}>Disk</option>
        <option value="cdrom" {if $vm['details']['boot_order'] == 'cdrom'} selected {/if}>Cdrom</option>
        <option value="disk,cdrom" {if $vm['details']['boot_order'] == 'disk,cdrom'} selected {/if}>Disk,Cdrom </option>
        <option value="cdrom,disk" {if $vm['details']['boot_order'] == 'cdrom,disk'} selected {/if}>Cdrom,Disk</option>
    </select>
    <br>
    <button class="btn btn-primary" id="savevmConfiguration"> {$_LANG.server.save} </button>
</div>
    </div>
</div>
<!--Configuration Change -->

<!--bandwidth -->
<div class="well buttons-content">
<div id="uc-wrapper" class="main-tab">
        <div class="module-header">
          <div style="text-align: justify;">
            <i class="icon-header icon-graphs"></i>
            <h1>{$_LANG.bandwidth.title}</h1>
            <p>{$_LANG.bandwidth.description}</p>
        </div>
        </div>
</div>
<script src="modules/servers/upCloudVps/templates/assets/js/Chart.js"></script>
<select name="graphTime" id="graphTime" class="form-control" style=" width:200px; float:right;">
<option value="24 Hours">24 Hours</option>
<option value="Week">Week</option>
<option value="Month">Month</option>
<option value="Year">Year</option>
</select>
<canvas id="charts"></canvas>
</div>

<!--bandwidth -->
</div>


<script language="JavaScript" type="text/javascript">
{literal}
//Power toggle
function viewPassword()
{
var passwordInput = document.getElementById('password-field');
var passStatus = document.getElementById('pass-status');

if (passwordInput.type == 'password'){
passwordInput.type='text';
passStatus.className='fa fa-eye-slash';
passwordInput.disabled = true;
}
else{
passwordInput.type='password';
passStatus.className='fa fa-eye';
passwordInput.disabled = true;
}
}
{/literal}

    var mainUrl = "clientarea.php?action=productdetails&id={$moduleParams['serviceid']}";
    var allowRefresh = true;

// Power On/ Off/ Reboot of VM
$(".upcloud-confirm").on('click', function(event){
    allowRefresh = false;
    $(".upcloud-confirm").css('pointer-events','none').css('opacity','0.5');
    $("#custom-alert").hide();
    $("#custom-loader").show();
$.post( "clientarea.php?action=productdetails&id={$moduleParams['serviceid']}", { subaction: $(this).data('subaction')})
  .done(function( data ) {
        $("#custom-loader").hide();
        $(".upcloud-confirm").css('opacity','1.0').css('pointer-events','auto');
        allowRefresh = true;
        parseResponse(data);
    setTimeout(function(){
        refreshVM();
}, 2000);

  });

return false;
});
$( document ).ready(function() {
    window.setInterval(function(){
    if(allowRefresh == true)
    {
        refreshVM();
    }
}, 15000);
});

//VM Status refreshment
function refreshVM()
{
    $("#custom-loader2").css('display','');
    $.post( "clientarea.php?action=productdetails&id={$moduleParams['serviceid']}", { subaction: 'refreshServer'})
  .done(function( data ) {
    try {
      //console.log(data);
        var parsed = JSON.parse(data);

        if(parsed.data.details.status != '')
        {
            $("#vmStatus").removeClass().addClass('label');
            if(parsed.data.details.status == 'started')
            {
                $("#vmStatus").addClass('label-success');
            }
            else if(parsed.data.details.status == 'stopped')
            {
                $("#vmStatus").addClass('label-danger');
            }
            else
            {
                $("#vmStatus").addClass('label-warning');
            }
            $("#vmStatus").html(parsed.data.details.statusLang.toUpperCase());
        }
        $("#custom-loader2").hide();
    } catch(e) {
        $("#custom-loader2").hide();
        return;
    }
  });
}

$( "#close-custom-alert" ).click(function() {
    $( "#custom-alert" ).hide();
});


//VNC Information
$('#saveConfiguration').on('click', function (event) {
    $( ".alert" ).hide();
      $("#custom-loader").show();
    var data = {};
    data['subaction'] = 'saveVNCConfiguration';
    data['vnc_password'] = $('#vnc_password').val();
  $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
  .done(function( data ) {
      $("#custom-loader").hide();
    parseResponse(data);
  });
    return false;
})
$('#enableVNC').on('click', function (event) {
    $("#custom-loader").show();
    $( ".alert" ).hide();
    var data = {};
    data['subaction'] = 'changeVNCStatus';
    data['vnc'] = 'on';
  $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
  .done(function( data ) {
    parseResponse(data);
    var parsed = jQuery.parseJSON(data);
    if(parsed.result != 'failure')
    {
        $('#vncStatus').removeClass().addClass('label label-success').html("{$_LANG['vnc']['on']}");
        $('#disableVNC').show();
        $('#enableVNC').hide();
    }
      $("#custom-loader").hide();
    updateDetails();
  });
    return false;
})
$('#disableVNC').on('click', function (event) {
    $( ".alert" ).hide();
      $("#custom-loader").show();
    var data = {};
    data['subaction'] = 'changeVNCStatus';
    data['vnc'] = 'off';
  $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
  .done(function( data ) {
    parseResponse(data);
    var parsed = jQuery.parseJSON(data);
    if(parsed.result != 'failure')
    {
        $('#vncStatus').removeClass().addClass('label label-danger').html("{$_LANG['vnc']['off']}");
        $('#disableVNC').hide();
        $('#enableVNC').show();
    }
      $("#custom-loader").hide();
    updateDetails();
  });
    return false;
})
function updateDetails()
{
var data = {};
    data['subaction'] = 'vncDetails';
$.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
  .done(function( data ) {
    try
    {
        var parsed = jQuery.parseJSON(data);
        //console.log(parsed);
        if(parsed.vncport)
        {
            $('#vnc_port').val(parsed.vncport).show();
            $('#vnc_host').val(parsed.vnchost).show();
        }
    } catch(e) {
return;
    }
  });
}

function parseResponse(data)
{
  if(data)
  {
    try {
    //  console.log(data);
        var parsed = JSON.parse(data);
        $("#custom-alert-content").html(parsed.message);
        if(parsed.result != 'failure')
        {
            $("#custom-alert").removeClass().addClass('alert alert-success collapse');
        }
        else
        {
            $("#custom-alert").removeClass().addClass('alert alert-danger collapse');
        }

        $("#custom-alert").show();

    } catch(e) {
        $("#custom-alert-content").html('Can not parse response');
        $("#custom-alert").removeClass().addClass('alert alert-danger collapse');
    }
 }
}

//IP Address
$( document ).ready(function() {
    $("#ipAddresses").DataTable({
  destroy: true,
  "ajax": {
            "url": "clientarea.php?action=productdetails&id={$serviceid}",
            "type": "POST",
            data: { subaction:"getIpAddresses" },
        },
  "info": false,
  "columnDefs": [{ "orderable": false, "targets": 3 }],
  "length": false,
  processing: true,
  searching: true,
  scrollX: false,
  autoWidth: false
});

 });

 $('#editPtrModal').on('show.bs.modal', function (event) {
     $('#ptrGroup').removeClass('has-error');
   var button = $(event.relatedTarget)
   $('#ptrIp').html(button.data('ip'));
   $('#ptrIpVal').val(button.data('ip'));
   $('#ptrRecord').val(button.data('ptr'));
 })

 $('#saveChanges').on('click', function (event) {
     if($('#ptrRecord').val() == '')
     {
         $('#ptrGroup').addClass('has-error');
         return;
     }
       $("#custom-loader").show();
     $('#ptrGroup').removeClass('has-error');
   $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'editIp', ip: $('#ptrIpVal').val(),ptr: $('#ptrRecord').val()})
   .done(function( data ) {
       $('.modal').modal('hide');
         $("#custom-loader").hide();
     parseResponse(data);
     $( '#ipAddresses').DataTable().ajax.reload().columns.adjust();
   });
     return false;
 })

 //Server Configuration
 $('#savevmConfiguration').on('click', function (event) {
     $( ".alert" ).hide();
     var data = {};
     data['subaction'] = 'saveServerConfiguration';
     data['hostname'] = $('#hostname').val();
     data['displayAdapter'] = $('#displayAdapter').val();
     data['networkAdapter'] = $('#networkAdapter').val();
     data['timezone'] = $('#timezone').val();
     data['bootOrder'] = $('#bootOrder').val();
   $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
   .done(function( data ) {
     $("#vmhostname").html($('#hostname').val()).show();
    // console.log(data);
     parseResponse(data);
   });
     return false;
 })

 //Graph
$.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'getBandwidth', time: "24 Hours"})
  .done(function( data ) {
  //  console.log(data);
  if(data)
  {
    try {
    var parsed = jQuery.parseJSON(data);
    var ctx = document.getElementById('charts').getContext('2d');

    var combinedChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: parsed.data.labels,
            datasets: [{
                label: "IPv4 in MB",
                borderColor: 'rgb(255, 99, 132)',
                data: parsed.data.IPv4,
            }, {
                label: "IPv6 in MB",
                borderColor: 'rgb(55, 199, 132)',
                data: parsed.data.IPv6,
            }]
        }
    });
} catch (e) {
    // Handle parsing error
}
 }

  });

  $('#graphTime').on('change', function (event) {
      $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'getBandwidth', time: $('#graphTime').val()})
    .done(function( data ) {
      //console.log(data);
      if(data)
    {
      try {
    var parsed = jQuery.parseJSON(data);
    var ctx = document.getElementById('charts').getContext('2d');

    var combinedChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: parsed.data.labels,
            datasets: [{
                label: "IPv4 in MB",
                borderColor: 'rgb(255, 99, 132)',
                data: parsed.data.IPv4,
            }, {
                label: "IPv6 in MB",
                borderColor: 'rgb(55, 199, 132)',
                data: parsed.data.IPv6,
            }]
        }
    });
} catch (e) {
    // Handle parsing error
}
   }
    });
      return false;
  })

</script>
