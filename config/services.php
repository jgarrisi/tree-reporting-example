<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => '',
		'secret' => '',
	],

	'mandrill' => [
		'secret' => '',
	],

	'ses' => [
		'key' => '',
		'secret' => '',
		'region' => 'us-east-1',
	],

	'stripe' => [
		'model'  => 'User',
		'secret' => '',
	],

	'RIPSvc' => [
		'baseurl' => 'http://c0007700.itcs.hp.com:8080/ITSMOReIPServices/services/query/',
		'userpwd'  => '', //TODO(mark): place rip svc userpwd here
	],

	'LDAP' => [
		'host' => 'ldap.hp.com',
		'dn'  => 'ou=Groups,o=hp.com',
		'accessGroup' => 'day1-it-smo-rip-data-entry'
	],

];
