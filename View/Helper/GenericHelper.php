<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
* Webzash Plugin Generic Helper
*
* @package Webzash
* @subpackage Webzash.View
*/
class GenericHelper extends AppHelper {

	var $helpers = array('Html', 'Session');

/**
 * Helper method to return the tag
 */
	function showTag($id) {
		if (empty($id)) {
			return '';
		}

		/* Load the Tag model */
		App::import("Webzash.Model", "Tag");
		$model = new Tag();

		/* Find and return the tag */
		$tag = $model->findById($id);
		if (empty($tag)) {
			return '';
		} else {
			return '<span class="tag" style="color:#' . h($tag['Tag']['color']) . '; background-color:#' . h($tag['Tag']['background']) . ';">' . $this->Html->link($tag['Tag']['title'], array('plugin' => 'webzash', 'controller' => 'entries', 'action' => 'index', 'tag' => $tag['Tag']['id']), array('style' => 'color:#' . h($tag['Tag']['color']) . ';')) . '</span>';
		}
	}

/**
 * Helper method to return the entry type
 */
	function showEntrytype($id) {
		if (empty($id)) {
			return array('(Unknown)', '');
		}

		/* Load the Entry type model */
		App::import("Webzash.Model", "Entrytype");
		$model = new Entrytype();

		/* Find and return the entry type */
		$entrytype = $model->findById($id);
		if (empty($entrytype)) {
			return array('(Unknown)', '');
		} else {
			return array($entrytype['Entrytype']['name'], $entrytype['Entrytype']['label']);
		}
	}

/**
 * Helper method to return the entry number
 */
	function showEntryNumber($number, $entrytype_id) {
		if (Configure::read('Account.ET.' . $entrytype_id . '.zero_padding') > 0) {
			return Configure::read('Account.ET.' . $entrytype_id . '.prefix') .
				Configure::read('Account.ET.' . $entrytype_id . '.suffix');
		} else {
			return Configure::read('Account.ET.' . $entrytype_id . '.prefix') .
				str_pad($number, Configure::read('Account.ET.' . $entrytype_id . '.zero_padding'), '0', STR_PAD_LEFT) .
				Configure::read('Account.ET.' . $entrytype_id . '.suffix');
		}
	}

/**
 * Helper method to return the tags in list form
 */
	function tagList() {
		/* Load the Tag model */
		App::import("Webzash.Model", "Tag");
		$model = new Tag();

		$rawtags = $model->find('all', array('fields' => array('id', 'title'), 'order' => 'Tag.title'));
		$tags = array(0 => '(None)');
		foreach ($rawtags as $id => $rawtag) {
			$tags[$rawtag['Tag']['id']] = h($rawtag['Tag']['title']);
		}
		return $tags;
	}

/**
 * Helper method to return the ledgers in list form
 */
	function ledgerList($restriction_bankcash) {
		/* Load the Ledger model */
		App::import("Webzash.Model", "Ledger");
		$Ledger = new Ledger();

		/* Fetch all ledgers depending on the entry type */
		$ledgers[0] = '(Please select..)';

		if ($restriction_bankcash == 4) {
			$rawledgers = $Ledger->find('all', array('conditions' => array('Ledger.type' => '1'), 'order' => 'Ledger.name'));
		} else if ($restriction_bankcash == 5) {
			$rawledgers = $Ledger->find('all', array('conditions' => array('Ledger.type' => '0'), 'order' => 'Ledger.name'));
		} else {
			$rawledgers = $Ledger->find('all', array('order' => 'Ledger.name'));
		}

		foreach ($rawledgers as $row => $rawledger) {
			$ledgers[$rawledger['Ledger']['id']] = h($rawledger['Ledger']['name']);
		}

		return $ledgers;
	}

/**
 * Show the entry ledger details
 */
	public function entryLedgers($id) {
		/* Load the Entry model */
		App::import("Webzash.Model", "Entry");
		$Entry = new Entry();
		return $Entry->entryLedgers($id);
	}

/**
 * Return Ledger name from id
 */
	public function getLedgerName($id) {
		/* Load the Ledger model */
		App::import("Webzash.Model", "Ledger");
		$Ledger = new Ledger();
		return $Ledger->getName($id);
	}

/**
 * Helper method to return the ledgers in list form
 */
	function ajaxAddLedger($restriction_bankcash) {
		$ajaxurl = '';
		if ($restriction_bankcash == 4) {
			$ajaxurl = 'bankcash';
		} else if ($restriction_bankcash == 5) {
			$ajaxurl = 'nonbankcash';
		} else {
			$ajaxurl = 'all';
		}
		return $ajaxurl;
	}

/**
 * Wzuser return status string
 */
	function wzuser_status($status) {
		switch ($status) {
			case '0': return __d('webzash', 'Disabled');
			case '1': return __d('webzash', 'Enabled');
			default: return __d('webzash', 'Error');
		}
	}
/**
 * Wzuser return status options
 */
	function wzuser_status_options() {
		return array(
			'0' => __d('webzash', 'Disabled'),
			'1' => __d('webzash', 'Enabled'),
		);
	}

/**
 * Wzuser return status string
 */
	function wzuser_role($role) {
		switch ($role) {
			case 'admin': return __d('webzash', 'Administrator');
			case 'manager': return __d('webzash', 'Manager');
			case 'accountant': return __d('webzash', 'Accountant');
			case 'dataentry': return __d('webzash', 'Data entry operator');
			case 'guest': return __d('webzash', 'Guest');
			default: return __d('webzash', 'Error');
		}
	}
/**
 * Wzuser return status options
 */
	function wzuser_role_options() {
		return array(
			'admin' => __d('webzash', 'Administrator'),
			'manager' => __d('webzash', 'Manager'),
			'accountant' => __d('webzash', 'Accountant'),
			'dataentry' => __d('webzash', 'Data entry operator'),
			'guest' => __d('webzash', 'Guest'),
		);
	}

/**
 * Wzaccount return database type string
 */
	function wzaccount_dbtype($dbtype) {
		switch ($dbtype) {
			case 'Database/Mysql': return __d('webzash', 'MySQL');
			case 'Database/Sqlserver': return __d('webzash', 'MS SQL Server');
			case 'Database/Postgres': return __d('webzash', 'Postgres SQL');
			default: return __d('webzash', 'Error');
		}
	}
/**
 * Wzaccount return database type options
 */
	function wzaccount_dbtype_options() {
		return array(
			'Database/Mysql' => __d('webzash', 'MySQL'),
			'Database/Sqlserver' => __d('webzash', 'MS SQL Server'),
			'Database/Postgres' => __d('webzash', 'Postgres SQL'),
		);
	}

/**
 * Display flash messages
 *
 * https://github.com/dereuromark/cakephp-tools
 * The MIT License
 */
	public function flash(array $types = array()) {
		/* Get the messages from the session */
		$messages = (array)$this->Session->read('messages');
		$cMessages = (array)Configure::read('messages');
		if (!empty($cMessages)) {
			$messages = array_merge($messages, $cMessages);
		}
		$html = '';
		if (!empty($messages)) {
			$html = '<div>';

			if ($types) {
				foreach ($types as $type) {
					/* Add a div for each message using the type as the class. */
					foreach ($messages as $messageType => $msgs) {
						if ($messageType !== $type) {
							continue;
						}
						foreach ((array)$msgs as $msg) {
							$html .= $this->_message($msg, $messageType);
						}
					}
				}
			} else {
				foreach ($messages as $messageType => $msgs) {
					foreach ((array)$msgs as $msg) {
						$html .= $this->_message($msg, $messageType);
					}
				}
			}
			$html .= '</div>';
			if ($types) {
				foreach ($types as $type) {
					CakeSession::delete('messages.' . $type);
					Configure::delete('messages.' . $type);
				}
			} else {
				CakeSession::delete('messages');
				Configure::delete('messages');
			}
		}

		return $html;
	}

/**
 * Outputs a single flashMessage directly. Note that this does not use the Session.
 *
 * https://github.com/dereuromark/cakephp-tools
 * The MIT License
 *
 * @param string $message String to output.
 * @param string $type Type (success, warning, error, info)
 * @param bool $escape Set to false to disable escaping.
 * @return string HTML
 */
	public function flashMessage($msg, $type = 'info', $escape = true) {
		$html = '<div class="alert alert-' . $type . '" role="alert">';
		if ($escape) {
			$msg = h($msg);
		}
		$html .= $this->_message($msg, $type);
		$html .= '</div>';
		return $html;
	}

/**
 * Formats a message
 *
 * https://github.com/dereuromark/cakephp-tools
 * The MIT License
 *
 * @param string $msg Message to output.
 * @param string $type Type that will be formatted to a class tag.
 * @return string
 */
	protected function _message($msg, $type) {
		if (!empty($msg)) {
			return '<div class="alert alert-' . (!empty($type) ? $type : '') . '" role="alert">' . $msg . '</div>';
		}
		return '';
	}

}
