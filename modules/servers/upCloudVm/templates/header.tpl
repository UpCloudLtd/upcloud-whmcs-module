
<link href="modules/servers/upCloudVm/templates/assets/css/theme.css" rel="stylesheet">
<link href="modules/servers/upCloudVm/templates/assets/css/layout.css" rel="stylesheet">
<link rel="stylesheet" href="modules/servers/upCloudVm/templates/assets/css/font-awesome.min.css">

<link href="modules/servers/upCloudVm/templates/assets/css/layout.css" rel="stylesheet">
<link href="modules/servers/upCloudVm/templates/assets/css/theme.css" rel="stylesheet">
<link href="modules/servers/UpCloudVm/templates/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="modules/servers/upCloudVm/templates/assets/css/font-awesome.min.css" rel="stylesheet">


<div class="module-main-header" style="">
        <a style="display: inline;" class="btn btn-back btn-icon" href="clientarea.php?action=productdetails&id={$serviceId}">
                <i class="fa fa-arrow-left"></i></a>
                <span style="font-size: 24px;">{$productGroup} - {$productName} {if $domain}{$domain}{/if}</span>
</div>

<div id="custom-alert" style="display:none;position:fixed; 
top:0px; 
right: 0px; 
max-width: 800px;
z-index:9999; 
border-radius:0px" class="alert alert-danger collapse alertUpCloud">
    <button type="button" id="close-custom-alert" class="close" style="margin-left:5px;" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
    <span id="custom-alert-message"></span>

</div>
<div id="custom-loader" class="loader" style='display:none; margin:-20px;'></div>
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


.upCloudTable td{
    vertical-align: middle !important;
}
.dataTables_length
{
    background:white !important;
}

.dataTables_filter input { width: 300px !important }


.alert-fixed {
    position:fixed; 
    top: 0px; 
    left: 0px; 
    width: 100%;
    z-index:9999; 
    border-radius:0px
}
</style>
<script>
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};
var page = getUrlParameter('page')
if(page != null)
{
    $("#Primary_Sidebar-ManageVM-"+page).addClass('active');
}
if(getUrlParameter('modop') == 'custom' && getUrlParameter('a') == 'management' && page == null)
{
    $("#Primary_Sidebar-ManageVM-editConfiguration").addClass('active');
}
$( document ).ajaxSend(function() {
  $('.modal').modal('hide');
  $('#mg-wrapper .btn').prop('disabled',true);
  $("#custom-loader").show();
});

$( document ).ajaxComplete(function() {
    $('#mg-wrapper .btn').prop('disabled',false);
    $("#custom-loader").hide();
});

$( document ).on( "click", ".btn", function() {
    $( "#custom-alert" ).hide();
});

$( "#close-custom-alert" ).click(function() {
    $( "#custom-alert" ).hide();
});

function parseResponse(data)
{
  if(data)
  {
  try 
  {
    var parsed = jQuery.parseJSON(data);
    if(parsed.result == 'success')
    {
      if(parsed.message != '')
      {
        $('#custom-alert-message').html(parsed.message);
      }
      else
      {
        $('#custom-alert-message').html('Action completed successfully');
      }
      $('#custom-alert').removeClass().addClass('alert alert-success collapse alertUpCloud').show();
    }
    else
    {
      if(parsed.message != '')
      {
        $('#custom-alert-message').html(parsed.message);
      }
      else
      {
        $('#custom-alert-message').html('Can not complete action');
      }
      $('#custom-alert').removeClass().addClass('alert alert-danger collapse alertUpCloud').show();
    }
  } catch(e) {
      console.log(e);
      $('#custom-alert-message').html('Can not parse message');
      $('#custom-alert').removeClass().addClass('alert alert-danger collapse alertUpCloud').show();
  }
 }
}

</script>