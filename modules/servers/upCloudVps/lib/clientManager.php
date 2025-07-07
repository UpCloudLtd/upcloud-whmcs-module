<?php

namespace WHMCS\Module\Server\upCloudVps;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Module\Server\upCloudVps\upCloudVps;

class clientManager
{
    private $manager;
    private $params;
    public function __construct(array $params)
    {
        $this->manager = new upCloudVps($params);
        $this->params = $params;
    }

    public function getData($page)
    {
        try {
            $instanceId = $this->params['model']->serviceProperties->get('instanceId|instance Id');
            switch ($page) {
                case 'details':
                    $details = $this->manager->GetServer($instanceId);
                    if ($details['response_code'] == '200') {
                        $details = $details['response']['server'];
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
                            if ($temp['part_of_plan'] == 'yes' || $details['plan'] == 'custom') {
                                $details['osname'] = $temp['storage_title'];
                                $details['base_storage_size'] = $temp['storage_size'];
                                break;
                            }
                        }

                        $vnc = ($details['remote_access_enabled'] == 'yes') ? 'on' : 'off';
                        $remoteAccessHost = $details['remote_access_host'];
                        if (!empty($remoteAccessHost)) {
                            $resolvedHost = gethostbyname($remoteAccessHost);
                            $resolvedHost = ($resolvedHost !== $remoteAccessHost) ? $resolvedHost : $remoteAccessHost;
                        } else {
                            $resolvedHost = null;
                        }

                        $vncIp = $resolvedHost;
                        $data['timezones'] = $this->manager->getTimezones()['response']['timezones']['timezone'];
                        $data['details'] = [
                            'hostname' => $details['hostname'],
                            'ip' => $details->ip,
                            'uuid' => $details['uuid'],
                            'plan' => $details['plan'],
                            'template' => $details['osname'],
                            'diskSize' => $details['base_storage_size'],
                            'status' => $details['state'],
                            'location' => $details['zoneDescription'],
                            'vnc' => $vnc,
                            'vnc_host' => $vncIp,
                            'vnc_port' => $details['remote_access_port'],
                            'vnc_password' => $details['remote_access_password'],
                            'video_model' => $details['video_model'],
                            'nic_model' => $details['nic_model'],
                            'timezone' => $details['timezone'],
                            'boot_order' => $details['boot_order'],
                        ];
                        if (!empty($details['ip_addresses'])) {
                            foreach ($details['ip_addresses']['ip_address'] as $ip) {
                                if ($ip['access'] == 'public' && $ip['family'] == 'IPv4') {
                                    $data['details']['ip'] = $ip['address'];
                                }
                            }
                        }
                    }

                    break;
            }
        } catch (\Exception $e) {
            return [];
        }

        return $data;
    }
}
