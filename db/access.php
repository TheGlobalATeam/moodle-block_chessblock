<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The access php file
 *
 * For providing with instance information.
 *
 * @package block_chessblock
 * @copyright 2016 Global A-Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * MOODLE_INTERNAL - object, moodles intenral object.
  */
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
