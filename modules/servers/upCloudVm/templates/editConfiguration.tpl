{include file='./header.tpl'}

<div id="mg-wrapper" class="main-tab">
    <div class="module-header">
        <i class="icon-header icon-editvm"></i>
        <h1>{$_LANG.server.title}</h1>
        <p>{$_LANG.server.description}</p>
    </div>
   
    <div class="well buttons-content" style="margin-top:20px;">
     <h4 class="text-center mb-20">{$_LANG.server.edit}</h4>


    <label for="hostname" class="control-label" style="display:inline;float:none;" >{$_LANG.server.hostname}</label>
    <input type="text" name="hostname" id="hostname" value="{$vm['details']->hostname}" class="form-control" style="max-width: 400px;"/>

    <label for="displayAdapter" class="control-label" style="display:inline;float:none;" >{$_LANG.server.display}</label>
    <select name="displayAdapter" id="displayAdapter" style="max-width: 400px;" class="form-control">
        <option value="vga" {if $vm['details']->video_model == 'vga'} selected {/if}>Standard VGA card with VESA 2.0 VBE extensions</option>
        <option value="cirrus" {if $vm['details']->video_model == 'cirrus'} selected {/if}>Cirrus Logic GD5446</option>

    </select>

    <label for="networkAdapter" class="control-label" style="display:inline;float:none;" >{$_LANG.server.network}</label>
    <select name="networkAdapter" id="networkAdapter" style="max-width: 400px;" class="form-control">
        <option value="e1000" {if $vm['details']->nic_model == 'e1000'} selected {/if}>Intel E1000 emulation</option>
        <option value="virtio" {if $vm['details']->nic_model == 'virtio'} selected {/if}>VirtIO</option>
        <option value="rtl8139" {if $vm['details']->nic_model == 'rtl8139'} selected {/if}>RealTek RTL8139 emulation</option>
    </select>

    <label for="timezone" class="control-label" style="display:inline;float:none;" >{$_LANG.server.timezone}</label>
    <select name="timezone" id="timezone" style="max-width: 400px;" class="form-control">
        {foreach $vm['timezones'] as $timezone}
        <option value="{$timezone}" {if $vm['details']->timezone == $timezone} selected {/if}>{$timezone}</option>
        {/foreach}
    </select>

    <label for="bootOrder" class="control-label" style="display:inline;float:none;" >{$_LANG.server.boot}</label>
    <select name="bootOrder" id="bootOrder" style="max-width: 400px;" class="form-control">
        <option value="disk" {if $vm['details']->boot_order == 'disk'} selected {/if}>Disk</option>
        <option value="cdrom" {if $vm['details']->boot_order == 'cdrom'} selected {/if}>Cdrom</option>
        <option value="disk,cdrom" {if $vm['details']->boot_order == 'disk,cdrom'} selected {/if}>Disk,Cdrom </option>
        <option value="cdrom,disk" {if $vm['details']->boot_order == 'cdrom,disk'} selected {/if}>Cdrom,Disk</option>
    </select>
    <br>
    <button class="btn btn-primary" id="saveConfiguration"> {$_LANG.server.save} </button>

    </div>
</div>

<script>
$('#saveConfiguration').on('click', function (event) {
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
    parseResponse(data);
  });
    return false;
})

</script>