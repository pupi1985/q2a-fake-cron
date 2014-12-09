<?php

/*
	Plugin Name: PUPI - Fake Cron
	Plugin URI: http://github.com/pupi1985/q2a-fake-cron
	Plugin Description: Fires events after a certain amount of requests
	Plugin Version: 1.0.0
	Plugin Date: 2014-12-08
	Plugin Author: Gabriel Zanetti
	Plugin Author URI: http://question2answer.org/qa/user/pupi1985
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Minimum PHP Version: 5.1.6
	Plugin Update Check URI: https://raw.githubusercontent.com/pupi1985/q2a-fake-cron/master/qa-plugin.php
*/

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

if (!defined('QA_VERSION')) { // don't allow this file to be requested directly from browser
	header('Location: ../../');
	exit;
}

qa_register_plugin_layer('pupi_fc_layer.php', 'PUPI FC Layer');
qa_register_plugin_module('module', 'pupi_fc_module_admin.php', 'PUPI_FC_Module_Admin', 'PUPI FC Module Admin');
qa_register_plugin_phrases('lang/pupi_fc_*.php', 'pupi_fc');
