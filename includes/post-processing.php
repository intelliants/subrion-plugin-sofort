<?php
//##copyright##

$transaction = $temp_transaction;

switch ($action)
{
	case 'completed':
		if (!empty($_GET['ref']) && !empty($_GET['amt']) && !empty($_GET['s']) && isset($_GET['payer']) && isset($_GET['currency']))
		{
			if ($_GET['s'] == md5(IA_SALT . $transaction['id']))
			{
				$transaction['reference_id'] = $_GET['ref'];
				$transaction['fullname'] = $_GET['payer'];
				$transaction['currency'] = $_GET['currency'];

				$transaction['status'] = iaTransaction::PASSED;

				$payer = explode(' ', $_GET['payer']);

				$order = array(
					'payment_gross' => (float)$_GET['amt'],
					'mc_currency' => $_GET['currency'],
					'payment_date' => date(iaDb::DATETIME_SHORT_FORMAT),
					'payment_status' => iaLanguage::get(iaTransaction::PASSED),
					'first_name' => iaSanitize::html($payer[0]),
					'last_name' => isset($payer[1]) ? iaSanitize::html($payer[1]) : '',
					'payer_email' => '',
					'txn_id' => iaSanitize::html($transaction['reference_id'])
				);
			}
		}

		break;

	case 'canceled':
		$error = true;
		$messages[] = iaLanguage::get('oops');

		$transaction['status'] = iaTransaction::FAILED;
}