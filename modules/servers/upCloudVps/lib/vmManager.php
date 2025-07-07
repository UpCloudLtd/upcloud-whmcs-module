<?php

namespace WHMCS\Module\Server\upCloudVps;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Server\upCloudVps\upCloudVps;
use WHMCS\Module\Server\upCloudVps\Helper;

class vmManager
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

    public function stop()
    {
        $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
        $getInstamceState = $this->manager->GetServer($instanceId)['response'];
        if ($getInstamceState['server']['state'] == 'started') {
            $stop = $this->manager->StopServer($instanceId);
            sleep(45);
            return isset($stop['response']['error']['error_message']) ? $stop['response']['error']['error_message'] : 'success';
        }
    }

    public function start()
    {
        $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
        $start = $this->manager->StartServer($instanceId);
        sleep(45);
        return isset($start['response']['server']['host']) ? 'success' : $start['response']['error']['error_message'];
    }

    public function reboot()
    {
        $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
        $reboot = $this->manager->RestartServer($instanceId);
        return isset($reboot['response']['error']['error_message']) ? $reboot['response']['error']['error_message'] : 'success';
    }

    public function terminate()
    {
        $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
        $delete = ($this->params['configoptions']['backup'] == 'no') ? $this->manager->DeleteServernStorage($instanceId) : $this->manager->DeleteServernStorageBackup($instanceId);
        if ($delete['response_code'] == '204') {
            $this->params['model']->serviceProperties->save(['instanceId|instance Id' => '']);
            Capsule::table('mod_upCloudVps_bandwidth')->where('serviceId', '=', $this->params['serviceid'])->delete();
            $message = 'success';
        } else {
            $message = $delete['response']['error']['error_message'];
        }
        return $message;
    }

    public function create()
    {
        $zone = $this->params['configoptions']['location'] ?? $this->params['configoption1'];
        $Plan = $this->params['configoption2'];
        $OsUUID = $this->params['configoptions']['template'] ?? $this->params['configoption3'];
        $sshkey = $this->params['customfields']['ssh_key'];
        $user_data = $this->params['customfields']['userData'];
        $Hostname = !empty($this->params['domain']) ? $this->params['domain'] : 'client' . $this->params['serviceid'] . '.' . $_SERVER['SERVER_NAME'];
        $sshkey = empty($sshkey) ? 'na' : $sshkey;
        $user_data = empty($user_data) ? 'na' : $user_data;
        $backup = $this->params['configoptions']['backup'];
        $networking = 'ipv4only';
        if ($Plan == 'custom') {
            $ram = $this->params['configoptions']['ram'];
            $vcpu = $this->params['configoptions']['vcpu'];
            $storage = $this->params['configoptions']['storage'];
            $actionResponse = $this->manager->CreateServer($zone, $Hostname, $Plan, $OsUUID, $sshkey, $user_data, $backup, $networking, $ram, $vcpu, $storage);
        } else {
            $actionResponse = $this->manager->CreateServer($zone, $Hostname, $Plan, $OsUUID, $sshkey, $user_data, $backup, $networking);
        }
        if ($actionResponse['response_code'] == '202') {
            foreach ($actionResponse['response']['server']['ip_addresses']['ip_address'] as $IPList) {
                if ($IPList['access'] == 'public') {
                    if ($IPList['family'] == 'IPv4' && ($IPList['part_of_plan'] || $Plan == 'custom')) {
                        $IPv4 = $IPList['address'];
                    } elseif ($IPList['family'] == 'IPv6') {
                        $IPv6 = $IPList['address'];
                    }
                }
            }

            $postData = [
                'serviceid' => $this->params['serviceid'],
                'serviceusername' => $actionResponse['response']['server']['username'],
                'dedicatedip' => $IPv4,
                'servicepassword' => $actionResponse['response']['server']['password'],
                'assignedips' => $IPv6,
                'domain' => $actionResponse['response']['server']['hostname'],
            ];
            localAPI('UpdateClientProduct', $postData);
            $this->params['model']->serviceProperties->save(['instanceId|instance Id' => $actionResponse['response']['server']['uuid']]);
            $message = 'success';
        } else {
            $message = $actionResponse['response']['error']['error_message'];
        }
        return $message;
    }

    public function rdns()
    {
        $ip = $this->params['ip'];
        $DNSValue = $this->params['rdns'];
        $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
        $data = $this->manager->ModifyIPaddress($instanceId, $ip, $DNSValue);
        return $data['response_code'] == '202' ? 'success' : $data['response']['error']['error_message'];
    }

    public function upgradePlan()
    {
        $Plan = $this->params['configoption2'];
        if ($Plan == 'custom') {
            return [];
        } else {
            $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
            $data = $this->manager->ModifyServer($instanceId, $Plan);
            return isset($data['response']['error']['error_message']) ? $data['response']['error']['error_message'] : 'success';
        }
    }
}
