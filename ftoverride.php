<?php

require_once 'ftoverride.civix.php';
use CRM_Ftoverride_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function ftoverride_civicrm_config(&$config) {
  _ftoverride_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function ftoverride_civicrm_install() {
  _ftoverride_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function ftoverride_civicrm_enable() {
  _ftoverride_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function ftoverride_civicrm_managed(&$entities) {
  $entities[] = [
    'module' => 'com.megaphonetech.ftoverride',
    'name' => 'contributionCustomfield',
    'update' => 'never',
    'entity' => 'OptionValue',
    'params' => [
      'label' => ts('ContributionPage'),
      'name' => 'civicrm_contribution_page',
      'value' => 'ContributionPage',
      'option_group_id' => 'cg_extend_objects',
      'options' => ['match' => ['option_group_id', 'name']],
      'is_active' => 1,
      'version' => 3,
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.ftoverride',
    'name' => 'ft_override_designation_og',
    'entity' => 'OptionGroup',
    'params' => [
      'version' => 3,
      'name' => 'ft_override_designation',
      'label' => ts('Designation'),
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.ftoverride',
    'name' => 'ft_override',
    'entity' => 'CustomGroup',
    'update' => 'never',
    'params' => [
      'version' => 3,
      'name' => 'ft_override',
      'title' => ts('Details'),
      'extends' => 'ContributionPage',
      'style' => 'Inline',
      'is_active' => TRUE,
    ],
  ];
  $entities[] = [
    'module' => 'com.megaphonetech.ftoverride',
    'name' => 'ft_override_designation',
    'entity' => 'CustomField',
    'update' => 'never',
    'params' => [
      'version' => 3,
      'name' => 'ft_override_designation',
      'label' => ts('Designation'),
      'data_type' => 'String',
      'html_type' => 'Multi-Select',
      'is_active' => TRUE,
      'text_length' => 255,
      'custom_group_id' => 'ft_override',
      'option_group_id' => 'ft_override_designation'
    ],
  ];
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function ftoverride_civicrm_buildForm($formName, &$form) {
  if ('CRM_Contribute_Form_ContributionPage_Settings' == $formName) {
    $showElement = TRUE;
    if ($form->getVar('_id')) {
      $snippet = CRM_Utils_Array::value('snippet', $_GET);
      if (empty($snippet)) {
        $showElement = FALSE;
      }
    }

    $form->assign('showElement', $showElement);
    $financialTypes = ftoverride_get_financialType($form->getVar('_action'));
    $form->add('hidden', 'hidden_designation');
    $form->add(
      'select',
      'designation',
      ts('Designation'),
      $financialTypes,
      FALSE,
      [
        'class' => 'crm-select2',
        'multiple' => 'multiple',
        'data-option-edit-path' => 'civicrm/admin/financial/financialType',
      ]
    );
    CRM_Core_Region::instance('page-body')->add(array(
     'template' => 'CRM/Contribute/Form/ContributionPage/common.tpl',
    ));

    if ($form->getVar('_id')) {
      try {
        $designations = ftoverride_get_designation($form->getVar('_id'));
        if (empty($designations)) {
          return;
        }
        $form->setDefaults([
          'designation' => $designations,
          'hidden_designation' => implode(',', $designations),
        ]);
      }
      catch (Exception $e) {
        // Ignore
      }
    }
  }

  if ('CRM_Contribute_Form_Contribution_Confirm' == $formName && $form->_flagSubmitted) {
    $submitValues = $form->_params;
    if (!empty($submitValues['designation'])) {
      $form->assign('contribution_designation', $submitValues['designation']);
      if ($submitValues['designation'] == 'other_financial_type') {
        $form->_params['contribution_note'] = $submitValues['designation_note'];
      }
    }
  }

  if ('CRM_Contribute_Form_Contribution_Main' == $formName) {
    try {
      $designations = ftoverride_get_designation($form->_id);
      if (empty($designations)) {
        return;
      }

      $financialTypes = ftoverride_get_financialType($form->getVar('_action'));
      $designations = array_flip($designations);
      foreach($designations as $id => &$label) {
        $label = $financialTypes[$id];
      }
      $form->add(
        'select',
        'designation',
        ts('Designation'),
        ['' => ts('- select -')] + $designations,
        FALSE,
        ['class' => 'crm-select2']
      );
      $form->add(
        'text',
        'designation_note',
        ''
      );
      reset($designations);
      $form->setDefaults([
        'designation' => key($designations),
      ]);
      CRM_Core_Region::instance('page-body')->add(array(
       'template' => 'CRM/Contribute/Form/ContributionMain/common.tpl',
      ));
    }
    catch (Exception $e) {
      // Ignore
    }
  }

}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function ftoverride_civicrm_pre($op, $objectName, $id, &$params) {
  if ($op == 'create' && $objectName == 'Contribution' && !empty($params['contribution_page_id'])) {
    $designation = CRM_Core_Smarty::singleton()->get_template_vars('contribution_designation');
    if (!empty($designation)) {
      if ($designation == 'other_financial_type') {
        // Ignore
      }
      else {
        $params['financial_type_id'] = $designation;
      }
    }
  }

  if (in_array($op, ['create', 'edit'])
    && $objectName == 'ContributionPage'
    && isset($params['designation'])
  ) {
    if ($id) {
      $params['id'] = $id;
    }
    $customFieldId = civicrm_api3('CustomField', 'getvalue', [
      'return' => "id",
      'custom_group_id' => "ft_override",
      'name' => "ft_override_designation",
    ]);
    $params["custom_{$customFieldId}"] = explode(',', $params['hidden_designation']);
    unset($params['designation'], $params['hidden_designation']);
    $contributionPage = civicrm_api3('ContributionPage', 'create', $params);
    $params = ['id' => $contributionPage['id']];
  }
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_validateForm
 */
function ftoverride_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if ('CRM_Contribute_Form_Contribution_Main' == $formName) {
    if (!empty($fields['designation'])
      && $fields['designation'] == 'other_financial_type'
      && empty($fields['designation_note'])
    ) {
      $errors['designation_note'] = ts('Please provide other information about designation.');
    }
  }
}

/**
 * Implements hook_civicrm_buildAmount().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildAmount
 */
function ftoverride_civicrm_buildAmount($pageType, &$form, &$amount) {
  if (!empty($amount) && $form->_flagSubmitted) {
    $submitValues = $form->_submitValues;
    if (!empty($submitValues['designation']) && $submitValues['designation'] != 'other_financial_type') {
      foreach ($amount as &$priceFields) {
        foreach ($priceFields['options'] as &$options) {
          $options['financial_type_id'] = $submitValues['designation'];
        }
      }
    }
  }
}

function ftoverride_get_designation($pageId) {
  $customFieldId = civicrm_api3('CustomField', 'getvalue', [
    'return' => "id",
    'custom_group_id' => "ft_override",
    'name' => "ft_override_designation",
  ]);
  $designations = civicrm_api3('ContributionPage', 'getvalue', [
    'return' => "custom_{$customFieldId}",
    'id' => $pageId,
  ]);
  return $designations;
}

function ftoverride_get_financialType($action) {
  $financialTypes = [];
  CRM_Financial_BAO_FinancialType::getAvailableFinancialTypes($financialTypes, $action);
  if (empty($financialTypes)) {
    return $financialTypes;
  }
  $result = civicrm_api3('FinancialType', 'get', [
    'return' => ["description"],
    'description' => ['!=' => ""],
    'id' => ['IN' => array_keys($financialTypes)],
    'options' => ['limit' => 0],
  ]);
  $descFinancialType = [];
  if (!empty($result['values'])) {
    $descFinancialType = array_column($result['values'], 'description', 'id');
  }
  return $descFinancialType + $financialTypes + ['other_financial_type' => ts('Other')];
}
