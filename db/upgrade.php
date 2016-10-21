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
 * The upgrade file for database
 *
 * Each upgarde should be specified with their version number.
 *
 * @package block_chessblock
 * @copyright 2016 Global A-Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * MOODLE_INTERNAL - object, moodles intenral object.
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Defines changes to the db when to upgrade
 *
 * @param int $oldversion The old version of this plugin
 * @return bool The condition if the upgrade is successfully
 */
function xmldb_block_chessblock_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;

    // Loads ddl manager and xmldb classes.
    $dbman = $DB->get_manager();

    if ($oldversion < 2016092305) {

        // Drop unused tables.
        $table = new xmldb_table('block_chessblock_positions');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('block_chessblock_states');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('block_chessblock_games');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Create new table.
        $table = new xmldb_table('block_chessblock_games');

        // Adding fields to table block_chessblock_games.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('game_fen', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('game_pgn', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('black_user_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('white_user_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table block_chessblock_games.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_chessblock_games.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_block_savepoint(true, 2016092305, 'chessblock');
    }
    return $result;
}
