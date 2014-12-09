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

class PUPI_FC_Module_Admin {

	const BUTTON_SAVE = 'pupi_fc_button_save';

	// Settings

	const SETTING_PLUGIN_ENABLED = 'pupi_fc_plugin_enabled';

	const SETTING_REQUEST_COUNTER_ENABLED = 'pupi_fc_request_counter_enabled';
	const SETTING_DAILY_EXECUTION_ENABLED = 'pupi_fc_last_daily_execution_enabled';
	const SETTING_WEEKLY_EXECUTION_ENABLED = 'pupi_fc_last_weekly_execution_enabled';
	const SETTING_MONTHLY_EXECUTION_ENABLED = 'pupi_fc_last_monthly_execution_enabled';

	const SETTING_REQUEST_COUNTER = 'pupi_fc_request_counter';
	const SETTING_LAST_DAILY_EXECUTION = 'pupi_fc_last_daily_execution';
	const SETTING_LAST_WEEKLY_EXECUTION = 'pupi_fc_last_weekly_execution';
	const SETTING_LAST_MONTHLY_EXECUTION = 'pupi_fc_last_monthly_execution';

	// Default setting values omitted as they are all 0 or false

	// Events

	const EVENT_REQUEST = 'pupi_fc_request';
	const EVENT_TIME = 'pupi_fc_time';

	// Language keys

	const LANG_ID_ADMIN_SETTINGS_SAVED = 'admin_settings_saved';
	const LANG_ID_ADMIN_SAVE_SETTINGS_BUTTON = 'admin_save_settings_button';
	const LANG_ID_ADMIN_PLUGIN_ENABLED = 'admin_plugin_enabled';
	const LANG_ID_ADMIN_REQUEST_COUNTER_ENABLED = 'admin_request_counter_enabled';
	const LANG_ID_ADMIN_DAILY_EXECUTION_ENABLED = 'admin_daily_execution_enabled';
	const LANG_ID_ADMIN_WEEKLY_EXECUTION_ENABLED = 'admin_weekly_execution_enabled';
	const LANG_ID_ADMIN_MONTHLY_EXECUTION_ENABLED = 'admin_monthly_execution_enabled';

	public static function translate($id) {
		return qa_lang_html('pupi_fc/' . $id);
	}

	public function admin_form(&$qa_content) {
		$ok = null;
		if (qa_clicked(self::BUTTON_SAVE)) {
			$this->savePluginEnabledSetting();
			$this->saveRequestCounterEnabledSetting();
			$this->saveDailyExecutionEnabledSetting();
			$this->saveWeeklyExecutionEnabledSetting();
			$this->saveMonthlyExecutionEnabledSetting();
			$ok = self::translate(self::LANG_ID_ADMIN_SETTINGS_SAVED);
		}
		$fields = array_merge(
			$this->getPluginEnabledField(),
			$this->getRequestCounterEnabledField(),
			$this->getDailyExecutionEnabledField(),
			$this->getWeeklyExecutionEnabledField(),
			$this->getMonthlyExecutionEnabledField()
		);
		return array(
			'ok' => $ok,
			'style' => 'wide',
			'fields' => $fields,
			'buttons' => $this->getButtons(),
		);
	}

	private function getButtons() {
		return array(
			'save' => array(
				'tags' => 'name="' . self::BUTTON_SAVE . '"',
				'label' => self::translate(self::LANG_ID_ADMIN_SAVE_SETTINGS_BUTTON),
			),
		);
	}

	// All field returning methods

	private function getPluginEnabledField() {
		return array(array(
			'label' => self::translate(self::LANG_ID_ADMIN_PLUGIN_ENABLED),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_PLUGIN_ENABLED . '"',
			'type' => 'checkbox',
			'value' => (bool) qa_opt(self::SETTING_PLUGIN_ENABLED),
		));
	}

	private function getRequestCounterEnabledField() {
		return array(array(
			'label' => self::translate(self::LANG_ID_ADMIN_REQUEST_COUNTER_ENABLED),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_REQUEST_COUNTER_ENABLED . '"',
			'type' => 'checkbox',
			'value' => (bool) qa_opt(self::SETTING_REQUEST_COUNTER_ENABLED),
		));
	}

	private function getDailyExecutionEnabledField() {
		return array(array(
			'label' => self::translate(self::LANG_ID_ADMIN_DAILY_EXECUTION_ENABLED),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_DAILY_EXECUTION_ENABLED . '"',
			'type' => 'checkbox',
			'value' => (bool) qa_opt(self::SETTING_DAILY_EXECUTION_ENABLED),
		));
	}

	private function getWeeklyExecutionEnabledField() {
		return array(array(
			'label' => self::translate(self::LANG_ID_ADMIN_WEEKLY_EXECUTION_ENABLED),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_WEEKLY_EXECUTION_ENABLED . '"',
			'type' => 'checkbox',
			'value' => (bool) qa_opt(self::SETTING_WEEKLY_EXECUTION_ENABLED),
		));
	}

	private function getMonthlyExecutionEnabledField() {
		return array(array(
			'label' => self::translate(self::LANG_ID_ADMIN_MONTHLY_EXECUTION_ENABLED),
			'style' => 'tall',
			'tags' => 'name="' . self::SETTING_MONTHLY_EXECUTION_ENABLED . '"',
			'type' => 'checkbox',
			'value' => (bool) qa_opt(self::SETTING_MONTHLY_EXECUTION_ENABLED),
		));
	}

	// All field saving methods

	private function savePluginEnabledSetting() {
		qa_opt(self::SETTING_PLUGIN_ENABLED, (bool) qa_post_text(self::SETTING_PLUGIN_ENABLED));
	}

	private function saveRequestCounterEnabledSetting() {
		qa_opt(self::SETTING_REQUEST_COUNTER_ENABLED, (bool) qa_post_text(self::SETTING_REQUEST_COUNTER_ENABLED));
	}

	private function saveDailyExecutionEnabledSetting() {
		qa_opt(self::SETTING_DAILY_EXECUTION_ENABLED, (bool) qa_post_text(self::SETTING_DAILY_EXECUTION_ENABLED));
	}

	private function saveWeeklyExecutionEnabledSetting() {
		qa_opt(self::SETTING_WEEKLY_EXECUTION_ENABLED, (bool) qa_post_text(self::SETTING_WEEKLY_EXECUTION_ENABLED));
	}

	private function saveMonthlyExecutionEnabledSetting() {
		qa_opt(self::SETTING_MONTHLY_EXECUTION_ENABLED, (bool) qa_post_text(self::SETTING_MONTHLY_EXECUTION_ENABLED));
	}

}