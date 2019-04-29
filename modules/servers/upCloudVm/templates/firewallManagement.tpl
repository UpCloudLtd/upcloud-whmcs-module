{include file='./header.tpl'}

<div class="alert alert-warning"><strong>{$_LANG['firewall']['dns_message_1']}</strong>{$_LANG['firewall']['dns_message_2']}</div>
<div id="mg-wrapper" class="module-container">
    <div class="module-header">
        <i class="icon-header icon-firewall"></i>
        <h1>{$_LANG.firewall.title}</h1>
        <p>{$_LANG.firewall.description}</p>
    </div>
   
    <table id="trafficRules" class="table upCloudTable display nowrap" role="grid" style="width:100%;">
        <caption>{$_LANG['firewall']['rules']}</caption>
    <thead >
      <tr >
          <th>#</th>
        <th style="width:20px;">{$_LANG['firewall']['direction']}</th>
        <th style="width:100px;">{$_LANG['firewall']['protocol']}</th>
        <th style="width:100px;">{$_LANG['firewall']['source_address']}</th>
        <th style="width:100px;">{$_LANG['firewall']['source_port']}</th>
        <th style="width:100px;">{$_LANG['firewall']['target_address']}</th>
        <th style="width:100px;">{$_LANG['firewall']['target_port']}</th>
         <th style="width:100px;"">{$_LANG['firewall']['action']}</th>
         <th style="width:100px;" >{$_LANG['firewall']['comment']}</th>
         <th >{$_LANG.all.actions}</th>
      </tr>    
    </thead>
    <tbody> 
</tbody>
</table>
<button class="btn btn-success" data-toggle="modal" data-target="#pm-add-rule" id="so_button_add_rule"> {$_LANG.firewall.add} </button>
<button class="btn btn-success" id="autoAdd"> {$_LANG.firewall.add_dns} </button>


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

   <!-- Modal add/edit rule  -->
   <div class="modal fade " id="pm-add-rule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <form method="post">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                    <h4 id="modal_header">{$_LANG.firewall.add_rule}</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-alerts">
                         <div class="alertContainer">
                            <div class="alertPrototype" style="display:none;">
                                <div class="alert alert-danger" role="alert">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                                    <p></p>
                                    <a style="display:none;" class="errorID" href=""></a>
                                </div>
                            </div>
                            <div class="alertPrototype" style="display:none;">
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                     </div>
                    <table class="table table-bordered table-striped" style="width:100%; margin-top:10px;">
                        <tr>
                            <td style="width:25%;"> 
                                <label for="firewall_direction" class="control-label" style="display:inline;float:none;">{$_LANG.firewall.direction}: </label>
                            </td>
                            <td>
                                <select name="firewall[direction]" id="firewall_direction" style="width: 220px; margin-bottom: -1px;" class="form-control">     
                                <option value="in">In</option>
                                <option value="out">Out</option>
                                </select>
                            </td>
                            <td style="width:25%;"> 
                                    <label for="firewall_action" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.action}:  </label>
                                </td>
                                <td>
                                    <select name="firewall[action]" id="firewall_action" style="width: 220px; margin-bottom: -1px;" class="form-control">
                                            <option value="accept">Accept</option>
                                            <option value="drop">Drop</option>
                                    </select>
                                </td>
                        </tr>
                        <tr>
                            <td style="width:25%;"> 
                                <label for="family" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.family}:  </label>
                            </td>
                            <td>
                                <select name="firewall[family]" id="firewall_family" style="width: 220px; margin-bottom: -1px;" class="form-control">
                                        <option value="IPv4">IPv4</option>
                                        <option value="IPv6">IPv6</option>
                                </select>
                            </td>

                            <td style="width:25%;"> 
                                <label for="firewall_protocol" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.protocol}:  </label>
                            </td>
                            <td>
                                <select name="firewall[protocol]" id="firewall_protocol" style="max-width: 220px;" class="form-control">
                                    <option value="all">ALL</option>
                                    <option value="tcp">TCP</option>
                                    <option value="udp">UDP</option>
                                    <option value="icmp">ICMP</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr id="icmp_row" style="display:none;">
                            <td style="width:25%;"> 
                            <label for="firewall_icmp_type" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.icmp_type}:  </label>
                        </td>
                        <td>
                            <input type="text" name="firewall[icmp_type]" id="firewall_icmp_type" value="" class="form-control"/>
                        </td>

                    </tr>

                        <tr>
                                <td style="width:25%;"> 
                                <label for="destination_address_start" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.destination_address_start}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[destination_address_start]" id="firewall_destination_address_start" value="" class="form-control"/>
                            </td>

                            <td style="width:25%;"> 
                                <label for="firewall_destination_address_end" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.destination_address_end}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[destination_address_end]" id="firewall_destination_address_end" value="" class="form-control"/>
                            </td>
                        </tr>

                        <tr>
                                <td style="width:25%;"> 
                                <label for="firewall_source_address_start" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.source_address_start}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[source_address_start]" id="firewall_source_address_start" value="" class="form-control"/>
                            </td>

                            <td style="width:25%;"> 
                                <label for="firewall_source_address_end" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.source_address_end}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[source_address_end]" id="firewall_source_address_end" value="" class="form-control"/>
                            </td>
                        </tr>
                        <tr>
                                <td style="width:25%;"> 
                                <label for="firewall_destination_port_start" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.destination_port_start}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[destination_port_start]" id="firewall_destination_port_start" value="" class="form-control"/>
                            </td>

                            <td style="width:25%;"> 
                                <label for="firewall_destination_port_end" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.destination_port_end}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[destination_port_end]" id="firewall_destination_port_end" value="" class="form-control"/>
                            </td>
                        </tr>

                        <tr>
                                <td style="width:25%;"> 
                                <label for="firewall_source_port_start" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.source_port_start}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[source_port_start]" id="firewall_source_port_start" value="" class="form-control"/>
                            </td>

                            <td style="width:25%;"> 
                                <label for="firewall_source_port_end" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.source_port_end}:  </label>
                            </td>
                            <td>
                                <input type="text" name="firewall[source_port_end]" id="firewall_source_port_end" value="" class="form-control"/>
                            </td>
                        </tr>

                        <tr>
                            <td style="width:25%;"> 
                                <label for="firewall_comment" class="control-label" style="display:inline;float:none;" >{$_LANG.firewall.comment}:  </label>
                            </td>
                            <td colspan="3">
                                <input type="text" name="firewall[comment]" id="firewall_comment" value="" style="width:100%; margin-bottom: -1px;" class="form-control"/>
                            </td>
                        </tr>
                        
                    </table>
                    <div class="modal-loader" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="subaction" value="createRule" id="pm-modal-rule-subaction" />
                    <input type="button" class="btn btn-primary mg-save" id="modal_button_submit"  value="{$_LANG.firewall.add}" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">{$_LANG.firewall.close}</button>
                </div>
            </div>
        </div>
    </form>
