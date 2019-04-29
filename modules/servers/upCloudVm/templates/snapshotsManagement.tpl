{include file='./header.tpl'}

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">{$_LANG.all.confirmDelete}</h4>
                </div>
                <div class="modal-body">
                    <p>{$_LANG.all.confirm1} <b><i class="title"></i></b> {$_LANG.all.confirm2}</p>
                    <p>{$_LANG.all.proceed}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$_LANG.all.cancel}</button>
                    <button type="button" class="btn btn-danger btn-ok">{$_LANG.all.delete}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-restore" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">{$_LANG.backups.confirmRestore}</h4>
                    </div>
                    <div class="modal-body">
                        <p>{$_LANG.backups.confirm1} <b><i class="title"></i></b> {$_LANG.backups.confirm2}</p>
                        <p>{$_LANG.all.proceed}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{$_LANG.all.cancel}</button>
                        <button type="button" class="btn btn-primary btn-ok">{$_LANG.backups.restore}</button>
                    </div>
                </div>
            </div>
        </div>

        
<div id="mg-wrapper" class="main-tab">
  <div class="module-header">
      <i class="icon-header icon-backup"></i>
      <h1>{$_LANG.backups.title}</h1>
      <p>{$_LANG.backups.description}</p>
  </div>
 
  <div class="well buttons-content">
    <h4 class="text-center mb-20">{$_LANG.backups.schedule}{if $vm['details']->time == ''}<span style="color:red;"> {$_LANG.backups.notset}</span>{/if}</h4>
    
    <div class="row">
        
        <div class="col-lg-4 col-xs-6">
            <label for="take" class="control-label" style="display:inline;float:none;" >{$_LANG.backups.take}</label>
    <select name="take" id="take" style="max-width: 400px;" class="form-control">
        <option value="daily" {if $vm['details']->interval == 'daily'} selected {/if}>{$_LANG.backups.daily}</option>
        <option value="mon" {if $vm['details']->interval == 'mon'} selected {/if}>{$_LANG.backups.onMondays}</option>
        <option value="tue" {if $vm['details']->interval == 'tue'} selected {/if}>{$_LANG.backups.onTuesdays}</option>
        <option value="wed" {if $vm['details']->interval == 'wed'} selected {/if}>{$_LANG.backups.onWednesdays}</option>
        <option value="thu" {if $vm['details']->interval == 'thu'} selected {/if}>{$_LANG.backups.onThursdays}</option>
        <option value="fri" {if $vm['details']->interval == 'fri'} selected {/if}>{$_LANG.backups.onFridays}</option>
        <option value="sat" {if $vm['details']->interval == 'sat'} selected {/if}>{$_LANG.backups.onSaturdays}</option>
        <option value="sun" {if $vm['details']->interval == 'sun'} selected {/if}>{$_LANG.backups.onSundays}</option>
    </select>
        </div>
        <div class="col-lg-4 col-xs-6">
            <label for="time" class="control-label" style="display:inline;float:none;" >{$_LANG.backups.time}</label>
            <select name="time" id="time" style="max-width: 400px;" class="form-control">
                {for $i=0 to 24}
                <option value="{if $i <=9}0{$i}00{else}{$i}00{/if}" {if $vm['details']->time == $i} selected {/if}>{if $i <=9}0{$i}:00{else}{$i}:00{/if}</option>
                {/for}
            </select>
        </div>
        <div class="col-lg-4 col-xs-6">
            <label for="retention" class="control-label" style="display:inline;float:none;" >{$_LANG.backups.delete}</label>
            <select name="retention" id="retention" style="max-width: 400px;" class="form-control">
                <option value="1" {if $vm['details']->retention == '1'} selected {/if}>{$_LANG.backups.oneDay}</option>
                <option value="2" {if $vm['details']->retention == '2'} selected {/if}>{$_LANG.backups.twoDays}</option>
                <option value="3" {if $vm['details']->retention == '3'} selected {/if}>{$_LANG.backups.threeDays}</option>
                <option value="4" {if $vm['details']->retention == '4'} selected {/if}>{$_LANG.backups.fourDays}</option>
                <option value="5" {if $vm['details']->retention == '5'} selected {/if}>{$_LANG.backups.fiveDays}</option>
                <option value="6" {if $vm['details']->retention == '6'} selected {/if}>{$_LANG.backups.sixDays}</option>
                <option value="7" {if $vm['details']->retention == '7'} selected {/if}>{$_LANG.backups.oneWeek}</option>
                <option value="14" {if $vm['details']->retention == '14'} selected {/if}>{$_LANG.backups.twoWeeks}</option>
                <option value="21" {if $vm['details']->retention == '21'} selected {/if}>{$_LANG.backups.threeWeeks}</option>
                <option value="30" {if $vm['details']->retention == '30'} selected {/if}>{$_LANG.backups.oneMonth}</option>
                <option value="60" {if $vm['details']->retention == '60'} selected {/if}>{$_LANG.backups.twoMonths}</option>
                <option value="90" {if $vm['details']->retention == '90'} selected {/if}>{$_LANG.backups.threeMonths}</option>
                <option value="120" {if $vm['details']->retention == '120'} selected {/if}>{$_LANG.backups.fourMonths}</option>
                <option value="150" {if $vm['details']->retention == '150'} selected {/if}>{$_LANG.backups.fiveMonths}</option>
                <option value="180" {if $vm['details']->retention == '180'} selected {/if}>{$_LANG.backups.sixMonths}</option>
                <option value="365" {if $vm['details']->retention == '365'} selected {/if}>{$_LANG.backups.oneYear}</option>
                <option value="710" {if $vm['details']->retention == '710'} selected {/if}>{$_LANG.backups.twoYears}</option>
                <option value="1095" {if $vm['details']->retention == '1095'} selected {/if}>{$_LANG.backups.threeYears}</option>
            </select>  
        </div>
    </div>
    <br>
    <button class="btn btn-primary" id="saveConfiguration"> {$_LANG.backups.save} </button>

