<?php
namespace WHMCS\Module\Server\upCloudVps;
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

use WHMCS\Module\Server\upCloudVps\upCloudVps;
use WHMCS\Module\Server\upCloudVps\Helper;

class adminManager
{

  private $manager;
  private $params;
  private $_LANG;

  public function __construct(array $params)
  {
    $this->manager = new upCloudVps($params);
    $this->params = $params;
    $this->_LANG = Helper::getLang();
  }


public function adminarea(){
  global $aInt;
  $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
  $details = $this->manager->GetServer($instanceId)['response']['server'];
  $totalStorage = 0;
  $memoryGb = $details['memory_amount'] / 1024;

  $templ = $details['storage_devices']['storage_device'];
  $zones = $this->manager->GetZones()['response']['zones']['zone'];

  foreach ($zones as $zone) {
      if ($zone['id'] == $details['zone']) {
          $details['zoneDescription'] = $zone['description'];
          break;
      }
  }


  foreach ($templ as $temp) {
      if ($temp['part_of_plan'] == "yes" || $details['plan'] == "custom") {
          $details['osname'] = $temp['storage_title'];
          $details['base_storage_size'] = $temp['storage_size'];
          break;
      }
  }
      if (!empty($details["ip_addresses"])) {
        foreach ($details['ip_addresses']['ip_address'] as $ip) {
          if($ip['access'] != 'utility'){
            $ReverseDNSValue = $this->manager->GetIPaddress($ip['address'])['response'];
            if (strpos($ReverseDNSValue['ip_address']['ptr_record'], "upcloud") !== false) {
              $this->manager->ModifyIPaddress($instanceId, $ip["address"], "client.".$_SERVER['SERVER_NAME'].".host");
            }
              $tableData[] = array($ip["address"], $ReverseDNSValue['ip_address']['ptr_record'], $ip["access"], $ip["family"]);
          } else {
            $tableData[] = array($ip["address"], "Not available for Utility IP Address", $ip["access"], $ip["family"]);
          }
        }
        $aInt->sortableTableInit("nopagination");
        $interfaceInfo = $aInt->sortableTable(array($this->_LANG["IPAddress"], $this->_LANG["reversePTR"], $this->_LANG["Access"], $this->_LANG["Family"],), $tableData);
      }

        foreach ($this->manager->Getplans()['response']['plans']['plan'] as $Plan){
         if($Plan['name'] == $details['plan'] and $Plan['memory_amount'] == $details['memory_amount']){
         $TotalTraffic = $Plan['public_traffic_out'] ;
         $Outgoing = $this->manager->formatSizeBytestoGB($details['plan_ipv4_bytes'] + $details['plan_ipv6_bytes']);
       $Percentage = round((($Outgoing / $TotalTraffic) * 100), 2) ;
       $progressClass = 'progress-bar-success';
   if ($Percentage >= 49 && $Percentage < 70) {
       $progressClass = 'progress-bar-info';
   } elseif ($Percentage >= 70 && $Percentage < 86) {
       $progressClass = 'progress-bar-warning';
   } elseif ($Percentage >= 86) {
       $progressClass = 'progress-bar-danger';
   }

   $Bandwidth = '<div class="progress">
       <div class="progress-bar ' . $progressClass . '" role="progressbar" aria-valuenow="' . $Percentage . '"
       aria-valuemin="0" aria-valuemax="100" style="width:' . $Percentage . '%">
         ' . $Percentage . '%
       </div>
     </div>
     ' . $this->_LANG["TotalTraffic"] . ': ' . $TotalTraffic . ' ' . $this->_LANG["GB"] . ' – ' . $this->_LANG["used"] . ' ' . $Outgoing . ' ' . $this->_LANG["GB"] . ' (' . $Percentage . '%)';
         }

        }


    $remoteAccessHost = $details['remote_access_host'];

    if (!empty($remoteAccessHost)) {
    $resolvedHost = gethostbyname($remoteAccessHost);
    $resolvedHost = ($resolvedHost !== $remoteAccessHost) ? $resolvedHost : $remoteAccessHost;
    } else {
    $resolvedHost = null;
    }

    $vncIp = $resolvedHost;

  $output = '
  <div style="display: flex; justify-content: space-between;">
  <table class="datatable"
  style="width:400px; text-align:center;margin-top:20px;border-spacing: 5px;border-collapse: separate;">
  <tbody>
  <tr> <th>'.$this->_LANG["Hostname"].'</th> <td>'.$details["hostname"].'</td> </tr>
  <tr> <th>'.$this->_LANG["VMId"].'</th> <td>'.$details["uuid"].'</td> </tr>
  <tr> <th>'.$this->_LANG["Template"].'</th> <td>'.$details['osname'].'</td> </tr>
  <tr> <th>'.$this->_LANG["Plan"].'</th> <td>'.$details["plan"].'</td> </tr>
  <tr> <th>'.$this->_LANG["Status"].'</th> <td>'.$details['state'].'</td> </tr>
  <tr> <th>'.$this->_LANG["Location"].'</th> <td>'.$details['zoneDescription'].'</td> </tr>
  <tr> <th>'.$this->_LANG["Backup"].'</th> <td>'.ucfirst($details["simple_backup"]).'</td> </tr>
  </tbody></table><table class="datatable"
  style="width:400px; text-align:center;margin-top:20px;border-spacing: 5px;border-collapse: separate;">
  <tbody>
  <tr> <th>'.$this->_LANG["cpu"].'</th> <td>'.$details["core_number"].''.$this->_LANG["core"].'</td> </tr>
  <tr> <th>'.$this->_LANG["Disk"].'</th> <td>'.$details["base_storage_size"].''.$this->_LANG["GB"].'</td> </tr>
  <tr> <th>'.$this->_LANG["memory"].'</th> <td>'.$memoryGb.''.$this->_LANG["GB"].'</td> </tr>
  <tr> <th>'.$this->_LANG["vncEnabled"].'</th> <td>'.ucfirst($details["remote_access_enabled"]).'</td> </tr>
  <tr> <th>'.$this->_LANG["vncHost"].'</th> <td>'. $vncIp.'</td> </tr>
  <tr> <th>'.$this->_LANG["vncPort"].'</th> <td>'.$details["remote_access_port"].'</td> </tr>
  <tr> <th>'.$this->_LANG["vncPassword"].'</th> <td>'.$details["remote_access_password"].'</td> </tr>
  </tbody></table>
</div>
  ';

  $ReverseDNS = '<table width="100%">
  <tr><td colspan="4">
  <select name="ip" class="form-control input-200 input-inline">
  <option value="">'.$this->_LANG["selIPAddress"].'</option>
  ';
  foreach ($details['ip_addresses']['ip_address'] as $IPList) {
   if ($IPList['access'] == 'public'){
  $ReverseDNS .= '<option value="'.$IPList['address'].'">'.$IPList['address'].'</option>';
   }
  }
  $ReverseDNS .= '
  </select>
  <input type="text" placeholder="'.$this->_LANG["rdnsval"].'" class="form-control input-200 input-inline" name="rdns">
  <input name="act" type="submit" value="rDNS" class="btn btn-sm btn-info">
  </td></tr></table>';

  $menu[$this->_LANG["VmInfo"]] = $output;
  $menu[$this->_LANG["Interface"]] = $interfaceInfo;
  $menu[$this->_LANG["reversePTR"]] = $ReverseDNS;
  $menu[$this->_LANG["Bandwidth"]] = $Bandwidth;
  return $menu;
}


}
