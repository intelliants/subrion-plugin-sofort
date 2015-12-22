<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	iaBreadcrumb::remove(iaBreadcrumb::POSITION_LAST);
	$iaView->set('nocsrf', true);

	$data = file_get_contents('php://input');

	if (empty($data))
	{
		return iaView::errorPage(iaView::ERROR_NOT_FOUND);
	}

	$iaView->disableLayout();

	$iaTransaction = $iaCore->factory('transaction');

	$params = array();
	parse_str($data, $params);

	if (isset($params['user_variable_0']) && isset($params['user_variable_1']) && isset($params['user_variable_2']))
	{
		$transaction = $iaTransaction->getBy('sec_key', $params['user_variable_0']);

		if (md5(IA_SALT . $transaction['id']) == $params['user_variable_1'])
		{
			$values = array(
				'date' => $params['created'],
				'amount' => $params['amount'],
				'currency' => $params['currency_id'],
				'reference_id' => $params['transaction'],
				'notes' => 'Updated via IPN at ' . date(iaDb::DATETIME_FORMAT)
			);

			$iaDb->update($values, iaDb::convertIds($params['user_variable_0'], 'sec_key'), null, $iaTransaction::getTable());

			$iaTransaction->addIpnLogEntry(IA_CURRENT_PLUGIN, $params, 'Valid');

			return;
		}
	}

	$iaTransaction->addIpnLogEntry(IA_CURRENT_PLUGIN, $params, 'Invalid');
}