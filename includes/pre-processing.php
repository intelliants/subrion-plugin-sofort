<?php
//##copyright##

$formValues['user_id'] = $iaCore->get('sofort_user_id');
$formValues['project_id'] = $iaCore->get('sofort_project_id');
$formValues['currency_id'] = $iaCore->get('sofort_currency');
$formValues['language_id'] = strtoupper($iaView->language);
$formValues['amount'] = $plan['cost'];
$formValues['reason_1'] = $transaction['operation'];

$formValues['user_variable_0'] = $transaction['sec_key'];
$formValues['user_variable_1'] = md5(IA_SALT . $transaction['id']);
$formValues['user_variable_2'] = IA_URL_LANG;

$iaView->assign('formValues', $formValues);