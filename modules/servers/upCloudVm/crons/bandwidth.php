<?php

/**
 * Created by ModulesGarden.
 *
 * PHP version 7
 *
 * @author ModulesGarden <contact@modulesgarden.com>
 * @link https://www.modulesgarden.com/
 *
 *  * ******************************************************************
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *  * ******************************************************************
 */

date_default_timezone_set("UTC");

$rootdir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once $rootdir.'/init.php';
require_once(dirname(dirname(__FILE__))."/vendor/autoload.php");

use Illuminate\Database\Capsule\Manager as Capsule;
use ModulesGarden\upCloudVm\Manager;

$services = Capsule::table('tblhosting')
    ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
    ->where('tblhosting.domainstatus', 'Active')
    ->where('tblproducts.servertype', 'upCloudVm')->get(['tblhosting.id','tblhosting.packageid']);

foreach ($services as $service)
 {
    $product = Capsule::table('tblproducts')->where('id', $service->packageid)->first();
    $server  = Capsule::table('tblservers')
        ->join('tblservergroupsrel', 'tblservergroupsrel.serverid', '=', 'tblservers.id')
        ->where('tblservergroupsrel.groupid', $product->servergroup)
        ->first();

    $params = [
        'serverusername' => $server->username,
        'serverpassword' => decrypt($server->password),
        'serviceid'      => $service->id,
    ];
    try
    {
        $manager  = new Manager($params);
        $response = $manager->getServerDetails();
        $Ipv4     = $response['data']->server->plan_ipv4_bytes;
        $Ipv6     = $response['data']->server->plan_ipv6_bytes;

        if ($Ipv4 != '' && $Ipv6 != '')
        {
            Capsule::table('mod_upCloudVm_bandwidth')->insert([ 'serviceId' => $service->id,'IPv4' => $Ipv4,
            'IPv6' => $Ipv6]);
        }
    }
    catch (\Exception $e)
    {
        echo $e->getMessage();
    }
}
