<?php
namespace WHMCS\Module\Server\upCloudVps;
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}
use WHMCS\Database\Capsule;

define('MODULE_VERSION', '2.0.0-pre');

class upCloudVps
{
  private $curl;
  private $baseUrl;
  private $httpHeader = [];

  public function __construct(array $params)
  {
      $user = $params['serverusername'];
      $password = $params['serverpassword'];
      $this->baseUrl = 'https://api.upcloud.com/1.3/';
      $this->curl = curl_init();
      curl_setopt_array($this->curl, [
        CURLOPT_USERAGENT => 'upcloud-whmcs-module/' . MODULE_VERSION,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $user . ':' . $password,
      ]);
  }

  public function __destruct()
  {
      curl_close($this->curl);
  }

  protected function setHttpHeader($name, $value)
  {
      $this->httpHeader[$name] = $value;
  }

  protected function executeRequest($method, $url, $data = null)
  {
      curl_setopt($this->curl, CURLOPT_URL, $this->baseUrl . $url);
      curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);

      if ($data !== null) {
          curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
          $this->setHttpHeader('Content-Type', 'application/json');
      }

      // Set HTTP headers
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, array_map(
          function ($key, $value) {
              return "$key: $value";
          },
          array_keys($this->httpHeader),
          $this->httpHeader
      ));

      $response = curl_exec($this->curl);
      $statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

      if ($response === false) {
          throw new \Exception('Curl error: ' . curl_error($this->curl));
      }

      logModuleCall(
          'upCloudVps',
          strtoupper($method),
          $url.PHP_EOL.((!empty($data)) ? json_encode($data, JSON_PRETTY_PRINT) : ''),
          json_decode($response, true),
          json_decode($response, true),
          []
      );

        return ['response_code' => $statusCode,  'response' => json_decode($response, true)];
  }

  public function get($url)
  {
      return $this->executeRequest('GET', $url);
  }

  public function post($url, $data = null)
  {
      return $this->executeRequest('POST', $url, $data);
  }

  public function put($url, $data = null)
  {
      return $this->executeRequest('PUT', $url, $data);
  }

  public function delete($url, $data = null)
  {
      return $this->executeRequest('DELETE', $url, $data);
  }



  //Get Account Info
  public function GetAccountInfo()
  {
      return $this->get('account');
  }

  //List prices
  public function GetPrices()
  {
      return $this->get('price');
  }

  //List available zones
  public function GetZones()
  {
      return $this->get('zone');
  }

  //List timezones
  public function GetTimezones()
  {
      return $this->get('timezone');
  }

  //List available plans
  public function GetPlans()
  {
      return $this->get('plan');
  }

  //List server configurations
  public function GetServerConfigurations()
  {
      return $this->get('server_size');
  }

  //List servers
  public function GetAllServers()
  {
      return $this->get('server');
  }

  //Get server details
  public function GetServer($ServerUUID)
  {
      return $this->get('server/' . $ServerUUID);
  }

  //Create server - Getting templates
  public function GetTemplate()
  {
      return $this->get('storage/template');
  }

  //Create server - Creating from a template
  public function CreateServer($ZoneID, $Hostname, $Plan, $OsUUID, $sshKey, $user_data, $backup, $networking, $ram = null, $vcpu = null, $storage = null)
{
$Templates = $this->GetTemplate()['response']['storages']['storage'];
foreach ($Templates as $Template){
if ($Template['uuid'] == $OsUUID){
$TemplateTitle = $Template['title'];
$TemplateUUID = $Template['uuid'];
$template_type = $Template['template_type']; #44
break;
}
}

if(($Plan == "custom") && isset($ram) && isset($vcpu) && isset($storage)){

  $postData = [
      'server' => [
          'metadata' => 'yes',
          'zone' => $ZoneID, // GetZones()
          'title' => $Hostname, // hostname
          'hostname' => $Hostname, // hostname
          'remote_access_enabled' => 'yes',
          //"simple_backup" => "0430,weeklies",
          "core_number" => (int)$vcpu,
          "memory_amount" => (int)"1024" * (int)$ram,
          'storage_devices' => [
              'storage_device' => [
                  [
                      'action' => 'clone',
                      'storage' => $TemplateUUID, // GetTemplates()
                      'size' => $storage,
                      'tier' => "maxiops",
                      'title' => $TemplateTitle, // OS Name
                  ]
              ]
          ]
      ]
  ];

} else {

  $AllPlans = $this->Getplans()['response']['plans']['plan'];
  foreach ($AllPlans as $Plans){
  if ($Plans['name'] == $Plan){
  $PlanName = $Plans['name'];
  $PlanSize = $Plans['storage_size'];
  $PlanTier = $Plans['storage_tier'];
  break;
  }
  }

  $postData = [
      'server' => [
          'metadata' => 'yes',
          'zone' => $ZoneID, // GetZones()
          'title' => $Hostname, // hostname
          'hostname' => $Hostname, // hostname
          'plan' => $PlanName, // Getplans()
        //  "simple_backup" => "0430,weeklies",
        'remote_access_enabled' => 'yes',
          'storage_devices' => [
              'storage_device' => [
                  [
                      'action' => 'clone',
                      'storage' => $TemplateUUID, // GetTemplates()
                      'size' => $PlanSize, // storage_size from Getplans()
                      'tier' => $PlanTier, // storage_tier from Getplans()
                      'title' => $TemplateTitle, // OS Name
                  ]
              ]
          ]
      ]
  ];

}
#44
if ($template_type == 'native') {
  if(!preg_match('/Windows/', $TemplateTitle)){
    if($sshKey != "na"){
      $postData['server']['login_user'] = [
          'username' => 'root',
          'ssh_keys' => [
              'ssh_key' => [
                  $sshKey,
              ],
          ],
      ];
    }
  }
}

if ($template_type == 'cloud-init') {
  if($sshKey != "na"){
    $postData['server']['login_user'] = [
        'username' => 'root',
        'ssh_keys' => [
            'ssh_key' => [
                $sshKey,
            ],
        ],
    ];
  } else {
    $error['response']['error']['error_message'] = 'ssh key required';
    return $error;
  }
}
#44

if($networking == "ipv4only"){
  $postData['server']['networking'] = [
    "interfaces" => [
        "interface" => [
            [
                "ip_addresses" => [
                    "ip_address" => [
                        [
                            "family" => "IPv4"
                        ]
                    ]
                ],
                "type" => "public"
            ]
        ]
    ]
];
}

if ($user_data != "na") {
    $postData['server']['user_data'] = $user_data;
}

if ($backup != "no") {
    $postData['server']['simple_backup'] = "0100,".$backup;
}

  return $this->post('server', $postData);
}

