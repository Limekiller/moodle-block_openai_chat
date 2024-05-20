<?php

function xmldb_block_openai_chat_upgrade($oldversion): bool {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2024040800) {

        // Define table block_openai_chat_log to be created.
        $table = new xmldb_table('block_openai_chat_log');

        // Adding fields to table block_openai_chat_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermessage', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('airesponse', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table block_openai_chat_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Adding indexes to table block_openai_chat_log.
        $table->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);
        $table->add_index('user-time', XMLDB_INDEX_NOTUNIQUE, ['userid', 'timecreated']);

        // Conditionally launch create table for block_openai_chat_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        // Openai_chat savepoint reached.
        upgrade_block_savepoint(true, 2024040401, 'openai_chat');
    }

    // Everything has succeeded to here. Return true.
    return true;
}