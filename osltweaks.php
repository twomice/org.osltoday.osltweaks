<?php

require_once 'osltweaks.civix.php';
use CRM_Osltweaks_ExtensionUtil as E;

function osltweaks_civicrm_dashboard( $contactID, &$contentPlacement = self::DASHBOARD_BELOW ) {
  // Insert some HTML code which used to be in overridden templates, as found on the live site.
  $content = array(
    'CiviCRM Quick Links' => '
      <div>
        <div style="float:left" class="container1"> <ul class="indented">
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/dashboard&reset=1">CiviCRM Home</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/search&reset=1">Find Contacts</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/group&reset=1">Manage Groups</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/import/contact&reset=1">Import</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contribute&reset=1">CiviContribute</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/pledge&reset=1">CiviPledge</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/mailing/send&reset=1">CiviMail</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/member&reset=1">CiviMember</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/event&reset=1">CiviEvent</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/report/list&reset=1">CiviReport</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/admin&reset=1">Administer CiviCRM</a></li>

          </ul>
        </div>

        <div style="float:left" class="container2">
          <ul>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/add&reset=1&ct=Individual" accesskey="I">New Individual</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/add&reset=1&ct=Organization" accesskey="O">New Organization</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/add&reset=1&ct=Household" accesskey="H">New Household</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/activity&reset=1&action=add&context=standalone" accesskey="A">New Activity</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/group/add&reset=1" accesskey="G">New Group</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/user&reset=1" accesskey="">My Contact Dashboard</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/search/custom&csid=26&reset=1">Search For New Members</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/search/custom&csid=22&reset=1">Sharing Magazine Subscriber List</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/search/custom&csid=23&reset=1">Gift membership/Pending status</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/search/custom&csid=24&reset=1">Gift membership Contacts</a></li>
            <li class="collapsed"><a href="?page=CiviCRM&q=civicrm/contact/search/custom&csid=25&reset=1">Membership Expired Status</a></li>
          </ul>
        </div>


      </div>
      <div class="clear"></div>

    ',
  );
  return $content;
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_buildForm/
 */
function osltweaks_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
    $showCMS = CRM_Core_Smarty::singleton()->get_template_vars('showCMS');
    if ($showCMS) {
      // On contribution pages where users would see cms-user-help section,
      // include some javascript to finagle the wording in that section.
      CRM_Core_Resources::singleton()->addScriptFile('org.osltoday.osltweaks', 'js/CRM_Contribute_Form_Contribution_Main-isShowCMS.js');
    }

    // Get extension settings.
    $settings = CRM_Core_BAO_Setting::getItem(NULL, 'com.joineryhq.osltweaks');

    // Check if contribution page id is match on the 'us_only_page_ids' setting;
    // if  so, make appropriate changes.
    if (in_array($form->_id, ($settings['us_only_page_ids'] ?? array()))) {
      if ($form->elementExists('country-1')) {
        // Remove non-US countries.
        $elCountry = $form->getElement('country-1');
        $elCountryOptions = & $elCountry->_options;
        foreach ($elCountryOptions as $key => $option) {
          if ($option['attr']['value'] != 1228) {
            unset($elCountryOptions[$key]);
          }
        }

        // Rewrite State/Province label to State
        $elState = $form->getElement('state_province-1');
        $elState->_label = 'State';

        // If exisitng value of country is not US, set Country to US and delete value of State.
        if ($elCountry->_values[0] != 1228) {
          $elCountry->_values[0] = 1228;
          unset($elState->_values[0]);
        }
      }
    }

    // If contribution page id exist in $settings['member_redirect_contribution_pages'] array,
    // and if we can get current contact id (user is logged in).
    if (
      array_key_exists($form->_id, ($settings['member_redirect_contribution_pages'] ?? array()))
      && $currentContactId = CRM_Core_Session::singleton()->getLoggedInContactID()
    ) {
      // Get memberships count base on the current contact id
      $membershipCount = civicrm_api3('Membership', 'getcount', [
        'contact_id' => $currentContactId,
      ]);

      // Redirect if there are results on memberships api
      if ($membershipCount) {
        // Parse current URL to get the current q
        parse_str(html_entity_decode(parse_url($form->controller->_entryURL, PHP_URL_QUERY)), $parseDecodedUrl);
        // Get the redirect id in $settings['member_redirect_contribution_pages'] base on $form->_id
        $redirectFormId = $settings['member_redirect_contribution_pages'][$form->_id];
        CRM_Utils_System::redirect(CRM_Utils_System::url($parseDecodedUrl['q'], "reset=1&id={$redirectFormId}"));
      }
    }
  }
}

/**
 * Implements hook_civicrm_pageRun().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_pageRun/
 */
function osltweaks_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  if ($pageName == 'CRM_Contact_Page_View_UserDashBoard') {
    // add CiviCRM core resources.
    _osltweaks_addCoreResources();
    $resource = CRM_Core_Resources::singleton();
    $resource->addScriptFile('org.osltoday.osltweaks', 'js/CRM_Contact_Page_View_UserDashBoard.js');
    $resource->addStyleFile('org.osltoday.osltweaks', 'css/CRM_Contact_Page_View_UserDashBoard.css');

    // Get memberships for this contact and pass relevant data to js settings.
    $memberships = civicrm_api3('Membership', 'get', ['contact_id' => $page->_contactId]);
    $settings = ['memberships' => $memberships['values']];
    $resource->addVars('osltweaks', $settings);
  }
}

/**
 * Because User Dashboard shortcode doesn't addCoreResources (reference
 * https://lab.civicrm.org/dev/wordpress/-/issues/97) we have to include the
 * necessary resources manually.
 */
function _osltweaks_addCoreResources() {
  $resource = CRM_Core_Resources::singleton();
  $items = [
    "js/noconflict.js",
    "bower_components/jquery/dist/jquery.js",
    "bower_components/jquery-ui/jquery-ui.js",
    "bower_components/lodash-compat/lodash.js",
  ];
  foreach ($items as $item) {
    if (substr($item, -4) == '.css') {
      $resource->addStyleFile('civicrm', $item, [
        'weight' => 9999,
        'translate' => FALSE,
      ]);
    }
    else {
      $resource->addScriptFile('civicrm', $item, [
        'weight' => 9999,
        'translate' => FALSE,
      ]);
    }
  }    
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function osltweaks_civicrm_config(&$config) {
  _osltweaks_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function osltweaks_civicrm_xmlMenu(&$files) {
  _osltweaks_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function osltweaks_civicrm_install() {
  _osltweaks_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function osltweaks_civicrm_postInstall() {
  _osltweaks_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function osltweaks_civicrm_uninstall() {
  _osltweaks_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function osltweaks_civicrm_enable() {
  _osltweaks_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function osltweaks_civicrm_disable() {
  _osltweaks_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function osltweaks_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _osltweaks_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function osltweaks_civicrm_managed(&$entities) {
  _osltweaks_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function osltweaks_civicrm_caseTypes(&$caseTypes) {
  _osltweaks_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function osltweaks_civicrm_angularModules(&$angularModules) {
  _osltweaks_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function osltweaks_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _osltweaks_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function osltweaks_civicrm_entityTypes(&$entityTypes) {
  _osltweaks_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function osltweaks_civicrm_themes(&$themes) {
  _osltweaks_civix_civicrm_themes($themes);
}
