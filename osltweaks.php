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
    // add CiviCRM core resources (use our own function, so we cqn avoid
    // loading civicrm css.
    _osltweaks_addCoreResources('page-footer', ['js', 'settings']);
    CRM_Core_Resources::singleton()->addScriptFile('org.osltoday.osltweaks', 'js/CRM_Contact_Page_View_UserDashBoard.js');
    CRM_Core_Resources::singleton()->addStyleFile('org.osltoday.osltweaks', 'css/CRM_Contact_Page_View_UserDashBoard.css');
  }
}

/**
 * Copied from CRM_Core_Resources::addCoreResources(), modified to allow including
 * only some core resources (e.g., exclude css)
 *
 * @param string $region
 *   location within the file; 'html-header', 'page-header', 'page-footer'.
 * * @param array $types Which resources to add: any of 'css', 'settings, 'js'
 */
function _osltweaks_addCoreResources($region, $types = ['settings', 'css', 'js']) {
  $resource = CRM_Core_Resources::singleton();
  // Skip this, because it may prevent CRM_Core_Resources::addCoreResources()
  // from actually adding all resources if called from another context.
  //
  // $resource->addedCoreResources[$region] = TRUE;

  // Add resources from coreResourceList
  $jsWeight = -9999;
  foreach ($resource->coreResourceList($region) as $item) {
    if (is_array($item) && in_array('settings', $types)) {
      $resource->addSetting($item);
    }
    elseif (strpos($item, '.css') && in_array('css', $types)) {
      $resource->isFullyFormedUrl($item) ? $resource->addStyleUrl($item, -100, $region) : $resource->addStyleFile('civicrm', $item, -100, $region);
    }
    elseif (in_array('js', $types) && $resource->isFullyFormedUrl($item)) {
      $resource->addScriptUrl($item, $jsWeight++, $region);
    }
    elseif (in_array('js', $types)) {
      // Don't bother  looking for ts() calls in packages, there aren't any
      $translate = (substr($item, 0, 3) == 'js/');
      $resource->addScriptFile('civicrm', $item, $jsWeight++, $region, $translate);
    }
  }

  if (in_array('settings', $types)) {
    $config = CRM_Core_Config::singleton();
    // Add global settings
    $settings = [
      'config' => [
        'isFrontend' => $config->userFrameworkFrontend,
      ],
    ];
    // Disable profile creation if user lacks permission
    if (!CRM_Core_Permission::check('edit all contacts') && !CRM_Core_Permission::check('add contacts')) {
      $settings['config']['entityRef']['contactCreate'] = FALSE;
    }
    $resource->addSetting($settings);
  }

  if (in_array('js', $types)) {
    // Give control of jQuery and _ back to the CMS - this loads last
    $resource->addScriptFile('civicrm', 'js/noconflict.js', 9999, $region, FALSE);
  }

  if (in_array('css', $types)) {
    $resource->addCoreStyles($region);
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
