<?php

/**
 * Created by ModulesGarden.
 *
 * PHP version 7
 *
 * @author ModulesGarden <contact@modulesgarden.com>
 *
 * @see https://www.modulesgarden.com/
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
require_once dirname(__FILE__).'/vendor/autoload.php';

use WHMCS\Database\Capsule;
use ModulesGarden\upCloudVm\Manager;
use ModulesGarden\upCloudVm\Helper;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Module Meta Data.
 *
 * @return array
 */
function upCloudVm_MetaData()
{
    return [
        'DisplayName' => 'UpCloud VM',
        'APIVersion' => '1.1',
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'RequiresServer' => true,
    ];
}

/**
 * Config options are the module settings defined on a per product basis.
 *
 * @param array $params WHMCS Params
 *
 * @throws \Exception if server group not set or storage is not writable
 *
 * @return array
 */
function upCloudVm_ConfigOptions(array $params)
{
    if (App::getFromRequest('action') != 'save' && App::getFromRequest('servergroup') == 0) {
        throw new \Exception('Please select Server Group first');
    }

    if (App::getFromRequest('action') != 'save' && !Helper::checkStorage()) {
        throw new \Exception('Make sure that '.dirname(__FILE__).'/storage directory is writable');
    }

    if (!Capsule::schema()->hasTable('mod_upCloudVm')) {
        Capsule::schema()->create('mod_upCloudVm', function ($table) {
            $table->increments('id');
            $table->string('serviceId');
            $table->string('serverId');
        });
    }

    if (!Capsule::schema()->hasTable('mod_upCloudVm_bandwidth')) {
        Capsule::schema()->create('mod_upCloudVm_bandwidth', function ($table) {
            $table->integer('serviceId');
            $table->string('IPv4');
            $table->string('IPv6');
            $table->timestamp('created_at')->default(Capsule::raw('CURRENT_TIMESTAMP'));
        });
    }

    $product = WHMCS\Product\Product::find(App::getFromRequest('id'));

    $server = Capsule::table('tblservers')
        ->join('tblservergroupsrel', 'tblservergroupsrel.serverid', '=', 'tblservers.id')
        ->where('tblservergroupsrel.groupid', App::getFromRequest('servergroup'))
        ->where('tblservers.disabled', '0')
        ->first();

    $params['serverusername'] = $server->username;
    $params['serverpassword'] = decrypt($server->password);
    $params['serverhostname'] = $server->hostname;
    $params['serverip'] = $server->ipaddress;
    $params['serversecure'] = $server->secure;

    try {
        $manager = new Manager($params);
        $pomPlans = $pomZones = $pomTemplates = [];
        $templates = $manager->getTemplates()['data']->storages->storage;

        foreach ($templates as $template) {
            $pomTemplates[$template->uuid] = $template->title;
        }

        $plans = $manager->getPlans()['data']->plans->plan;
        foreach ($plans as $plan) {
            $pomPlans[$plan->name] = $plan->name.' [ '.$plan->storage_tier.' ]';
        }

        $zones = $manager->getZones()['data']->zones->zone;
        foreach ($zones as $zone) {
            $pomZones[$zone->id] = $zone->description;
        }

        $output = '
       <tr style="width:100%;background-color:#BFBFBF;"> <td colspan="4">Default Options</td> </tr>
       <tr>
       <td class="fieldlabel" width="20%">Configurable Options </td>
       <td class="fieldarea">
       <a  href="configproducts.php?action=edit&id='.App::getFromRequest('id').'&tab=3&generateFields=configurable" >
       Generate Default</a>
       </td>
       <td class="fieldlabel" width="20%">Custom Fields</td>
       <td class="fieldarea">
       <a  href="configproducts.php?action=edit&id='.App::getFromRequest('id').'&tab=3&generateFields=custom" >
       Generate Default</a>
       </td>
       <tr  style="width:100%;background-color:#BFBFBF;"> <td colspan="4">Main Configuration</td> </tr>
       <tr>

       <td class="fieldlabel" width="20%">Default Location</td>
       <td class="fieldarea">
       <select name="packageconfigoption[1]" class="form-control select-inline">
       ';
        foreach ($pomZones as $zoneId => $desc) {
            $output .= '<option value="'.$zoneId.'" '.(($zoneId == $product->moduleConfigOption1) ? 'selected' : '').'>'.$desc.'</option>';
        }

        $output .= '</select></td>
       <td class="fieldlabel" width="20%">Plan</td>
       <td class="fieldarea">
       <select name="packageconfigoption[2]" class="form-control select-inline">
       ';
        foreach ($pomPlans as $planId => $desc) {
            $output .= '<option value="'.$planId.'" '.(($planId == $product->moduleConfigOption2) ? 'selected' : '').'>'.$desc.'</option>';
        }

        $output .= '</td></tr><tr><td class="fieldlabel" width="20%">Template</td><td class="fieldarea">
       <select name="packageconfigoption[3]" class="form-control select-inline">';
        foreach ($pomTemplates as $templateId => $desc) {
            $output .= '<option value="'.$templateId.'" '.(($templateId == $product->moduleConfigOption3) ? 'selected' : '').'>'.$desc.'</option>';
        }

        $output .= '</select></td></tr>';

        if (App::getFromRequest('action') != 'save') {
            ob_clean();
            $data['content'] = $output;
            echo json_encode($data);
            die;
        }

        return [
        'Default Location' => ['Type' => 'dropdown', 'Options' => $pomZones],
        'Plan' => ['Type' => 'dropdown', 'Options' => $pomPlans],
        'Template' => ['Type' => 'dropdown', 'Options' => $pomTemplates],
        ];
    } catch (Exception $e) {
        if (App::getFromRequest('action') != 'save') {
            throw new \Exception($e->getMessage());
        }
    }
}

