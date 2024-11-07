<?php
namespace WHMCS\Module\Server\upCloudVps;
if (!defined("WHMCS")) {die("This file cannot be accessed directly");}

use WHMCS\View\Menu\Item as MenuItem;
use WHMCS\Module\Server\upCloudVps\upCloudVps;
use WHMCS\Module\Server\upCloudVps\ajaxAction;

class Helper
{
  public static function getLang()
  {
      $languageDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
      $config = isset($GLOBALS['CONFIG']) ? $GLOBALS['CONFIG'] : []; // Check if CONFIG is set
      $language = isset($_SESSION['Language']) ? $_SESSION['Language'] : (isset($config['Language']) ? $config['Language'] : 'english'); // Set default language to 'english' if not found
      $languageFile = file_exists($languageDir . $language . '.php') ? $language : 'english'; // Check if language file exists, otherwise default to English

      // Include English language file if it exists
      if (file_exists($languageDir . 'english.php')) {
          include $languageDir . 'english.php';
      }

      // Require the selected language file
      require $languageDir . $languageFile . '.php';

      return isset($_LANG) ? $_LANG : []; // Return the language array
  }

  public static function ajaxAction(array $params, string $action)
  {
      ob_clean();
      try {
          $manager = new ajaxAction($params);
          $_LANG = self::getLang();

          if (method_exists($manager, $action)) {
              $details = $manager->$action();
              if ($details['response']['error']['error_message']) {
                $results['result'] = 'failure';
                $results['message'] = $details['response']['error']['error_message'];
              } else {
                $results['message'] = (!empty($_LANG['ajax'][$action])) ? $_LANG['ajax'][$action] : $_LANG['ajax']['action']['success'];

                switch ($action) {
                  case "refreshServer":
                  $results['data']['details']['status'] = $details['response']['server']['state'];
                  $results['data']['details']['statusLang'] = $_LANG['status'][$details['response']['server']['state']];
                  break;
                  case "vncDetails":
                  $results['vnchost'] = $details['vnchost'];
                  $results['vncport'] = $details['vncport'];
                  break;
                  case "getIpAddresses":
                  $results = $details;
                  break;
                  case "getBandwidth":
                  $results = $details;
                  break;
                }
          }

          } else {
              $results['result'] = 'failure';
              $results['message'] = $_LANG['ajax']['action']['not_valid'];
          }

          echo json_encode($results);
          die;
      } catch (\Exception $e) {
          echo json_encode(['result' => 'failure', 'message' => $e->getMessage()]);
          die;
      }
  }

  public static function clientAreaPrimarySidebarHook(array $params)
  {
      add_hook('ClientAreaPrimarySidebar', 1, function (MenuItem $primarySidebar) use ($params) {
          $_LANG = Helper::getLang();
          $panel = $primarySidebar->getChild('Service Details Overview');
          if (is_a($panel, 'WHMCS\View\Menu\Item')) {
              $panel = $panel->getChild('Information');
              if (is_a($panel, 'WHMCS\View\Menu\Item')) {
                  $panel->setUri("clientarea.php?action=productdetails&id={$params['serviceid']}");
                  $panel->setAttributes([]);
              }
          }
      });
  }

}
