<?php

date_default_timezone_set("UTC");

$rootdir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once $rootdir . '/init.php';

use WHMCS\Database\Capsule;
use WHMCS\Module\Server\upCloudVps\upCloudVps;

$services = Capsule::table('tblhosting')
    ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
    ->where('tblhosting.domainstatus', 'Active')
    ->where('tblproducts.servertype', 'upCloudVps')->get(['tblhosting.id', 'tblhosting.packageid']);

foreach ($services as $service) {
    $serviceId = $service->id;
    $packageId = $service->packageid;

    $tblcustomfields = Capsule::table('tblcustomfields')
        ->where('tblcustomfields.relid', $packageId)
        ->where('tblcustomfields.fieldname', 'instanceId|instance Id')->value('id');

    $instanceId = Capsule::table('tblcustomfieldsvalues')
        ->where('tblcustomfieldsvalues.relid', $serviceId)
        ->where('tblcustomfieldsvalues.fieldid', $tblcustomfields)->value('value');

    $product = Capsule::table('tblproducts')->where('id', $packageId)->first();
    $server = Capsule::table('tblservers')
        ->join('tblservergroupsrel', 'tblservergroupsrel.serverid', '=', 'tblservers.id')
        ->where('tblservergroupsrel.groupid', $product->servergroup)
        ->first();

    $params = [
        'serverusername' => $server->username,
        'serverpassword' => decrypt($server->password),
    ];

    try {
        $manager = new upCloudVps($params);
        $details = $manager->GetServer($instanceId)['response']['server'];
        $Ipv4 = $manager->formatSizeBytestoMB($details['plan_ipv4_bytes']);
        $Ipv6 = $manager->formatSizeBytestoMB($details['plan_ipv6_bytes']);
        if ($Ipv4 != '' && $Ipv6 != '') {
            Capsule::table('mod_upCloudVps_bandwidth')->insert([
                'serviceId' => $serviceId,
                'IPv4' => $Ipv4,
                'IPv6' => $Ipv6
            ]);
        }
    } catch (\Exception $e) {
        echo $e->getMessage() . ' [' . $service->id . ']' . PHP_EOL;
    }
}
