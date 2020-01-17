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

namespace ModulesGarden\upCloudVm;

use Httpful\Request;
use Httpful\Http;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Manager.
 *
 * @author ModulesGarden <contact@modulesgarden.com>
 *
 * @see https://www.modulesgarden.com/
 *
 * Used to call UpCloud API in order to create/manage server.
 */
class Manager
{
    /**
     * UpCloud Server ID.
     *
     * Example value: "00803EFA-2635-4CDE-863C-6929CDCFBF59".
     *
     * @var string
     */
    private $server;

    /**
     * WHMCS Params.
     *
     * @var array
     */
    private $params;

    /**
     * Constructor.
     *
     * Attach Upcloud Server ID and set Request template with WHMCS params.
     *
     * @param array $params WHMCS Params
     */
    public function __construct(array $params)
    {
        if ($params['serviceid'] != '') {
            $this->server = Capsule::table('mod_upCloudVm')->where('serviceId', $params['serviceid'])->value('serverId');
        }

        $this->params = $params;

        $template = Request::init()
            ->method(Http::POST)
            ->expectsJson()
            ->sendsJson()
            ->authenticateWith($params['serverusername'], $params['serverpassword']);

        Request::ini($template);
    }

    /**
     * Rest Call UpCloud Api and log in case of Exception.
     *
     * @param string $method requested Method
     * @param string $query  requested Query
     * @param array  $body   requested Body
     *
     * @throws \Exception in case of error occurs in api call
     *
     * @return array
     */
    private function callApi(string $method, string $query, array $body = [])
    {
        $endpoint = (!empty($this->params['serverhostname']) ? $this->params['serverhostname'] : $this->params['serverip']);
        $endpoint = ($this->params['serversecure'] != '' ? 'https' : 'http').'://'.$endpoint.'/1.3';

        try {
            switch ($method) {
                case 'get':
                    $response = Request::get($endpoint.$query);
                    break;
                case 'post':
                    $response = Request::post($endpoint.$query);
                    break;
                case 'put':
                    $response = Request::put($endpoint.$query);
                    break;
                case 'delete':
                    $response = Request::delete($endpoint.$query);
                    break;
            }

            if (!empty($body)) {
                $response->body(json_encode($body));
            }

            $response = $response->send();

            if (!empty($response->body->error->error_message)) {
                throw new \Exception($response->body->error->error_message);
            }

            return [
                'result' => 'success',
                'message' => $response->body->error->error_message,
                'data' => $response->body,
            ];
        } catch (\Exception $e) {
            logModuleCall(
                'upCloudVm',
                strtoupper($method),
                $endpoint.$query.PHP_EOL.((!empty($body)) ? json_encode($body, JSON_PRETTY_PRINT) : ''),
                $e->getMessage(),
                $e->getMessage(),
                []
            );
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create UpCloud Server using configurable options if set.
     * Otherwise use default options set in product config.
     * Attach SSH Key if sset in custom field.
     *
     * @return array
     */
    public function createServer()
    {
        $zone = ($this->params['configoptions']['Location'] == '') ? $this->params['configoption1'] : $this->params['configoptions']['Location'];
        $size = ($this->params['configoptions']['Storage'] == '') ? 10 : $this->params['configoptions']['Storage'];

        $hddAvaliable = ['fi-hel', 'sg-sin', 'uk-lon'];
        $tier = 'maxiops';

        foreach ($hddAvaliable as $zon) {
            if (strpos($zone, $zon) !== false) {
                $tier = 'hdd';
                break;
            }
        }

        if (empty($this->params['domain'])) {
            $this->params['domain'] = '127.0.0.1';
        }
        $body = [
            'server' => [
                'zone' => $zone,
                'title' => 'VM-'.$this->params['serviceid'],
                'hostname' => $this->params['domain'],
                'plan' => $this->params['configoption2'],
                'firewall' => 'off',
                'remote_access_type' => 'vnc',
                'storage_devices' => [
                    'storage_device' => [
                        'action' => 'clone',
                        'storage' => $this->params['configoption3'],
                        'title' => 'VM-'.$this->params['serviceid'].' Storage',
                        'size' => $size,
                        'tier' => $tier,
                    ],
                ],
            ],
        ];

        if (!empty($this->params['customfields']['SSHKey'])) {
            $body['server']['login_user'] = [
                'username' => 'root',
                'ssh_keys' => [
                    'ssh_key' => [
                        $this->params['customfields']['SSHKey'],
                    ],
                ],
            ];
        }

        if (!empty($this->params['customfields']['initialization'])) {
            $body['server']['user_data'] = $this->params['customfields']['initialization'];
        }

        return $this->callApi('post', '/server', $body);
    }

    /**
     * Stop Server and Upgrade plan using product confing.
     *
     * @return array
     */
    public function upgradePlan()
    {
        $this->stopServerAndWait();

        $body = ['server' => [
            'plan' => $this->params['configoption2'],
        ]];
        $result = $this->callApi('put', '/server/'.$this->server, $body);

        $this->startServer();

        return $result;
    }

    /**
     * Get server details.
     *
     * @return array
     */
    public function getServerDetails()
    {
        $this->checkServer();

        return $this->callApi('get', '/server/'.$this->server);
    }

    /**
     * Get server status for client area.
     *
     * @return array
     */
    public function refreshServer()
    {
        $lang = Helper::getLang();
        $details = $this->getServerDetails();
        $results['data']['details']['status'] = $details['data']->server->state;
        $vnc = $details['data']->server->remote_access_enabled;
        if ($vnc == 'yes') {
            $vnc = 'on';
        } else {
            $vnc = 'off';
        }
        $results['data']['details']['vnc'] = $vnc;
        $results['data']['details']['statusLang'] = strtoupper($lang['status'][$details['data']->server->state]);

        return $results;
    }

    /**
     * Hard stop server and wait untill stopped.
     * Used in termination and upgrade server.
     *
     * @throws \Exception if can't stop server
     */
    public function stopServerAndWait()
    {
        $this->checkServer();
        $result = $this->callApi('get', '/server/'.$this->server);
        $state = $result['data']->server->state;

        if ($state == 'started') {
            $body = [
                'stop_server' => [
                    'stop_type' => 'hard',
                ],
            ];
            $this->callApi('post', '/server/'.$this->server.'/stop', $body);
        }

        $times = 0;
        while ($state != 'stopped' && $times < 15) {
            sleep(2);
            $result = $this->callApi('get', '/server/'.$this->server);
            $state = $result['data']->server->state;
            ++$times;
        }

        if ($state != 'stopped') {
            throw new \Exception('Can not stop server');
        }
    }

    /**
     * Stop and Delete server as well as attached storage.
     *
     * @return array
     */
    public function terminateServer()
    {
        $this->stopServerAndWait();

        return $this->callApi('delete', '/server/'.$this->server.'?storages=1');
    }

    /**
     * Start server.
     *
     * @return array
     */
    public function startServer()
    {
        $this->checkServer();

        return $this->callApi('post', '/server/'.$this->server.'/start');
    }

    /**
     * Send soft stop server request.
     *
     * @return array
     */
    public function stopServer()
    {
        $this->checkServer();
        $body = [
            'stop_server' => [
                'stop_type' => 'soft',
                'timeout' => '30',
            ],
        ];

        return $this->callApi('post', '/server/'.$this->server.'/stop', $body);
    }

    /**
     * Send hard stop server request.
     *
     * @return array
     */
    public function forceStopServer()
    {
        $this->checkServer();
        $body = [
            'stop_server' => [
                'stop_type' => 'hard',
            ],
        ];

        return $this->callApi('post', '/server/'.$this->server.'/stop', $body);
    }

    /**
     * Send reboot server request.
     *
     * @return array
     */
    public function rebootServer()
    {
        $this->checkServer();
        $body = [
            'restart_server' => [
                'stop_type' => 'hard',
            ],
        ];

        return $this->callApi('post', '/server/'.$this->server.'/restart', $body);
    }

    /**
     * Check if requested server exists in WHMCS system.
     *
     * @throws \Exception if server not found in WHMCS system
     */
    private function checkServer()
    {
        if ($this->params['serviceid'] != '') {
            $this->server = Capsule::table('mod_upCloudVm')
                ->where('serviceId', $this->params['serviceid'])
                ->value('serverId');
        }

        if ($this->server == '') {
            throw new \Exception('Server not found');
        }
    }

    /**
     * Get avaliable zones.
     *
     * @return array
     */
    public function getZones()
    {
        return $this->callApi('get', '/zone');
    }

    /**
     * Get server template details.
     *
     * @return array
     */
    public function getTemplate()
    {
        return $this->callApi('get', '/storage/'.$this->params['configoption3']);
    }

    /**
     * Get avaliable templates.
     *
     * @return array
     */
    public function getTemplates()
    {
        return $this->callApi('get', '/storage/template');
    }

    /**
     * Get avaliable Timezones.
     *
     * @return array
     */
    public function getTimezones()
    {
        return $this->callApi('get', '/timezone');
    }

    /**
     * Get avaliable Plans.
     *
     * @return array
     */
    public function getPlans()
    {
        return $this->callApi('get', '/plan');
    }

    /**
     * Update Server Configuration.
     *
     * @return array
     */
    public function saveServerConfiguration()
    {
        $this->checkServer();
        $body = [
            'server' => [
                'hostname' => filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING),
                'boot_order' => filter_input(INPUT_POST, 'bootOrder', FILTER_SANITIZE_STRING),
                'video_model' => filter_input(INPUT_POST, 'displayAdapter', FILTER_SANITIZE_STRING),
                'nic_model' => filter_input(INPUT_POST, 'networkAdapter', FILTER_SANITIZE_STRING),
                'timezone' => filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING),
            ],
        ];

        return $this->callApi('put', '/server/'.$this->server, $body);
    }

