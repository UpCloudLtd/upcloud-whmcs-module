{include file='./header.tpl'}

<div id="mg-wrapper" class="main-tab">
        <div class="module-header">
            <i class="icon-header icon-graphs"></i>
            <h1>{$_LANG.bandwidth.title}</h1>
            <p>{$_LANG.bandwidth.description}</p>
        </div>
       
</div>

<script src="modules/servers/upCloudVm/templates/assets/js/Chart.js"></script>

<select name="graphTime" id="graphTime" class="form-control" style=" width:200px; float:right;">
<option value="24 Hours">24 Hours</option>
<option value="Week">Week</option>
<option value="Month">Month</option>
<option value="Year">Year</option>
</select>

<canvas id="IPv4"></canvas>

<canvas id="IPv6"></canvas>

<script>

 var chart1,chart2;
 var IPv4 = document.getElementById('IPv4').getContext('2d');

 var IPv6 = document.getElementById('IPv6').getContext('2d');

      $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'getBandwidth', time: "24 Hours"})
  .done(function( data ) { 

  if(data)
  {
    try 
    {
        var parsed = jQuery.parseJSON(data);
         chart1 = new Chart(IPv4, {
        type: 'line',
        data: {
            labels: parsed.data.labels,
            datasets: [{
                label: "IPv4 in bytes",
                borderColor: 'rgb(255, 99, 132)',
                data: parsed.data.IPv4,
            }]
        }
    });
    chart2 = new Chart(IPv6, {
        type: 'line',
        data: {
            labels: parsed.data.labels,
            datasets: [{
                label: "IPv6 in bytes",
                borderColor: 'rgb(55, 199, 132)',
                data: parsed.data.IPv6,
            }]
        }
    });

    } catch(e) {
        
    }
 }

  });

$('#graphTime').on('change', function (event) {
    $.post( "clientarea.php?action=productdetails&id={$serviceid}", { subaction: 'getBandwidth', time: $('#graphTime').val()})
  .done(function( data ) { 
    if(data)
  {
    try 
    {
        var parsed = jQuery.parseJSON(data);
        chart1.destroy();
        chart2.destroy();
        chart1 = new Chart(IPv4, {
        type: 'line',
        data: {
            labels: parsed.data.labels,
            datasets: [{
                label: "IPv4 in bytes",
                borderColor: 'rgb(255, 99, 132)',
                data: parsed.data.IPv4,
            }]
        }
    });
    chart2 = new Chart(IPv6, {
        type: 'line',
        data: {
            labels: parsed.data.labels,
            datasets: [{
                label: "IPv6 in bytes",
                borderColor: 'rgb(55, 199, 132)',
                data: parsed.data.IPv6,
            }]
        }
    });
    } catch(e) {
        return;
    }
 }
  });
    return false;
})
</script>