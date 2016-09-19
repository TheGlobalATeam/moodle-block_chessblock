<?php

defined('MOODLE_INTERNAL') || die();
$capabilities = array(
'block/chessblock:myaddinstance' => array(
	'captype' => 'write',
	'contextlevel' => CONTEXT_COURSE,
	'archetypes' => array(
		'user' => CAP_ALLOW
	),
	 'legacy' => array(
		'guest' => CAP_PREVENT,

	),

	'clonepermissionsfrom' => 'moodle/my:manageblocks'
),

'block/chessblock:addinstance' => array(
	'riskbitmask' => RISK_SPAM | RISK_XSS,

	'captype' => 'write',
	'contextlevel' => CONTEXT_BLOCK,
	'archetypes' => array(
		'editingteacher' => CAP_ALLOW,
		'manager' => CAP_ALLOW,
		),
	'legacy' => array(
		'guest' => CAP_PREVENT,

	),
	'clonepermissionsfrom' => 'moodle/site:manageblocks'
),
	'block/chessblock:view' => array(
	'captype' => 'read',
	'contextlevel' => CONTEXT_COURSE,
	'archetypes' => array(

		'guest'        => CAP_PREVENT

		),

	),
);
