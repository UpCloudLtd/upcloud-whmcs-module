<?php

//general
$_LANG['manageVM']                    = "Manage VM";
$_LANG['generalError']                = "Something has gone wrong. Contact the administrator.";
$_LANG['ajax']['action']['not_valid'] = 'Action not valid';
$_LANG['ajax']['action']['success']   = 'Action completed successfully';
$_LANG['ajax']['unknown']             = 'Unknown error';
$_LANG['ajax']['startServer']         = 'The container has been booted successfully';
$_LANG['ajax']['rebootServer']        = 'Reboot signal has been sent successfully';
$_LANG['ajax']['stopServer']          = 'Stop signal has been sent successfully';
$_LANG['ajax']['forceStopServer']     = 'Shutdown signal has been sent successfully';
$_LANG['notFound']                    = 'Not Found';

$_LANG['status']['started']     = 'Started';
$_LANG['status']['stopped']     = 'Stopped';
$_LANG['status']['maintenance'] = 'Maintenance';
$_LANG['status']['error']       = 'Error';

//overwiew
$_LANG['overview']['details']  = "Server Details";
$_LANG['overview']['hostname'] = "Hostname";
$_LANG['overview']['ip']       = "IP";
$_LANG['overview']['uuid']     = "Uuid";
$_LANG['overview']['template'] = "Template";
$_LANG['overview']['plan']     = "Plan";
$_LANG['overview']['status']   = "Status";

$_LANG['vps']['shutdown']         = 'Shutdown';
$_LANG['vps']['stop']             = 'Stop';
$_LANG['vps']['reboot']           = 'Reboot';
$_LANG['vps']['console']          = 'VNC Console';
$_LANG['vps']['control_panel']    = 'Control Panel';
$_LANG['vps']['additional_tools'] = 'Additional Tools';

$_LANG['vps']['status'] = 'Status';
$_LANG['vps']['boot']   = 'Boot';
$_LANG['vps']['novnc']  = 'NO-VNC Console';

//server
$_LANG['server']['title']                 = 'Server Configuration';
$_LANG['server']['description']           = 'Your Server configuration can be updated here';
$_LANG['server']['edit']                  = 'Edit Server Configuration';
$_LANG['server']['hostname']              = 'Hostname';
$_LANG['server']['display']               = 'Display Adapter';
$_LANG['server']['network']               = 'Network Adapter';
$_LANG['server']['timezone']              = 'Timezone';
$_LANG['server']['boot']                  = 'Boot Order';
$_LANG['server']['save']                  = 'Save Configuration';
$_LANG['ajax']['saveServerConfiguration'] = "Server Configuration has been saved successfully";

//backups
$_LANG['backups']['title']          = 'Backups Management';
$_LANG['backups']['description']    = 'Secure your system by creating backups. In case of any failure, you can restore your server to one of the previously created backups.';
$_LANG['backups']['list']           = 'Backups List';
$_LANG['backups']['created']        = 'Created';
$_LANG['backups']['access']         = 'Access';
$_LANG['backups']['state']          = 'State';
$_LANG['backups']['titl']           = 'Title';
$_LANG['backups']['size']           = 'Size';
$_LANG['backups']['add']            = 'Create New Backup';
$_LANG['ajax']['addBackup']         = "New backup has been created successfully";
$_LANG['ajax']['deleteBackup']      = "Selected backup has been deleted successfully";
$_LANG['ajax']['restoreBackup']     = "Restore signal has been sent for selected backup";
$_LANG['ajax']['updateStorageRule'] = "Configuration saved successfully";

$_LANG['backups']['schedule'] = 'Schedule backups';
$_LANG['backups']['take']     = 'Take backup';
$_LANG['backups']['time']     = 'Time';
$_LANG['backups']['delete']   = 'Delete';
$_LANG['backups']['save']     = 'Save Configuration';

