<?php

namespace WHMCS\Module\Server\upCloudVps;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Server\upCloudVps\upCloudVps;

class ajaxAction
{
    private $manager;
    private $instanceId;
    private $serviceid;
    public function __construct(array $params)
    {
        $this->manager = new upCloudVps($params);
        $this->instanceId = $params['model']->serviceProperties->get('instanceId|instance Id');
        $this->serviceid = $params['serviceid'];
    }

    public function refreshServer()
    {
        return $this->manager->GetServer($this->instanceId);
    }

    public function StartServer()
    {
        return $this->manager->StartServer($this->instanceId);
    }

    public function RestartServer()
    {
        return $this->manager->RestartServer($this->instanceId);
    }

    public function StopServer()
    {
        return $this->manager->StopServer($this->instanceId);
    }

    public function saveVNCConfiguration()
    {
        return $this->manager->vncPasswordUpdate($this->instanceId, filter_input(INPUT_POST, 'vnc_password', FILTER_SANITIZE_STRING));
    }

    public function changeVNCStatus()
    {
        $type = filter_input(INPUT_POST, 'vnc', FILTER_SANITIZE_STRING);
        $type = ($type == 'on') ? 'yes' : 'no';
        return $this->manager->vncEnableDisable($this->instanceId, $type);
    }

    public function vncDetails()
    {
        $details = $this->manager->GetServer($this->instanceId);
        if ($details['response']['error']['error_message']) {
            return $details['response']['error']['error_message'];
        } else {
            $remoteAccessHost = $details['response']['server']['remote_access_host'];
            if (!empty($remoteAccessHost)) {
                $resolvedHost = gethostbyname($remoteAccessHost);
                $resolvedHost = ($resolvedHost !== $remoteAccessHost) ? $resolvedHost : $remoteAccessHost;
            } else {
                $resolvedHost = null;
            }
            $results['vnchost'] = $resolvedHost;
            $results['vncport'] = $details['response']['server']['remote_access_port'];
            return $results;
        }
    }

    public function getIpAddresses()
    {
        $details = $this->manager->GetServer($this->instanceId);
        $ips = $details['response']['server']['ip_addresses']['ip_address'];
        foreach ($ips as $ip) {
            $ReverseDNSValue = $this->manager->GetIPaddress($ip['address'])['response']['ip_address']['ptr_record'];
            if (strpos($ReverseDNSValue, 'upcloud') !== false) {
                $this->manager->ModifyIPaddress($this->instanceId, $ip['address'], 'client.' . $_SERVER['SERVER_NAME'] . '.host');
            }
            $btn = ($ip['access'] == 'utility') ? '' : '<button class="btn btn-primary editIp" data-toggle="modal" data-target="#editPtrModal" data-ip="' . $ip['address'] . '" data-ptr="' . $ReverseDNSValue . '"><i class="btn-icon fa fa-pencil fa-lg"></i></button>';
            $output[] = [
                ucfirst($ip['access']) . ' ' . $ip['family'],
                $ip['address'],
                $ReverseDNSValue,
                $btn,
            ];
        }

        $ips['data'] = (!empty($output)) ? $output : [];
        return $ips;
    }

    public function editIp()
    {
        return $this->manager->ModifyIPaddress($this->instanceId, filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING), filter_input(INPUT_POST, 'ptr', FILTER_SANITIZE_STRING));
    }

    public function saveServerConfiguration()
    {
        $serverConfig = [
            'server' => [
                'hostname' => filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING),
                'boot_order' => filter_input(INPUT_POST, 'bootOrder', FILTER_SANITIZE_STRING),
                'video_model' => filter_input(INPUT_POST, 'displayAdapter', FILTER_SANITIZE_STRING),
                'nic_model' => filter_input(INPUT_POST, 'networkAdapter', FILTER_SANITIZE_STRING),
                'timezone' => filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING),
            ],
        ];
        return $this->manager->modifyVPS($this->instanceId, $serverConfig);
    }

    public function getBandwidth()
    {
        $data = Capsule::table('mod_upCloudVps_bandwidth');
        if (!empty(filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING))) {
            switch (filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING)) {
                case '24 Hours':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 DAY')
                        ->where('serviceId', $this->serviceid)
                        ->groupBy(Capsule::raw('day(created_at),hour(created_at),from_unixtime(FLOOR(UNIX_TIMESTAMP(created_at)/(15*60))*(15*60)) '));

                    break;
                case 'Week':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 WEEK')
                        ->where('serviceId', $this->serviceid)
                        ->groupBy(Capsule::raw('week(created_at),day(created_at), hour(created_at), from_unixtime(FLOOR(UNIX_TIMESTAMP(created_at)/(60*60))*(60*60))'));

                    break;
                case 'Month':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 MONTH')
                        ->where('serviceId', $this->serviceid)
                        ->groupBy(Capsule::raw('month(created_at),day(created_at)'));

                    break;
                case 'Year':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 YEAR')
                        ->where('serviceId', $this->serviceid)
                        ->groupBy(Capsule::raw('year(created_at),month(created_at),week(created_at)'));

                    break;
                default:
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 DAY')
                        ->where('serviceId', $this->serviceid)
                        ->groupBy(Capsule::raw('day(created_at),hour(created_at),from_unixtime(FLOOR(UNIX_TIMESTAMP(created_at)/(15*60))*(15*60))'));
            }
        }

        $data = $data->get();
        $output = [];
        foreach ($data as $index => $dat) {
            if ($index >= 0) {
                $output['IPv4'][] = ($data[$index]->IPv4 - $data[($index - 1)]->IPv4);
                $output['IPv6'][] = ($data[$index]->IPv6 - $data[($index - 1)]->IPv6);
                $output['labels'][] = $dat->created_at;
            }
        }

        return ['data' => $output];
    }
}