public function serverOperation($action, $ServerUUID, $stop_type = null) {
    $data = array();

    if ($stop_type !== null) {
        $data[$action.'_server']['stop_type'] = $stop_type;
        $data[$action.'_server']['timeout'] = "60";
    }
    return $this->post('server/'.$ServerUUID.'/'.$action, $data);
}

public function StartServer($ServerUUID) {
    return $this->serverOperation('start', $ServerUUID);
}

public function StopServer($ServerUUID) {
    return $this->serverOperation('stop', $ServerUUID, 'hard');
}

public function RestartServer($ServerUUID) {
    return $this->serverOperation('restart', $ServerUUID, 'hard');
}

public function CancelServer($ServerUUID) {
    return $this->serverOperation('cancel', $ServerUUID);
}

public function DeleteServer($ServerUUID) {
    return $this->delete('server/'.$ServerUUID);
}

public function ModifyServer($uuid, $Plan)
{
  $this->stopServerAndWait($uuid);

  $allPlans = $this->Getplans()['response']['plans']['plan'];
  foreach ($allPlans as $plan) {
    if ($plan['name'] == $Plan) {
        $planSize = $plan['storage_size'];
        break;
    }
}

  $upgradePlan = $this->put('server/' . $uuid, ['server' => ['plan' => $Plan]]);

  if($upgradePlan['response']['error']['error_message']){
    return $upgradePlan;
  } else {
    $storages = $this->GetServer($uuid)['response']['server']['storage_devices']['storage_device'];
    foreach ($storages as $storage) {
      if ($storage['part_of_plan'] == "yes") {
          $storageId = $storage['storage'];
          $existingStorageSize = $storage['storage_size'];
          break;
      }
    }
    if ($storageId && $planSize > $existingStorageSize) {
      $modeyStorage =  $this->modifyStorage($storageId, $planSize);
      $this->StartServer($uuid);
      return $modeyStorage;
    }
  }

}