$_LANG['backups']['daily']        = 'Daily';
$_LANG['backups']['onMondays']    = 'On Mondays';
$_LANG['backups']['onTuesdays']   = 'On Tuesdays';
$_LANG['backups']['onWednesdays'] = 'On Wednesdays';
$_LANG['backups']['onThursdays']  = 'On Thursdays';
$_LANG['backups']['onFridays']    = 'On Fridays';
$_LANG['backups']['onSaturdays']  = 'On Saturdays';
$_LANG['backups']['onSundays']    = 'On Sundays';

$_LANG['backups']['oneDay']    = 'After One day';
$_LANG['backups']['twoDays']   = 'After Two Days';
$_LANG['backups']['threeDays'] = 'After Three Days';
$_LANG['backups']['fourDays']  = 'After Four Days';
$_LANG['backups']['fiveDays']  = 'After Five Days';
$_LANG['backups']['sixDays']   = 'After Six Days';

$_LANG['backups']['oneWeek']    = 'After One Week';
$_LANG['backups']['twoWeeks']   = 'After Two Weeks';
$_LANG['backups']['threeWeeks'] = 'After Three Weeks';

$_LANG['backups']['oneMonth']    = 'After One Month';
$_LANG['backups']['twoMonths']   = 'After Two Months';
$_LANG['backups']['threeMonths'] = 'After Three Months';
$_LANG['backups']['fourMonths']  = 'After Four Months';
$_LANG['backups']['fiveMonths']  = 'After Five Months';
$_LANG['backups']['sixMonths']   = 'After Six Months';

$_LANG['backups']['oneYear']    = 'After One Year';
$_LANG['backups']['twoYears']   = 'After Two Years';
$_LANG['backups']['threeYears'] = 'After Three Years';

$_LANG['backups']['notset'] = '( Save to enable )';
//network
$_LANG['network']['title']       = 'Network Management';
$_LANG['network']['description'] = 'Request a new IP address for this server. You can attach a maximum of five public IPv4 and IPv6 addresses to your server. You can attach only one private IPv4 address';
$_LANG['ajax']['deleteIp']       = 'Selected IP Address has been deleted successfully';
$_LANG['ajax']['addIp']          = 'New IP Address has been attached successfully';
$_LANG['ajax']['editIp']         = 'Selected IP Address has been edited successfully';
$_LANG['ip']['addIPv4']          = 'Add Public IPv4 Address';
$_LANG['ip']['addIPv6']          = 'Add Public IPv6 Address';
$_LANG['ip']['addPrivate']       = 'Add Private-network IPv4';
$_LANG['ip']['addresses']        = 'IP Addresses';
$_LANG['ip']['category']         = 'Category';
$_LANG['ip']['address']          = 'IP Address';
$_LANG['ip']['access']           = 'Access';
$_LANG['ip']['family']           = "Family";
$_LANG['ip']['rdn']              = 'Reverse DNS Name';
$_LANG['ip']['editRdn']          = 'Edit Reverse Dns Name for: ';
$_LANG['ip']['close']            = 'Close';
$_LANG['ip']['save']             = 'Save Changes';

///vnc
$_LANG['vnc']['title']                 = 'VNC Configuration';
$_LANG['vnc']['description']           = 'The console provides server management as if your screen and keyboard would be plugged into the server. The console connection is particularly useful when logging into the server is not possible using normal remote connection methods due to an OS error state or faulty firewall rules, etc.';
$_LANG['vnc']['settings']              = 'VNC Configuration';
$_LANG['vnc']['status']                = 'VNC Status';
$_LANG['vnc']['password']              = 'VNC Password';
$_LANG['vnc']['address']               = 'VNC Address';
$_LANG['vnc']['port']                  = 'VNC Port';
$_LANG['vnc']['disable']               = 'Disable VNC';
$_LANG['vnc']['enable']                = 'Enable VNC';
$_LANG['vnc']['save']                  = 'Save Settings';
$_LANG['vnc']['on']                    = 'On';
$_LANG['vnc']['off']                   = 'Off';
$_LANG['ajax']['saveVNCConfiguration'] = "VNC Configuration has been saved successfully";
$_LANG['ajax']['changeVNCStatus']      = "VNC Status has been changed successfully";

