<link href="modules/servers/upCloudVm/templates/assets/css/layout.css" rel="stylesheet">
<link href="modules/servers/upCloudVm/templates/assets/css/theme.css" rel="stylesheet">
<link rel="stylesheet" src="modules/servers/upCloudVm/templates/assets/css/font-awesome.min.css">
<div id="custom-alert" style="display:none;position:fixed;top:0px; right: 0px; max-width: 800px;z-index:9999; 
border-radius:0px" class="alert alert-danger collapse alertUpCloud">
     <button type="button" id="close-custom-alert" class="close" style="margin-left:5px;" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
    <span id="custom-alert-content"></span>
  
</div>
<div id="custom-loader" class="loader" style='display:none; margin:-20px;'></div>
<div id="mg-wrapper" class="main-tab">
        <table class='table ' style=''>
                <caption>{$_LANG['overview']['details']}</caption>
        <tbody>
        <tr> <th>{$_LANG['overview']['hostname']}</th> <td>{$vm['details']['hostname']}</td> </tr>
        <tr> <th>{$_LANG['overview']['ip']}</th> <td>{$vm['details']['ip']}</td> </tr>
        <tr> <th>{$_LANG['overview']['uuid']}</th> <td>{$vm['details']['uuid']|upper}</td> </tr>
        <tr> <th>{$_LANG['overview']['template']}</th> <td>{$vm['details']['template']}</td> </tr>
        <tr> <th>{$_LANG['overview']['plan']}</th> <td>{$vm['details']['plan']}</td> </tr>
        <tr> <th>{$_LANG['overview']['status']}</th> <td>
        <span id="vmStatus" style="font-size: 12px;" class="label label-{if $vm['details']['status'] == 'started'}success{else if $vm['details']['status'] == 'stopped'}danger{else}warning{/if}">{$vm['details']['status']|upper}</span>
        <div id="custom-loader2" class="loader-small" style='display:none;margin-top:3px; margin-left:7px;'></div>
        </td> </tr>
        <tr> <th>Location</th> <td>{$vm['details']['location']}</td> </tr>
        </tbody>
        </table>

        <div class="well buttons-content">

                <div class="row">
                    <h4 class="text-center mb-20">{$_LANG.vps.control_panel}</h4>
                    <div class="col-lg-4 col-xs-6">
                        <a class="big-button upcloud-confirm" href="#" data-subaction="startServer" >
                            <div class="button-wrapper">
                                <i class="icon-btn icon-boot"></i>
                                <span>{$_LANG.vps.boot}</span>
                            </div>
                        </a>
                    </div>
        
                    <div class="col-lg-4 col-xs-6">
                            <a class="big-button upcloud-confirm" href="#" data-subaction="rebootServer" >
                                <div class="button-wrapper">
                                    <i class="icon-btn icon-reboot"></i>
                                    <span>{$_LANG.vps.reboot}</span>
                                </div>
                            </a>
                        </div>
                    <div class="col-lg-4 col-xs-6">
                            <a class="big-button upcloud-confirm" href="#" data-subaction="stopServer" >
                                <div class="button-wrapper">
                                    <i class="icon-btn icon-stop"></i>
                                    <span>{$_LANG.vps.stop}</span>
                                </div>
                            </a>
                        </div>
                </div>
        
                <div class="row">
                        <div class="col-lg-4 col-xs-6">
                                <a class="big-button upcloud-confirm" href="#" data-subaction="forceStopServer" >
                                    <div class="button-wrapper">
                                        <i class="icon-btn icon-shutdown"></i>
                                        <span>{$_LANG.vps.shutdown}</span>
                                    </div>
                                </a>
                            </div>
                        <div class="col-lg-4 col-xs-6">
                            <a {if $vm['details']['vnc'] != 'on' || $vm['details']['status'] != 'started'} style="pointer-events:none;opacity:0.5;"{/if} id="consoleButton" class="big-button" href="#" onclick="window.open( mainUrl +'&subaction=novnc', '',
                            'width=1024,height=768'); return false;
                            " >
                                <div class="button-wrapper">
                                    <i class="icon-btn icon-novnc"></i>
                                    <span>{$_LANG.vps.novnc}</span>
                                </div>
                            </a>
                        </div>
            
                        
                    </div>
            </div>

            <div class="well buttons-content">

                    <div class="row">
                        <h4 class="text-center mb-20">{$_LANG.vps.additional_tools}</h4>
                        <div class="col-lg-4 col-xs-6">
                            <a class="big-button" href="clientarea.php?action=productdetails&amp;id={$moduleParams['serviceid']}&amp;modop=custom&a=management&page=editConfiguration" >
                                <div class="button-wrapper">
                                    <i class="icon-btn icon-editvm"></i>
                                    <span>{$_LANG.server.title}</span>
                                </div>
                            </a>
                        </div>
            
                        <div class="col-lg-4 col-xs-6">
                                <a class="big-button" href="clientarea.php?action=productdetails&amp;id={$moduleParams['serviceid']}&amp;modop=custom&a=management&page=snapshotsManagement" >
                                    <div class="button-wrapper">
                                        <i class="icon-btn icon-backup"></i>
                                        <span>{$_LANG.backups.title}</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-xs-6">
                                    <a class="big-button" href="clientarea.php?action=productdetails&amp;id={$moduleParams['serviceid']}&amp;modop=custom&a=management&page=networkManagement" >
                                        <div class="button-wrapper">
                                            <i class="icon-btn icon-network"></i>
                                            <span>{$_LANG.network.title}</span>
                                        </div>
                                    </a>
                                </div>
                    </div>
            
                    <div class="row">
                            <div class="col-lg-4 col-xs-6">
                                <a class="big-button" href="clientarea.php?action=productdetails&amp;id={$moduleParams['serviceid']}&amp;modop=custom&a=management&page=firewallManagement" >
                                    <div class="button-wrapper">
                                        <i class="icon-btn icon-firewall"></i>
                                        <span>{$_LANG.firewall.title}</span>
                                    </div>
                                </a>
                            </div>
                
                            <div class="col-lg-4 col-xs-6">
                                    <a class="big-button" href="clientarea.php?action=productdetails&amp;id={$moduleParams['serviceid']}&amp;modop=custom&a=management&page=bandwidthGraphs" >
                                        <div class="button-wrapper">
                                            <i class="icon-btn icon-graphs"></i>
                                            <span>{$_LANG.bandwidth.title}</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-lg-4 col-xs-6">
                                        <a class="big-button" href="clientarea.php?action=productdetails&amp;id={$moduleParams['serviceid']}&amp;modop=custom&a=management&page=vncconsole" >
                                            <div class="button-wrapper">
                                                <i class="icon-btn icon-vnc"></i>
                                                <span>{$_LANG.vnc.title}</span>
                                            </div>
                                        </a>
                                    </div>
                        </div>
                </div>

                    <table class='table table-striped ' style='text-align: left;'>
                <caption>{$_LANG['ip']['addresses']}</caption>
        <thead>
        <th>{$_LANG['ip']['address']}</th><th>{$_LANG['ip']['access']}</th><th>{$_LANG['ip']['family']}</th>
        </thead>
        <tbody>
                        {if $vm['details']['ips'] != ''}
                        {foreach $vm['details']['ips'] as $ip}
                        <tr><td>{$ip['address']}</td><td>{$ip['access']|ucfirst}</td><td>{$ip['family']}</td></tr>
                        {/foreach}
                        {else}
                        <tr><td>{$_LANG['notFound']}</td><td>{$_LANG['notFound']}</td><td>{$_LANG['notFound']}</td></tr>
                        {/if}
               
        </tbody>
        </table>