/**
 * Test Connection with UpCloud API.
 *
 * @param array $params WHMCS Params
 *
 * @return array
 */
function upCloudVm_TestConnection(array $params)
{
    try {
        $manager = new Manager($params);
        $manager->testConnection();
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }

    return ['success' => true];
}

/**
 * Create server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_CreateAccount(array $params)
{
    if ($params['status'] != 'Pending' && $params['status'] != 'Terminated') {
        return 'Cannot create service.';
    }

    try {
        $manager = new Manager($params);
        $response = $manager->createServer();

        $postData = [
            'password2' => $response['data']->server->password,
        ];
        $crypted = localAPI('EncryptPassword', $postData);

        Capsule::table('mod_upCloudVm')->updateOrInsert(
            ['serviceId' => $params['serviceid']],
            [
                'serverId' => $response['data']->server->uuid,
            ]
        );

        Capsule::table('tblhosting')->updateOrInsert(
            ['id' => $params['serviceid']],
            [
                'username' => 'root',
                'password' => $crypted['password'],
            ]
        );
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Stop and Terminate Server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_TerminateAccount(array $params)
{
    if ($params['status'] != 'Active' && $params['status'] != 'Suspended') {
        return 'Cannot terminate service';
    }

    try {
        $manager = new Manager($params);
        $manager->terminateServer();
        Capsule::table('mod_upCloudVm')->where('serviceId', $params['serviceid'])->delete();
        Capsule::table('mod_upCloudVm_bandwidth')->where('serviceId', $params['serviceid'])->delete();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Stop and Suspend Server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_SuspendAccount(array $params)
{
    if ($params['status'] == 'Terminated') {
        return 'Cannot suspend terminated service';
    }

    try {
        $manager = new Manager($params);
        $status = $manager->getServerDetails()['data']->server->state;
        if ($status == 'started') {
            $manager->stopServer();
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Start and Unsuspend Server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_UnsuspendAccount(array $params)
{
    if ($params['status'] == 'Terminated') {
        return 'Cannot unsuspend terminated service';
    }

    try {
        $manager = new Manager($params);
        $status = $manager->getServerDetails()['data']->server->state;
        if ($status == 'stopped') {
            $manager->startServer();
        }
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Set custom action on Admin Area side.
 *
 * @return array
 */
function upCloudVm_AdminCustomButtonArray()
{
    return [
        'Start VM' => 'StartVM',
        'Stop VM' => 'StopVM',
        'Shutdown VM' => 'ShutdownVM',
        'Reboot VM' => 'RebootVM',
        'Refresh' => 'Refresh',
    ];
}

/**
 * Set custom action on Client Area side.
 *
 * @return array
 */
function upCloudVm_ClientAreaCustomButtonArray()
{
    return [
        'Management' => 'management',
    ];
}

/**
 * Admin Area output.
 *
 * @param array $params WHMCS Params
 *
 * @return array
 */