//bandwidth
$_LANG['bandwidth']['title']       = 'Bandwidth Graphs';
$_LANG['bandwidth']['description'] = 'Your Server usage over time can be viewed here.';
///firewall
$_LANG['firewall']['title']                     = 'Firewall Management';
$_LANG['firewall']['description']               = 'UpCloud’s firewall makes it easy to limit server traffic by IP address or port. In the network topology the L3 firewall is located just before the network interfaces, so that all network traffic passes through the firewall, ensuring the best possible protection. Firewall usage is charged according to the price list.The firewall evaluates rules on a first match basis from top to bottom. If none of the rules apply to a packet travelling through the firewall, the default rule is applied.';
$_LANG['firewall']['dns_message_1']             = 'Please note that DNS traffic must also be explicitly allowed.';
$_LANG['firewall']['dns_message_2']             = 'Our DNS servers are 94.237.127.9 and 94.237.40.9 (in IPv6 network 2a04:3540:53::1 and 2a04:3544:53::1). Incoming TCP and UDP traffic from source port 53 must be allowed from these hosts.';
$_LANG['firewall']['direction']                 = 'Dir';
$_LANG['firewall']['rules']                     = 'Traffic Rules';
$_LANG['firewall']['protocol']                  = 'Protocol';
$_LANG['firewall']['source_address']            = 'Source Address';
$_LANG['firewall']['source_port']               = 'Source Port';
$_LANG['firewall']['target_address']            = 'Target Address';
$_LANG['firewall']['target_port']               = 'Target Port';
$_LANG['firewall']['action']                    = 'Action';
$_LANG['firewall']['destination_address_start'] = 'Destination address start';
$_LANG['firewall']['destination_address_end']   = 'Destination address end';
$_LANG['firewall']['destination_port_start']    = 'Destination port start';
$_LANG['firewall']['destination_port_end']      = 'Destination port end';
$_LANG['firewall']['source_address_start']      = 'Source address start';
$_LANG['firewall']['source_address_end']        = 'Source address end';
$_LANG['firewall']['source_port_start']         = 'Source port start';
$_LANG['firewall']['source_port_end']           = 'Source port end';
$_LANG['firewall']['family']                    = 'Family';
$_LANG['firewall']['icmp_type']                 = 'Icmp type';
$_LANG['firewall']['comment']                   = 'Comment';
$_LANG['firewall']['add']                       = 'Add Rule';
$_LANG['firewall']['add_dns']                   = 'Auto-add UpCloud DNS rules';
$_LANG['firewall']['add_rule']                  = 'Add: Rule';
$_LANG['firewall']['close']                     = 'Close';
$_LANG['ajax']['deleteRule']                    = 'Rule has been deleted successfully';
$_LANG['ajax']['addRule']                       = 'Rule has been added successfully';
$_LANG['ajax']['addDNSRules']                   = 'DNS rules have been added successfully';

$_LANG['all']['cancel']        = 'Cancel';
$_LANG['all']['delete']        = 'Delete';
$_LANG['all']['actions']       = 'Actions';
$_LANG['all']['confirmDelete'] = 'Confirm Delete';
$_LANG['all']['confirm1']      = 'You are about to delete';
$_LANG['all']['confirm2']      = 'record, this procedure is irreversible.';
$_LANG['all']['proceed']       = 'Do you want to proceed?';

$_LANG['backups']['confirmRestore'] = 'Confirm Restore';
$_LANG['backups']['confirm1']       = 'You are about to restore';
$_LANG['backups']['confirm2']       = 'record.';
$_LANG['backups']['restore']        = 'Restore';