</div>

  <table id="backups" class="table upCloudTable display nowrap" role="grid" style="width:100%;">
    <caption>{$_LANG.backups.list}</caption>
<thead>
  <tr >
    <th >#</th>
    <th >{$_LANG.backups.created}</th>
    <th >{$_LANG.backups.access}</th>
    <th >{$_LANG.backups.state}</th>
    <th >{$_LANG.backups.titl}</th>
    <th >{$_LANG.backups.size}</th>
     <th >{$_LANG.all.actions}</th>
  </tr>    
</thead>
<tbody> 
</tbody>
</table>
<button class="btn btn-success" id="addBackup"> {$_LANG.backups.add} </button>
<br><br>


<script>

                 $('#confirm-delete').on('click', '.btn-ok', function(e) {
            var $modalDiv = $(e.delegateTarget);
            var id = $(this).data('recordId');
            $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'deleteBackup', backup: id})
  .done(function( data ) { 
    parseResponse(data);
    $( '#backups').DataTable().ajax.reload().columns.adjust();
  });
    return false;

        });

        $('#confirm-delete').on('show.bs.modal', function(e) {
            var data = $(e.relatedTarget).data();
            $('.title', this).text(data.recordTitle);
            $('.btn-ok', this).data('recordId', data.recordId);
        });


                 $('#confirm-restore').on('click', '.btn-ok', function(e) {
            var $modalDiv = $(e.delegateTarget);
            var id = $(this).data('recordId');
            $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'restoreBackup', backup: id})
  .done(function( data ) { 
    parseResponse(data);
    $( '#backups').DataTable().ajax.reload().columns.adjust();
  });
    return false;

        });

        $('#confirm-restore').on('show.bs.modal', function(e) {
            var data = $(e.relatedTarget).data();
            $('.title', this).text(data.recordTitle);
            $('.btn-ok', this).data('recordId', data.recordId);
        });

$('#addBackup').on('click', function (event) {
    $( "#custom-alert" ).hide();
  $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'addBackup'})
  .done(function( data ) { 
    parseResponse(data);
    $( '#backups').DataTable().ajax.reload().columns.adjust();
  });
    return false;
})

$('#saveConfiguration').on('click', function (event) {
    $( "#custom-alert" ).hide();
  $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'updateStorageRule', time: $('#time').val(),
  retention: $('#retention').val(),interval: $('#take').val()})
  .done(function( data ) { 
    parseResponse(data);
    $( '#backups').DataTable().ajax.reload().columns.adjust();
  });
    return false;
})

$( document ).on( "click", ".restoreBackup", function() {
    $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'restoreBackup', backup: $(this).data("backup")})
  .done(function( data ) { 
    parseResponse(data);
    $( '#backups').DataTable().ajax.reload().columns.adjust();
  });
    return false;
});

$( document ).ready(function() {
    var trafficRules = $('#backups').DataTable({
"ajax": "clientarea.php?action=productdetails&id={$serviceId}&subaction=getBackups",
"info": false,
"columnDefs": [{ "orderable": false, "targets": 6 }],
"length": false,
scrollX: true,
        processing: false,
        searching: true,
        responsive: true,
    "autoWidth": false,
    "bAutoWidth": false
    }).columns.adjust();
});

</script>