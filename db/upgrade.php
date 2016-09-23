<?php

function xmldb_block_chessblock_upgrade($oldversion) {
    global $CFG, $DB;

    $result = TRUE;

    // loads ddl manager and xmldb classes
	$dbman = $DB->get_manager();

	if ($oldversion < 2016092304) {

        // Drop unused tables
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

        // Create new table
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

        upgrade_block_savepoint(true, 2016092304, 'chessblock');
    }
	return $result;
}