    /**
     * Update VNC password.
     *
     * @return array
     */
    public function saveVNCConfiguration()
    {
        $this->checkServer();
        $body = [
            'server' => [
                'remote_access_password' => filter_input(INPUT_POST, 'vnc_password', FILTER_SANITIZE_STRING),
            ],
        ];

        return $this->callApi('put', '/server/'.$this->server, $body);
    }

    /**
     * Update VNC Status.
     *
     * @return array
     */
    public function changeVNCStatus()
    {
        $this->checkServer();
        $type = filter_input(INPUT_POST, 'vnc', FILTER_SANITIZE_STRING);
        $type = ($type == 'on') ? 'yes' : 'no';
        $body = [
            'server' => [
                'remote_access_enabled' => $type,
            ],
        ];

        return $this->callApi('put', '/server/'.$this->server, $body);
    }

    /**
     * Get Firewall traffic rules and returns data for datatable.
     *
     * @return array
     */
    public function getTrafficRules()
    {
        $this->checkServer();
        $rules = $this->callApi('get', '/server/'.$this->server.'/firewall_rule')['data']->firewall_rules->firewall_rule;
        foreach ($rules as $rule) {
            $output[] = [
                $rule->position,
                ucfirst($rule->direction),
                (empty($rule->protocol) ? 'Any' : strtoupper($rule->protocol)).' / '.$rule->family,

                (empty($rule->source_address_start) || empty($rule->source_address_end)) ? 'Any' : (($rule->source_address_start == $rule->source_address_end) ? $rule->source_address_start : $rule->source_address_start.' - '.$rule->source_address_end),

                (empty($rule->source_port_start) || empty($rule->source_port_end)) ? 'Any' : (($rule->source_port_start == $rule->source_port_end) ? $rule->source_port_start : $rule->source_port_start.' - '.$rule->source_port_end),

                (empty($rule->destination_address_start) || empty($rule->destination_address_end)) ? 'Any' : (($rule->destination_address_start == $rule->destination_address_end) ? $rule->destination_address_start : $rule->destination_address_start.' - '.$rule->destination_address_end),

                (empty($rule->destination_port_start) || empty($rule->destination_port_end)) ? 'Any' : (($rule->destination_port_start == $rule->destination_port_end) ? $rule->destination_port_start : $rule->destination_port_start.' - '.$rule->destination_port_end),
                ucfirst($rule->action),
                $rule->comment,
                '<button class="btn btn-danger" data-record-id="'.$rule->position.'" data-record-title="" data-toggle="modal" data-target="#confirm-delete"><i class="btn-icon fa fa-trash fa-lg"></i></button>',
            ];
        }

        $rules['data'] = (!empty($output)) ? $output : [];

        return $rules;
    }

