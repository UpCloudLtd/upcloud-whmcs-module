{include file='./header.tpl'}

<div id="mg-wrapper" class="main-tab">
    <div class="module-header">
        <i class="icon-header icon-vnc"></i>
        <h1>{$_LANG['vnc']['title']}</h1>
        <p>{$_LANG['vnc']['description']}</p>
    </div>
    <div class="well buttons-content" style="">
            <h4 class="text-center mb-20">{$_LANG['vnc']['title']}</h4> 
            <div style="float:right;">
                <label for="vnc_status" class="control-label" style="display:inline;float:none;" >{$_LANG['vnc']['status']}</label>
                <span id="vmStatus" style="font-size: 12px;" class="label label-{if $vm['details']->remote_access_enabled == 'yes'}success{else}danger{/if}">{if $vm['details']->remote_access_enabled == 'yes'}{$_LANG['vnc']['on']}{else}{$_LANG['vnc']['off']}{/if}</span>     
            </div>
            <br>
            <label for="vnc_password" class="control-label" style="display:inline;float:none;" >{$_LANG['vnc']['password']}</label>
            <input type="text" name="vnc_password" id="vnc_password" value="{$vm['details']->remote_access_password}" class="form-control" style="max-width: 400px;"/>
            <label for="vnc_host" class="control-label" style="display:inline;float:none;" >{$_LANG['vnc']['address']}</label>
            <input type="text" id="vnc_host" value="{$vm['details']->remote_access_host}" class="form-control" readonly style="max-width: 400px;"/>
            <label for="vnc_port" class="control-label" style="display:inline;float:none;" >{$_LANG['vnc']['port']}</label>
            <input type="text" id="vnc_port" value="{$vm['details']->remote_access_port}" class="form-control" readonly style="max-width: 400px;"/>
            <br>
            <button class="btn btn-primary" id="saveConfiguration"> {$_LANG['vnc']['save']}</button>
            {if $vm['details']->remote_access_enabled == 'yes'}
            <button class="btn btn-danger" id="disableVNC" style="float:right;"> {$_LANG['vnc']['disable']}</button>     
            <button class="btn btn-success" id="enableVNC" style="float:right;display:none;">{$_LANG['vnc']['enable']}</button> 
            {else}
            <button class="btn btn-success" id="enableVNC" style="float:right;">{$_LANG['vnc']['enable']}</button>
            <button class="btn btn-danger" id="disableVNC" style="float:right;display:none;"> {$_LANG['vnc']['disable']}</button>  
            {/if}
    </div>
</div>
<script>
    $('#saveConfiguration').on('click', function (event) {
        $( ".alert" ).hide();
        var data = {};
        data['subaction'] = 'saveVNCConfiguration';
        data['vnc_password'] = $('#vnc_password').val();
      $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
      .done(function( data ) { 
        parseResponse(data);
      });
        return false;
    })  
    $('#enableVNC').on('click', function (event) {
        $( ".alert" ).hide();
        var data = {};
        data['subaction'] = 'changeVNCStatus';
        data['vnc'] = 'on';
      $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
      .done(function( data ) { 
        parseResponse(data);
var parsed = jQuery.parseJSON(data);
        if(parsed.result == 'success')
        {
            $('#vmStatus').removeClass().addClass('label label-success').html("{$_LANG['vnc']['on']}");
            $('#disableVNC').show();
            $('#enableVNC').hide();
        }
        updateDetails();
      });
        return false;
    })
    $('#disableVNC').on('click', function (event) {
        $( ".alert" ).hide();
        var data = {};
        data['subaction'] = 'changeVNCStatus';
        data['vnc'] = 'off';
      $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
      .done(function( data ) { 
        parseResponse(data);

            var parsed = jQuery.parseJSON(data);
            if(parsed.result == 'success')
        {
            $('#vmStatus').removeClass().addClass('label label-danger').html("{$_LANG['vnc']['off']}");
            $('#disableVNC').hide();
            $('#enableVNC').show();
        }
        updateDetails();
      });
        return false;
    })
function updateDetails()
{
    var data = {};
        data['subaction'] = 'getServerDetails';
    $.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
      .done(function( data ) { 
        try 
        {
            var parsed = jQuery.parseJSON(data);
            if(parsed.result == 'success')
            {
                $('#vnc_port').val(parsed.data.server.remote_access_port);
                $('#vnc_host').val(parsed.data.server.remote_access_host);
            }
        } catch(e) {
    return;
        }
      });
}
    </script>