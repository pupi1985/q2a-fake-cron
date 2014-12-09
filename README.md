# Fake Cron [by [Gabriel Zanetti][author]]

## Description

Fake Cron is a [Question2Answer][Q2A] plugin that fires events after a certain amount of requests or in a time-based manner (daily, weekly, monthly).

## Features

 *  An event is fired every a `100` requests
 *  An event is fired once daily, weekly and monthly (provided a request has been performed)
 *  Avoids performing a given operation (checking files, running database queries, sending an email, checking server status, etc) on each request
 *  No need of core hacks or plugin overrides
 *  Simple installation

### Notes:

 *  This plugin is no replacement of cron and does fire any event based on the time factor
 *  The plugin is intended to be installed by Q2A admins but actually used by developers
 *  Works on Q2A version 1.6 and later

## Setup comments

 1. Copy the plugin directory into the `qa-plugin` directory
 1. Enable the plugin and the events you want to listen to

## What the plugin does in detail

### Requests event

The plugin keeps track of a counter which is incremented whenever a layer is initialized. Each time the counter is incremented the plugin will check whether its modulo `100` matches `0`. If it does, then the event is fired.

As integer numbers have a limited size it is not possible to increment a number to infinity so the number is reset to `1` once it reaches `2000000000`. When this rare event happens, the plugin will restart the counter and keep firing events the same as before.

### Time-based events

These type of events are not exactly based on time as they are triggered by a request too. However, the frequency in which they are limited is based on fixed periods: daily, weekly and monthly. This means for a given period the first request that is run during that period triggers the event and all the other don't trigger the event for the same period.

However, there is a special case. When more than one event is being enabled, and the month changes, the first request would fire the daily, weekly and monthly requests. In order to avoid running too many actions together in the same request only the daily one would be run. The second request would fire the weekly event and the third request would fire the monthly event. Note the request event has more priority than the time-based events so it is always the first to execute.

## How to use the plugin

As this plugin aims at being a *dependency* of other plugins, it is likely that the reader is a Q2A developer. So in short, an [event module](http://www.question2answer.org/modules.php?module=event) has to be implemented in order to use this plugin.

Taking that into account, consider the following code event module class:

```php
class YOUR_PLUGIN_Sample_Event_Module {

	const EXECUTION_FREQUENCY = 2;

	public function process_event($event, $userId, $handle, $cookieId, $params) {
		switch ($event) {
			case 'pupi_fc_request':
				// At this point $userId, $handle and $cookieId are null
				if ($params['event_counter'] % self::EXECUTION_FREQUENCY == 0) {
					// The custom actions needed for request events should be added here
				}
				break;
			case 'pupi_fc_time':
				switch ($params['type']) {
					case 'daily':
						// The custom actions needed for daily events should be added here
						break;
					case 'weekly':
						// The custom actions needed for weekly events should be added here
						break;
					case 'monthly':
						// The custom actions needed for monthly events should be added here
						break;
					default:
				}
				break;
			default:
		}
	}

}
```

So in this case the implemented event module firstly checks for the event to be any of the ones that fire the Fake Cron plugin. Then, when processing the request event, instead of performing the needed action immediatly, the developer of the sample event class considered that performing the operation they need to execute after `100` requests was too often. So they decided to execute it every `200` requests (`2` times `100` requests).

This sample event module also listens for the time-based event in its three different types and performs different action depending on the type.

The key points are:

 *  The `$userId`, `$handle` and `$cookieId` will always be null. The reason for this is to save resources and it is unlikely that the current user makes any difference in the processing
 *  Regarding the request event
     *  It is fired by the plugin as `pupi_fc_request`
     *  The `$params` array always contains the key `event_counter` and its value is always an integer between `1` and `20000000`
     *  If a site receives its `2000000000` visit then the counter will notify `20000000` and then be reset to `1`
 *  Regarding the time-based event
     *  It is fired by the plugin as `pupi_fc_time`
     *  There is only one time-based event but has different parameters for each type
     *  The `$params` array always contains the key `type` and its value is always a string with the following possible values `'daily'`, `'weekly'` and `'monthly'`
     *  The `$params` array always contains the key `last_execution`and its value is a unix timestamp that holds the previous execution time or `0` if it had never been executed before

## Bear in mind

 *  If it is needed to add a modulo in the `process_event` function for handling request events (as in the sample event module) special care needs to be taken because of the counter resets. In other words, if `EXECUTION_FREQUENCY` was set to `3` then the process would run on `19999998` and, instead of `20000001` (because it exceeds the limit), it would run again on `3`. This results in `5` increments instead of `3`. Not a big deal for 99.99% of the plugins
 *  You should not perform heavy operations. The event will be fired by any user performing a request that loads a layer. That means that particular user will have their request delayed by whatever operation the event module will perform. Even if you don't care about that user's request taking long, you still have to care about being quick because most hosting providers set their PHP `max_execution_time` between `10` and `30` seconds
 *  When developing a plugin that depends on this one it is possible to change the request event limits by modifying `REQUEST_INTERVAL_NOTIFICATION` and `MAXIMUM_REQUEST_COUNTER` in `pupi_fc_layer.php`
 *  This plugin might be shared by more than one plugin so changing the `REQUEST_INTERVAL_NOTIFICATION` value to any other than `100` might be risky as it might delay or accelerate other plugin's event executions
 *  At most one event is fired in a single request. This applies to request and time-based events (regardless of their type)

## Support

If you've found a bug then create a ticket in the [Issues][issues] section.

## Get the plugin

The plugin can be downloaded from [this link][download]. You can say thanks [donating using PayPal][paypal].

[Q2A]: http://www.question2answer.com
[download]: https://github.com/pupi1985/q2a-fake-cron/archive/master.zip
[issues]: https://github.com/pupi1985/q2a-fake-cron/issues
[paypal]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y7LUM6ML4UV9L
[author]: http://question2answer.org/qa/user/pupi1985