</div>

    <style>

    </style>
    <script>

             $('#confirm-delete').on('click', '.btn-ok', function(e) {
            var $modalDiv = $(e.delegateTarget);
            var id = $(this).data('recordId');
            $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'deleteRule', position: id})
        .done(function( data ) { 
            parseResponse(data);
            $( '#trafficRules').DataTable().ajax.reload().columns.adjust();
        });
            return false;

        });

        $('#confirm-delete').on('show.bs.modal', function(e) {
            var data = $(e.relatedTarget).data();
            $('.title', this).text(data.recordId);
            $('.btn-ok', this).data('recordId', data.recordId);
        });

$('#modal_button_submit').on('click', function (event) {

    var data = {};
    
    $('select[name^="firewall"]').each(function() {
        data[$(this).attr('id')] = $(this).val();
});

    $('input[name^="firewall"]').each(function() {
        data[$(this).attr('id')] = $(this).val();
});
    
data['subaction'] = 'addRule';

$.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
  .done(function( data ) { 
    parseResponse(data);
    $( '#trafficRules').DataTable().ajax.reload().columns.adjust();
  });
    return false;
})

$('#autoAdd').on('click', function (event) {
    $( "#custom-alert" ).hide();
var data = {};
data['subaction'] = 'addDNSRules';
$.post( "clientarea.php?action=productdetails&id={$serviceid}", data)
.done(function( data ) { 
parseResponse(data);
$( '#trafficRules').DataTable().ajax.reload().columns.adjust();
});
return false;
})

$( document ).ready(function() {
    var trafficRules = $('#trafficRules').DataTable({
       
"ajax": "clientarea.php?action=productdetails&id={$serviceId}&subaction=getTrafficRules",
"info": false,
"length": false,
        processing: false,
        searching: true,
        scrollX: true,
        "columnDefs": [{ "orderable": false, "targets": 9 }]
    });
    
});

$( "#firewall_protocol" ).change(function() {
    if($( "#firewall_protocol" ).val() == 'icmp')
    {
        $( "#icmp_row" ).show();
    }
    else
    {
        $( "#icmp_row" ).hide();
    }
});

</script>
<style>
</style>