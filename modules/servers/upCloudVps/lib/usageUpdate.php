<?php
namespace WHMCS\Module\Server\upCloudVps;
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}
use WHMCS\Database\Capsule;
use WHMCS\Module\Server\upCloudVps\upCloudVps;

class usageUpdate{

  private $manager;
  private $params;

  public function __construct(array $params)
  {
    $this->manager = new upCloudVps($params);
    $this->params = $params;
  }

  public function usage(){
    $services = Capsule::table('tblhosting')
    ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
    ->where('tblhosting.domainstatus', 'Active')
    ->where('tblhosting.server', $this->params['serverid'])
    ->where('tblproducts.servertype', 'upCloudVps')->get(['tblhosting.id','tblhosting.packageid']);

  foreach ($services as $whmcsData) {
    $serviceId = $whmcsData->id;
    $packageId = $whmcsData->packageid;

    $tblcustomfields = Capsule::table('tblcustomfields')
    ->where('tblcustomfields.relid', $packageId)
    ->where('tblcustomfields.fieldname', 'instanceId|instance Id')->value('id');

    $instanceId = Capsule::table('tblcustomfieldsvalues')
    ->where('tblcustomfieldsvalues.relid', $serviceId)
    ->where('tblcustomfieldsvalues.fieldid', $tblcustomfields)->value('value');

    $details = $this->manager->GetServer($instanceId)['response']['server'];
    $Outgoing = $this->manager->formatSizeBytestoGB($details['plan_ipv4_bytes'] + $details['plan_ipv6_bytes']);

    foreach ($this->manager->Getplans()['response']['plans']['plan'] as $Plan){
     if($Plan['name'] == $details['plan'] and $Plan['memory_amount'] == $details['memory_amount']){
     $TotalTraffic = $Plan['public_traffic_out'] * 1000 ;
     }
    }
    $Outgoing = $this->manager->formatSizeBytestoMB($details['plan_ipv4_bytes'] + $details['plan_ipv6_bytes']);
    Capsule::table('tblhosting')
            ->where('server', $this->params['serverid'])
            ->where('id', $serviceId)
            ->where('packageid', $packageId)
            ->update([
                'bwusage' => $Outgoing,
                'bwlimit' => $TotalTraffic,
                'lastupdate' => Capsule::raw('now()'),
            ]);
  }
  }

}