</div>

<style>
.loader {
    position:fixed; 
    z-index:9999; 
    top:40px; 
    right: 50px; 
  display: inline-block;
  width: 50px;
  height: 50px;
  border: 3px solid rgba(255,255,255,.3);
  border-radius: 50%;
  border-top-color: rgb(0, 0, 0);
  animation: spin 1s ease-in-out infinite;
  -webkit-animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { -webkit-transform: rotate(360deg); }
}
@-webkit-keyframes spin {
  to { -webkit-transform: rotate(360deg); }
}
</style>
<script>
    var mainUrl = "clientarea.php?action=productdetails&id={$moduleParams['serviceid']}";
    var allowRefresh = true;

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
    try {
        var parsed = JSON.parse(data);
        $("#custom-alert-content").html(parsed.message);
        if(parsed.result == 'success')
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

function refreshVM()
{
    $("#custom-loader2").css('display','');
    $.post( "clientarea.php?action=productdetails&id={$moduleParams['serviceid']}", { subaction: 'refreshServer'})
  .done(function( data ) { 
    try {     
        var parsed = JSON.parse(data);

        if(parsed.data.details.status != '')
        {
            $("#vmStatus").removeClass().addClass('label');
            if(parsed.data.details.status == 'started')
            {
                if(parsed.data.details.vnc == 'on')
                {
                    $("#consoleButton").css('opacity','1.0').css('pointer-events','auto');
                }
                
                $("#vmStatus").addClass('label-success');
            }
            else if(parsed.data.details.status == 'stopped')
            {
                $("#vmStatus").addClass('label-danger');
                $("#consoleButton").css('pointer-events','none').css('opacity','0.5');
            }
            else
            {
                $("#vmStatus").addClass('label-warning');
                $("#consoleButton").css('pointer-events','none').css('opacity','0.5');
            }
            $("#vmStatus").html(parsed.data.details.statusLang);
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
</script>