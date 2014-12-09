<?php

/*
	This file is part of PUPI - Fake Cron, a Question2Answer plugin that
	fires events after a certain amount of requests.

	Copyright (C) 2014 Gabriel Zanetti <http://github.com/pupi1985>

	PUPI - Fake Cron is free software: you can redistribute it and/or
	modify it under the terms of the GNU General Public License as published
	by the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	PUPI - Fake Cron is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
	Public License for more details.

	You should have received a copy of the GNU General Public License along
	with PUPI - Fake Cron. If not, see <http://www.gnu.org/licenses/>.
*/

class qa_html_theme_layer extends qa_html_theme_base {

	const MAXIMUM_REQUEST_COUNTER = 2000000000;  // In order to use round numbers I've decided to use a big enough valid 32-bit number
	const REQUEST_INTERVAL_NOTIFICATION = 100;  // This constant and the MAXIMUM_REQUEST_COUNTER will determine the maximum event counter before resetting it

	private $eventFired = false;

	// Business logic methods

	private function requestEventHandler() {
		$executionEnabled = (bool) qa_opt(PUPI_FC_Module_Admin::SETTING_REQUEST_COUNTER_ENABLED);
		if ($executionEnabled) {  // Being the first handler to be processed there is no need to check for !$this->eventFired
			// Sanitize the data from the database and reset
			$requestCounter = (int) qa_opt(PUPI_FC_Module_Admin::SETTING_REQUEST_COUNTER);
			if ($requestCounter < 0 || $requestCounter >= self::MAXIMUM_REQUEST_COUNTER) {
				$requestCounter = 1;
			} else {
				$requestCounter++;
			}
			// Make sure the counter is increased regardless of the success of the operation
			qa_set_option(PUPI_FC_Module_Admin::SETTING_REQUEST_COUNTER, $requestCounter);

			if ($requestCounter % self::REQUEST_INTERVAL_NOTIFICATION == 0) {
				$this->eventFired = true;
				qa_report_event(
					PUPI_FC_Module_Admin::EVENT_REQUEST,
					null,
					null,
					null,
					array('event_counter' => $requestCounter / self::REQUEST_INTERVAL_NOTIFICATION)
				);
			}
		}
	}

	private function timeEventHandler($currentTime, $dateFormat, $executionSetting, $executionEnabledSetting, $timeSettingTypeParam) {
		$executionEnabled = (bool) qa_opt($executionEnabledSetting);
		if ($executionEnabled && !$this->eventFired) {  // Make sure the event is not fired more than once
			$lastExecution = (int) qa_opt($executionSetting);
			if (date($dateFormat, $currentTime) !== date($dateFormat, $lastExecution)) {
				// Make sure the current date is saved regardless of the success of the operation
				qa_set_option($executionSetting, $currentTime);
				$this->eventFired = true;
				qa_report_event(
					PUPI_FC_Module_Admin::EVENT_TIME,
					null,
					null,
					null,
					array(
						'type' => $timeSettingTypeParam,
						'last_execution' => $lastExecution,
					)
				);
			}
		}
	}

	// Layer methods

	public function doctype() {
		require_once QA_HTML_THEME_LAYER_DIRECTORY . 'pupi_fc_module_admin.php';

		$pluginEnabled = (bool) qa_opt(PUPI_FC_Module_Admin::SETTING_PLUGIN_ENABLED);
		if ($pluginEnabled) {
			$this->requestEventHandler();

			$currentTime = qa_opt('db_time');
			$this->timeEventHandler(
				$currentTime,
				'Y-m-d',
				PUPI_FC_Module_Admin::SETTING_LAST_DAILY_EXECUTION,
				PUPI_FC_Module_Admin::SETTING_DAILY_EXECUTION_ENABLED,
				'daily'
			);
			$this->timeEventHandler(
				$currentTime,
				'Y-W',
				PUPI_FC_Module_Admin::SETTING_LAST_WEEKLY_EXECUTION,
				PUPI_FC_Module_Admin::SETTING_WEEKLY_EXECUTION_ENABLED,
				'weekly'
			);
			$this->timeEventHandler(
				$currentTime,
				'Y-m',
				PUPI_FC_Module_Admin::SETTING_LAST_MONTHLY_EXECUTION,
				PUPI_FC_Module_Admin::SETTING_MONTHLY_EXECUTION_ENABLED,
				'monthly'
			);
		}

		parent::doctype();
	}


}
