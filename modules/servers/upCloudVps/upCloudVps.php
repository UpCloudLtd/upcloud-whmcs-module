<?php
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}
use App;
use WHMCS\Database\Capsule;
use WHMCS\View\Menu\Item as MenuItem;
use WHMCS\Module\Server\upCloudVps\upCloudVps;
use WHMCS\Module\Server\upCloudVps\Helper;
use WHMCS\Module\Server\upCloudVps\vmManager;
use WHMCS\Module\Server\upCloudVps\clientManager;
use WHMCS\Module\Server\upCloudVps\adminManager;
use WHMCS\Module\Server\upCloudVps\usageUpdate;
use WHMCS\Module\Server\upCloudVps\configOptions;

function upCloudVps_MetaData()
{
    return [
        'DisplayName' => 'UpCloud Server',
        'APIVersion' => '1.3',
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'RequiresServer' => true,
    ];
}

function upCloudVps_ConfigOptions(array $params)
{
  if (!Capsule::schema()->hasTable('mod_upCloudVps_bandwidth')) {
      Capsule::schema()->create('mod_upCloudVps_bandwidth', function ($table) {
          $table->integer('serviceId');
          $table->string('IPv4');
          $table->string('IPv6');
          $table->timestamp('created_at')->default(Capsule::raw('CURRENT_TIMESTAMP'));
      });
  }


    $server = Capsule::table('tblservers')
        ->join('tblservergroupsrel', 'tblservergroupsrel.serverid', '=', 'tblservers.id')
        ->where('tblservergroupsrel.groupid', App::getFromRequest('servergroup'))
        ->where('tblservers.disabled', '0')
        ->first();

    $params['serverusername'] = $server->username;
    $params['serverpassword'] = decrypt($server->password);
    $manager = new configOptions($params);
    return $manager->configs();
}

function upCloudVps_TestConnection(array $params)
{
  try {
    $manager = new upCloudVps($params);
    $account = $manager->GetAccountInfo();
    if($account['response_code'] == '200'){
    $success = true;
  } else {
    $errorMsg = 'Invalid Credentials';
  }
} catch (\Exception $e) {
       $success = false;
       $errorMsg = $e->getMessage();
   }
   return ['success' => $success, 'error' => $errorMsg];
 }

 function upCloudVps_CreateAccount(array $params)
 {
   if ($params['status'] != 'Pending' && $params['status'] != 'Terminated') {
       return 'Cannot create service.';
   }
   try {
     $vmManager = new vmManager($params);
     return $vmManager->create();
   } catch (\Exception $e) {
          return $e->getMessage();
    }
  }

  function upCloudVps_TerminateAccount(array $params)
  {
      if ($params['status'] != 'Active' && $params['status'] != 'Suspended') {
          return 'Cannot terminate service';
      }
      try{
        $vmManager = new vmManager($params);
        return $vmManager->terminate();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  function upCloudVps_SuspendAccount(array $params)
  {
    if ($params['status'] == 'Terminated') {
        return 'Cannot suspend terminated service';
    }
      try {
        $vmManager = new vmManager($params);
        return $vmManager->stop();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  function upCloudVps_UnsuspendAccount(array $params)
  {
    if ($params['status'] == 'Terminated') {
        return 'Cannot unsuspend terminated service';
    }
      try {
        $vmManager = new vmManager($params);
        return $vmManager->start();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  function upCloudVps_StopVPS(array $params)
  {
      try {
        $vmManager = new vmManager($params);
        return $vmManager->stop();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  function upCloudVps_StartVPS(array $params)
  {
      try {
        $vmManager = new vmManager($params);
        return $vmManager->start();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  function upCloudVps_RebootVPS(array $params)
  {
      try {
        $vmManager = new vmManager($params);
        return $vmManager->reboot();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }

  function upCloudVps_AdminCustomButtonArray()
  {
      return [
          'Start' => 'StartVPS',
          'Stop' => 'StopVPS',
          'Reboot' => 'RebootVPS',
      ];
  }

  function upCloudVps_ReverseDNS( $params )
  {
  try {
    $params["ip"] = $_POST['ip'];
    $params["rdns"] = $_POST['rdns'];
    $vmManager = new vmManager($params);
    return $vmManager->rdns();

  } catch (\Exception $e) {
            return $e->getMessage();
      }
  }

  function upCloudVps_AdminServicesTabFields(array $params)
  {
      if ($params['status'] != 'Active') {
          return [];
      }
      try {
        $adminManager = new adminManager($params);
          return $adminManager->adminarea();
      } catch (\Exception $e) {
          return ['error' => $e->getMessage()];
      }
  }

  function upCloudVps_AdminServicesTabFieldsSave( $params )
  {
  try {
  if($_REQUEST['act'] == 'rDNS'){
  upCloudVps_ReverseDNS( $params );
  }
} catch (\Exception $e) {
          return $e->getMessage();
      }
      return 'success';
  }

  function upCloudVps_ChangePackage(array $params)
  {
      try {
        $vmManager = new vmManager($params);
        return $vmManager->upgradePlan();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }


  function upCloudVps_ClientArea(array $params)
  {
      if ($params['status'] != 'Active') {
          return [];
      } 
          try {
          Helper::clientAreaPrimarySidebarHook($params);
          $action = App::getFromRequest('subaction');
          if (!empty($action)) {
              Helper::ajaxAction($params, $action);
          }
          $clientManager = new clientManager($params);
          $vm = $clientManager->getData('details');
          return [
              'templatefile' => 'templates/overview.tpl',
              'templateVariables' => [
                  'vm' => $vm,
                  '_LANG' => Helper::getLang(),
              ],
          ];
      } catch (\Exception $e) {
          return ['tabOverviewReplacementTemplate' => 'templates/error.tpl'];
      }
  }

  function upCloudVps_UsageUpdate(array $params)
  {
      try {
        $manager = new usageUpdate($params);
        $manager->usage();
      } catch (\Exception $e) {
          return $e->getMessage();
      }
  }