    /**
     * Delete reqeusted rule.
     *
     * @throws \Exception if requested position is not valid int
     *
     * @return array
     */
    public function deleteRule()
    {
        if (!filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT)) {
            throw new \Exception('Position not valid');
        }

        $this->checkServer();

        return $this->callApi('delete', '/server/'.$this->server.'/firewall_rule/'.filter_input(INPUT_POST, 'position', FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Add reqeusted rule.
     *
     * @return array
     */
    public function addRule()
    {
        $this->checkServer();
        $body = [];
        foreach (filter_input_array(INPUT_POST) as $req => $val) {
            if (strpos($req, 'firewall_') !== false && $val != '') {
                $name = str_replace('firewall_', '', $req);
                $body['firewall_rule'][$name] = $val;
            }
        }

        if ($body['firewall_rule']['protocol'] == 'all') {
            unset($body['firewall_rule']['protocol']);
        }

        return $this->callApi('post', '/server/'.$this->server.'/firewall_rule', $body);
    }

    /**
     * Add DNS rules required by DNS server.
     *
     * @return array
     */
    public function addDNSRules()
    {
        $this->checkServer();

        $rules = $this->callApi('get', '/server/'.$this->server.'/firewall_rule')['data']->firewall_rules->firewall_rule;

        $toDelete = [];

        foreach ($rules as $rule) {
            if ($rule->source_address_start == '94.237.127.9' || $rule->source_address_start == '94.237.40.9'
                || $rule->source_address_start == '2a04:3540:53::1' || $rule->source_address_start == '2a04:3544:53::1') {
                $toDelete[] = $rule->position;
            }
        }

        rsort($toDelete);
        foreach ($toDelete as $pos) {
            $response = $this->callApi('delete', '/server/'.$this->server.'/firewall_rule/'.$pos);
        }

        $protocols = ['tcp', 'udp'];
        foreach ($protocols as $protocol) {
            $body = [
                'firewall_rule' => [
                    'protocol' => $protocol,
                    'direction' => 'in',
                    'action' => 'accept',
                    'family' => 'IPv4',
                    'source_port_start' => 53,
                    'source_port_end' => 53,
                    'source_address_start' => '94.237.127.9',
                    'source_address_end' => '94.237.127.9',
                    'comment' => 'Automatic rule',
                ],
            ];
            $response = $this->callApi('post', '/server/'.$this->server.'/firewall_rule', $body);
            $body['firewall_rule']['source_address_start'] = $body['firewall_rule']['source_address_end'] = '94.237.40.9';
            $response = $this->callApi('post', '/server/'.$this->server.'/firewall_rule', $body);
            $body['firewall_rule']['family'] = 'IPv6';
            $body['firewall_rule']['source_address_start'] = $body['firewall_rule']['source_address_end'] = '2a04:3540:53::1';
            $response = $this->callApi('post', '/server/'.$this->server.'/firewall_rule', $body);
            $body['firewall_rule']['source_address_start'] = $body['firewall_rule']['source_address_end'] = '2a04:3544:53::1';
            $response = $this->callApi('post', '/server/'.$this->server.'/firewall_rule', $body);
        }

        return $response;
    }

    /**
     * Get bandwidth data from database and returns data for charts.
     *
     * @return array
     */
    public function getBandwidth()
    {
        $data = Capsule::table('mod_upCloudVm_bandwidth');
        if (!empty(filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING))) {
            switch (filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING)) {
                case '24 Hours':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 DAY')
                        ->where('serviceId', $this->params['serviceid'])
                        ->groupBy(Capsule::raw('day(created_at),hour(created_at),from_unixtime(FLOOR(UNIX_TIMESTAMP(created_at)/(15*60))*(15*60)) '));
                    break;
                case 'Week':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 WEEK')
                        ->where('serviceId', $this->params['serviceid'])
                        ->groupBy(Capsule::raw('week(created_at),day(created_at), hour(created_at), from_unixtime(FLOOR(UNIX_TIMESTAMP(created_at)/(60*60))*(60*60))'));
                    break;
                case 'Month':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 MONTH')
                        ->where('serviceId', $this->params['serviceid'])
                        ->groupBy(Capsule::raw('month(created_at),day(created_at)'));
                    break;
                case 'Year':
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 YEAR')
                        ->where('serviceId', $this->params['serviceid'])
                        ->groupBy(Capsule::raw('year(created_at),month(created_at),week(created_at)'));
                    break;
                default:
                    $data->whereRaw('created_at >= NOW() - INTERVAL 1 DAY')
                        ->where('serviceId', $this->params['serviceid'])
                        ->groupBy(Capsule::raw('day(created_at),hour(created_at),from_unixtime(FLOOR(UNIX_TIMESTAMP(created_at)/(15*60))*(15*60))'));
            }
        }

        $data = $data->get();
        $output = [];
        foreach ($data as $index => $dat) {
            if ($index > 0) {
                $output['IPv4'][] = ($data[$index]->IPv4 - $data[($index - 1)]->IPv4);
                $output['IPv6'][] = ($data[$index]->IPv6 - $data[($index - 1)]->IPv6);
                $output['labels'][] = $dat->created_at;
            }
        }

        return ['result' => 'success', 'message' => '', 'data' => $output];
    }