public function modifyStorage($storageId, $planSize)
{
  $body = [
      'storage' => [
          'size' => $planSize
      ]
  ];
return $this->put('storage/'.$storageId, $body);
}

//Delete server and storage
 public function DeleteServernStorage($ServerUUID)
{
  $this->stopServerAndWait($ServerUUID);
return $this->delete('server/'.$ServerUUID.'?storages=1');
}

public function DeleteServernStorageBackup($ServerUUID)
{
 $this->stopServerAndWait($ServerUUID);
return $this->delete('server/'.$ServerUUID.'?storages=1&backups=delete');
}

public function stopServerAndWait($ServerUUID)
{
    $result = $this->GetServer($ServerUUID);
    $state = $result['response']['server']['state'];

    if ($state == 'started') {
        $this->StopServer($ServerUUID);
    }

    $times = 0;
    // If VM delete takes time please increase it but when tested working within 45 second
    while ($state != 'stopped' && $times < 45) {
        sleep(2);
        $result = $this->GetServer($ServerUUID);
        $state = $result['response']['server']['state'];
        ++$times;
    }

    if ($state != 'stopped') {
        throw new \Exception('Can not stop server, taking time to response');
    }
}

public function GetIPaddress( $IPAddress )
{
return $this->get('ip_address/'.$IPAddress);
}

public function ModifyIPaddress($instanceId, $IP, $ptr_record)
{
  if (!($this->GetIPaddress($IP)['response']['ip_address']['server'] == $instanceId)) {
      throw new \Exception('IP does not belong to your server');
  }
return $this->put('ip_address/'.$IP, array('ip_address' => array('ptr_record' => $ptr_record)));
}


public function vncPasswordUpdate($instanceId, $vncPass)
{
return $this->put('server/'.$instanceId, array('server' => array('remote_access_password' => $vncPass)));
}

public function vncEnableDisable($instanceId, $vncType)
{
return $this->put('server/'.$instanceId, array('server' => array('remote_access_enabled' => $vncType)));
}

public function modifyVPS($instanceId, $serverConfig)
{
return $this->put('server/'.$instanceId, $serverConfig);
}

public function formatSizeBytestoTB($bytes)
{
 return   round($bytes / 1024 / 1024 / 1024 / 1024,2);
}

public function formatSizeBytestoMB($bytes)
{
 return   round($bytes / 1024 / 1024, 2);
}

public function formatSizeBytestoGB($bytes)
{
 return   round($bytes / 1024 / 1024 / 1024, 2);
}

public function formatSizeMBtoGB($MB)
{
 return   round($MB / 1024);
}

public function formatBytes($bytes, $precision = 2) {
$unit = ["B", "KB", "MB", "GB", "TB"];
$exp = floor(log($bytes, 1024)) | 0;
return round($bytes / (pow(1024, $exp)), $precision).' '.$unit[$exp];
}

public function CurrencyConvert($fromCurrency, $toCurrency, $amount)
{
$amount_from_db = Capsule::table('tblcurrencies')->where('code', $toCurrency)->get();
$exhangeRate_whmcs = json_decode($amount_from_db, true)[0]['rate'];
$convertedAmount = $amount * $exhangeRate_whmcs;
return array(  'convertedAmount' => round($convertedAmount, 2) );
}


}
