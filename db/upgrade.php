<?php

function xmldb_block_chessblock_upgrade($oldversion) {
	global $CFG, $DB;

	$result = TRUE;

	//NOT IN THE DOCUMENTATION!
	$dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

	//not working
	//if ($oldversion < 2016021202) {

        // Define table block_chessblock_positions to be created.
        $table = new xmldb_table('block_chessblock_positions');

        // Adding fields to table block_chessblock_positions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('game_fen', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('game_pgn', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('user_id', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('player_color', XMLDB_TYPE_INTEGER, '1', null, null, null, null);

        // Adding keys to table block_chessblock_positions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_chessblock_positions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Chessblock savepoint reached.
		//wat verson number ???
        upgrade_block_savepoint(true, 2016021205, 'chessblock');
    //}

	// Insert PHP code from XMLDB Editor here

	return $result;
}