    /**
     * Get IP Addresses and returns data for datatable.
     *
     * @return array
     */
    public function getIpAddresses()
    {
        $this->checkServer();

        $ips = $this->callApi('get', '/server/'.$this->server);
        $ips = $ips['data']->server->ip_addresses->ip_address;

        foreach ($ips as $ip) {
            $ipDetails = $this->callApi('get', '/ip_address/'.$ip->address);

            $output[] = [
                ucfirst($ip->access).' '.$ip->family,
                $ip->address,
                $ipDetails['data']->ip_address->ptr_record,
                '<button class="btn btn-primary editIp" data-toggle="modal" data-target="#editPtrModal" data-ip="'.$ip->address.'" data-ptr="'.$ipDetails['data']->ip_address->ptr_record.'"><i class="btn-icon fa fa-pencil fa-lg"></i></button>
                    <button class="btn btn-danger deleteIp" data-record-id="'.$ip->address.'" data-record-title="" data-toggle="modal" data-target="#confirm-delete"><i class="btn-icon fa fa-trash fa-lg"></i></button>',
            ];
        }

        $ips['data'] = (!empty($output)) ? $output : [];

        return $ips;
    }

    /**
     * Delete requested ip address.
     *
     * @return array
     */
    public function deleteIp()
    {
        $this->checkIp();

        return $this->callApi('delete', '/ip_address/'.filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING));
    }

    /**
     * Attach new ip address with requested type to server.
     *
     * @return array
     */
    public function addIp()
    {
        $body = [
            'ip_address' => [
                'family' => (filter_input(INPUT_POST, 'family', FILTER_SANITIZE_STRING) == 'Private') ? 'IPv4' : filter_input(INPUT_POST, 'family', FILTER_SANITIZE_STRING),
                'access' => (filter_input(INPUT_POST, 'family', FILTER_SANITIZE_STRING) == 'Private') ? 'private' : 'public',
                'server' => $this->server,
            ],
        ];

        return $this->callApi('post', '/ip_address', $body);
    }

    /**
     * Edit requested ip address.
     *
     * @return array
     */
    public function editIp()
    {
        $this->checkIp();
        $body = [
            'ip_address' => [
                'ptr_record' => filter_input(INPUT_POST, 'ptr', FILTER_SANITIZE_STRING),
            ],
        ];

        return $this->callApi('put', '/ip_address/'.filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING), $body);
    }

    /**
     * Validates requested ip.
     *
     * @throws \Exception if requested ip is not valid
     */
    public function checkIp()
    {
        if (!filter_input(INPUT_POST, 'ip', FILTER_VALIDATE_IP)) {
            throw new \Exception('IP not valid');
        }

        $response = $this->callApi('get', '/ip_address/'.filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING));
        if (!($response['data']->ip_address->server == $this->server)) {
            throw new \Exception('IP does not belong to your server');
        }
    }

    /**
     * Update Storage Rule.
     *
     * @return array
     */
    public function updateStorageRule()
    {
        $storage = $this->getServerDetails()['data']->server->storage_devices->storage_device[0]->storage;
        $body = [
            'storage' => [
                'backup_rule' => [
                    'interval' => filter_input(INPUT_POST, 'interval', FILTER_SANITIZE_STRING),
                    'time' => filter_input(INPUT_POST, 'time', FILTER_SANITIZE_NUMBER_INT),
                    'retention' => filter_input(INPUT_POST, 'retention', FILTER_SANITIZE_NUMBER_INT),
                ],
            ],
        ];

        return $this->callApi('put', '/storage/'.$storage, $body);
    }

    /**
     * Get storage details.
     *
     * @return array
     */
    public function getStorageDetails()
    {
        $storage = $this->getServerDetails()['data']->server->storage_devices->storage_device[0]->storage;

        return $this->callApi('get', '/storage/'.$storage);
    }

    /**
     * Get server's backups data and returns it for datatable.
     *
     * @return array
     */
    public function getBackups()
    {
        $this->checkServer();

        $response = $this->callApi('get', '/server/'.$this->server);
        $storageId = $response['data']->server->storage_devices->storage_device[0]->storage;

        $response = $this->callApi('get', '/storage/'.$storageId);
        $backups = $response['data']->storage->backups->backup;

        $index = 1;
        foreach ($backups as $back) {
            $res = $this->callApi('get', '/storage/'.$back);
            $backup = $res['data']->storage;
            if ($backup->origin == $storageId && !empty($storageId)) {
                $output[] = [
                    $index,
                    date('d-m-Y H:i:s', strtotime($backup->created)),
                    ucfirst($backup->access),
                    ucfirst($backup->state),
                    $backup->title,
                    $backup->size,
                    '<button class="btn btn-primary" data-toggle="modal" data-target="#confirm-restore" data-record-id="'.$backup->uuid.'" data-record-title="'.$index.'" ><i class="btn-icon fa fa-refresh fa-lg"></i></button>
                    <button class="btn btn-danger deleteBackup" data-record-id="'.$backup->uuid.'" data-record-title="'.$index.'" data-toggle="modal" data-target="#confirm-delete"><i class="btn-icon fa fa-trash fa-lg"></i></button>',
                ];
                ++$index;
            }
        }

        $backups['data'] = (!empty($output)) ? $output : [];

        return $backups;
    }

    /**
     * Delete requested backup.
     *
     * @return array
     */
    public function deleteBackup()
    {
        $this->checkBackup();

        return $this->callApi('delete', '/storage/'.filter_input(INPUT_POST, 'backup', FILTER_SANITIZE_STRING));
    }

    /**
     * Restore requested backup.
     *
     * @return array
     */
    public function restoreBackup()
    {
        $this->checkBackup();

        return $this->callApi('post', '/storage/'.filter_input(INPUT_POST, 'backup', FILTER_SANITIZE_STRING).'/restore');
    }

    /**
     * Create server backup.
     *
     * @return array
     */
    public function addBackup()
    {
        $this->checkServer();
        $response = $this->callApi('get', '/server/'.$this->server);
        $storageId = $response['data']->server->storage_devices->storage_device[0]->storage;

        $body = [
            'storage' => [
                'title' => 'Manually created backup',
            ],
        ];

        return $this->callApi('post', '/storage/'.$storageId.'/backup', $body);
    }

    /**
     * Check if requested backup belongs to server.
     *
     * @throws \Exception if requested backup does not belong to server
     */
    private function checkBackup()
    {
        $response = $this->callApi('get', '/server/'.$this->server);
        $storageId = $response['data']->server->storage_devices->storage_device[0]->storage;
        $response = $this->callApi('get', '/storage/'.$storageId);
        $backups = $response['data']->storage->backups->backup;
        $allow = false;

        foreach ($backups as $back) {
            if ($back == filter_input(INPUT_POST, 'backup', FILTER_SANITIZE_STRING)) {
                $allow = true;
                break;
            }
        }

        if (!$allow) {
            throw new \Exception('Backup does not belong to your server');
        }
    }

    /**
     * Get account's details.
     */
    public function testConnection()
    {
        $this->callApi('get', '/account');
    }
}