function upCloudVm_AdminServicesTabFields(array $params)
{
    if ($params['status'] != 'Active') {
        return [];
    }

    if (App::getFromRequest('subaction') == 'novnc') {
        Helper::runConsole($params);
    }

    try {
        $manager = new Manager($params);
        $details = $manager->getServerDetails()['data'];
        $templ = $manager->getTemplate();
        $zones = $manager->getZones();

        foreach ($zones['data']->zones->zone as $zone) {
            if ($zone->id == $details->server->zone) {
                $details->server->zone = $zone->description;
                break;
            }
        }

        $output = "
        <table class='datatable' 
        style='width:400px; text-align:center;margin-top:20px;border-spacing: 5px;border-collapse: separate;'>
        <tbody>
        <tr> <th>Hostname</th> <td>".$details->server->hostname.'</td> </tr>
        <tr> <th>Uuid</th> <td>'.$details->server->uuid.'</td> </tr>
        <tr> <th>Template</th> <td>'.$templ['data']->storage->title.'</td> </tr>
        <tr> <th>Plan</th> <td>'.$details->server->plan.'</td> </tr>   
        <tr> <th>Status</th> <td>'.$details->server->state.'</td> </tr>
        <tr> <th>Location</th> <td>'.$details->server->zone.'</td> </tr>
        </tbody></table>';

        if (!empty($details->server->ip_addresses)) {
            $output .= "<table class='datatable' 
            style='text-align:center;width:400px; margin-top:20px;border-spacing: 5px;
            border-collapse: separate;'><thead><tr><th>IP Address</th><th>Access</th><th>Family</th></tr></thead>
            <tbody>";
            foreach ($details->server->ip_addresses->ip_address as $ip) {
                $output .= "<tr><td>{$ip->address}</td><td>{$ip->access}</td><td>{$ip->family}</td></tr>";
            }

            $output .= '</tbody></table>';
        }

        if (!Helper::checkStorage()) {
            $menu['Console'] = "<span style='color:red;'>Make sure that ".dirname(__FILE__).'/storage directory is writable</span>';
        } else {
            $menu['Console'] = ($details->server->state == 'started' && $details->server->remote_access_enabled != 'no') ? '<button class="btn btn-primary" onclick="window.open( window.location.href+\'&subaction=novnc\', \'\',\'width=1024,height=768\'); return false;">Run Console</button>' : "<span style='color:red;'>Server or VNC Console Off</span>";
        }

        $menu['VM Informations'] = $output;

        return $menu;
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

/**
 * Client area management.
 *
 * @param array $params WHMCS Params
 *
 * @return array
 */
function upCloudVm_ClientArea(array $params)
{
    if ($params['status'] != 'Active') {
        return [];
    }

    try {
        $manager = new Manager($params);
        $manager->getServerDetails();

        $action = App::getFromRequest('subaction');

        if ($action == 'novnc') {
            Helper::RunConsole($params);
        } elseif (!empty($action)) {
            Helper::ajaxAction($params, $action);
        }

        if (App::getFromRequest('modop') == 'custom' && App::getFromRequest('a') == 'management') {
            return;
        }

        Helper::clientAreaPrimarySidebarHook($params);

        return [
            'templatefile' => 'templates/overview.tpl',
            'templateVariables' => [
                'vm' => Helper::getData($params, 'details'),
                '_LANG' => Helper::getLang(),
            ],
        ];
    } catch (\Exception $e) {
        return ['tabOverviewReplacementTemplate' => 'templates/error.tpl'];
    }
}

/**
 * This function manages client area pages.
 *
 * @param array $params WHMCS Params
 *
 * @return array
 */
function upCloudVm_management(array $params)
{
    try {
        Helper::clientAreaPrimarySidebarHook($params);
        $page = (!empty(filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING)) ? filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING) : 'editConfiguration');
        $productDetails = Capsule::table('tblproductgroups')
            ->join('tblproducts', 'tblproducts.gid', '=', 'tblproductgroups.id')
            ->join('tblhosting', 'tblproducts.id', '=', 'tblhosting.packageid')
            ->where('tblhosting.id', $params['serviceid'])
            ->select('tblproductgroups.name as productGroup', 'tblproducts.name as productName')
            ->first();

        return [
            'templatefile' => 'templates/'.$page,
            'templateVariables' => [
                'serviceId' => $params['serviceid'],
                'productGroup' => $productDetails->productGroup,
                'productName' => $productDetails->productName,
                'domain' => $params['domain'],
                'vm' => Helper::getData($params, $page),
                '_LANG' => Helper::getLang(),
            ],
        ];
    } catch (\Exception $e) {
        return ['templatefile' => 'templates/error'];
    }
}

/**
 * Starts server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_StartVM(array $params)
{
    try {
        $manager = new Manager($params);
        $manager->startServer();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Soft stops server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_StopVM(array $params)
{
    try {
        $manager = new Manager($params);
        $manager->stopServer();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Hard stops server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_ShutdownVM(array $params)
{
    try {
        $manager = new Manager($params);
        $manager->forceStopServer();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Restart server.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_RebootVM(array $params)
{
    try {
        $manager = new Manager($params);
        $manager->rebootServer();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Upgrade server's plan.
 *
 * @param array $params WHMCS Params
 *
 * @return string
 */
function upCloudVm_ChangePackage(array $params)
{
    try {
        $manager = new Manager($params);
        $manager->upgradePlan();
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    return 'success';
}

/**
 * Refresh Admin Area.
 *
 * @return string
 */
function upCloudVm_Refresh()
{
    return 'success';
}
