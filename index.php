<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

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

			$iaTransaction->addIpnLogEntry(IA_CURRENT_MODULE, $params, 'Valid');

			return;
		}
	}

	$iaTransaction->addIpnLogEntry(IA_CURRENT_MODULE, $params, 'Invalid');
}