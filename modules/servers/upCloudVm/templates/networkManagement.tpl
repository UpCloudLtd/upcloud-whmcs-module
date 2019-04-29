{include file='./header.tpl'}

<div class="modal fade" id="editPtrModal" tabindex="-1" role="dialog" aria-labelledby="editPtrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        <h4 class="modal-title" id="editPtrModalLabel">{$_LANG.ip.editRdn}<span id="ptrIp"></span></h4>      
      </div>
      <div class="modal-body">
          <div id="ptrGroup" class="form-group">
            <label for="ptrRecord" class="col-form-label">{$_LANG.ip.rdn}:</label>
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

<div id="mg-wrapper" class="main-tab">
    <div class="module-header">
        <i class="icon-header icon-network"></i>
        <h1>{$_LANG.network.title}</h1>
        <p>{$_LANG.network.description}</p>
    </div>
   
    <table id="ipAddresses" class="table upCloudTable display nowrap" role="grid" style="width:100%;">
            <caption>{$_LANG.ip.addresses}</caption>
        <thead >
          <tr >
            <th >{$_LANG.ip.category}</th>
            <th >{$_LANG.ip.address}</th>
            <th >{$_LANG.ip.rdn}</th>
            <th >{$_LANG.all.actions}</th>
          </tr>    
        </thead>
        <tbody> 
    </tbody>
    </table>
    
    <button class="btn btn-success" id="add4"> {$_LANG.ip.addIPv4} </button>
    <button class="btn btn-success" id="add6"> {$_LANG.ip.addIPv6} </button>
    <button class="btn btn-success" id="addP"> {$_LANG.ip.addPrivate} </button>
    

</div>
<hr>
<script>
        $( document ).ready(function() {
            var ipAddresses = $('#ipAddresses').DataTable({
        "ajax": "clientarea.php?action=productdetails&id={$serviceId}&subaction=getIpAddresses",
        "info": false,
        "columnDefs": [{ "orderable": false, "targets": 3 }],
        "length": false,
        scrollX: true,
                processing: true,
                searching: true,
                autoWidth: false
            });
});

$( document ).on( "click", "#add4", function() {
    $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'addIp',family: 'IPv4'})
  .done(function( data ) { 
    parseResponse(data);
    $( '#ipAddresses').DataTable().ajax.reload().columns.adjust();
  });
    return false;
});

$( document ).on( "click", "#add6", function() {
    $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'addIp', family: 'IPv6'})
  .done(function( data ) { 
    parseResponse(data);
    $( '#ipAddresses').DataTable().ajax.reload().columns.adjust();
  });
    return false;
});

$( document ).on( "click", "#addP", function() {
    $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'addIp',family: 'Private'})
  .done(function( data ) { 
    parseResponse(data);
    $( '#ipAddresses').DataTable().ajax.reload().columns.adjust();
  });
    return false;
});

$('#editPtrModal').on('show.bs.modal', function (event) {
    $('#ptrGroup').removeClass('has-error');
  var button = $(event.relatedTarget)
  $('#ptrIp').html(button.data('ip'));
  $('#ptrIpVal').val(button.data('ip'));
  $('#ptrRecord').val(button.data('ptr'));
})

     $('#confirm-delete').on('click', '.btn-ok', function(e) {
            var $modalDiv = $(e.delegateTarget);
            var id = $(this).data('recordId');
            $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'deleteIp', ip: id})
            .done(function( data ) { 
                parseResponse(data);
                $( '#ipAddresses').DataTable().ajax.reload().columns.adjust();
            });
                return false;
        });

        $('#confirm-delete').on('show.bs.modal', function(e) {
            var data = $(e.relatedTarget).data();
            $('.title', this).text(data.recordId);
            $('.btn-ok', this).data('recordId', data.recordId);
        });

$('#saveChanges').on('click', function (event) {
    if($('#ptrRecord').val() == '')
    {
        $('#ptrGroup').addClass('has-error');
        return;
    }
    $('#ptrGroup').removeClass('has-error');
  $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'editIp', ip: $('#ptrIpVal').val(),ptr: $('#ptrRecord').val()})
  .done(function( data ) { 
    parseResponse(data);
    $( '#ipAddresses').DataTable().ajax.reload().columns.adjust();
  });
    return false;
})

</script>