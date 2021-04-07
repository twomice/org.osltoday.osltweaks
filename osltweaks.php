<?php

require_once 'osltweaks.civix.php';
use CRM_Osltweaks_ExtensionUtil as E;

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
