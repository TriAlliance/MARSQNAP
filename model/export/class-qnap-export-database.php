<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Export_Database {

	public static function execute( $params ) {
		global $wpdb;

		// Set exclude database
		if ( isset( $params['options']['no_database'] ) ) {
			return $params;
		}

		// Set query offset
		if ( isset( $params['query_offset'] ) ) {
			$query_offset = (int) $params['query_offset'];
		} else {
			$query_offset = 0;
		}

		// Set table index
		if ( isset( $params['table_index'] ) ) {
			$table_index = (int) $params['table_index'];
		} else {
			$table_index = 0;
		}

		// Set table offset
		if ( isset( $params['table_offset'] ) ) {
			$table_offset = (int) $params['table_offset'];
		} else {
			$table_offset = 0;
		}

		// Set table rows
		if ( isset( $params['table_rows'] ) ) {
			$table_rows = (int) $params['table_rows'];
		} else {
			$table_rows = 0;
		}

		// Set total tables count
		if ( isset( $params['total_tables_count'] ) ) {
			$total_tables_count = (int) $params['total_tables_count'];
		} else {
			$total_tables_count = 1;
		}

		// What percent of tables have we processed?
		$progress = (int) ( ( $table_index / $total_tables_count ) * 100 );

		// Set progress
		QNAP_Status::info( sprintf( __( 'Exporting database...<br />%d%% complete<br />%s records saved', QNAP_PLUGIN_NAME ), $progress, number_format_i18n( $table_rows ) ) );

		// Get tables list file
		$tables_list = qnap_open( qnap_tables_list_path( $params ), 'r' );

		// Loop over tables
		$tables = array();
		while ( $table_name = trim( fgets( $tables_list ) ) ) {
			$tables[] = $table_name;
		}

		// Close the tables list file
		qnap_close( $tables_list );

		// Get database client
		$mysql = QNAP_Database_Utility::create_client();

		// Exclude spam comments
		if ( isset( $params['options']['no_spam_comments'] ) ) {
			$mysql->set_table_where_query( qnap_table_prefix() . 'comments', "`comment_approved` != 'spam'" )
				->set_table_where_query( qnap_table_prefix() . 'commentmeta', sprintf( "`comment_ID` IN ( SELECT `comment_ID` FROM `%s` WHERE `comment_approved` != 'spam' )", qnap_table_prefix() . 'comments' ) );
		}

		// Exclude post revisions
		if ( isset( $params['options']['no_post_revisions'] ) ) {
			$mysql->set_table_where_query( qnap_table_prefix() . 'posts', "`post_type` != 'revision'" );
		}

		$old_table_prefixes = $old_column_prefixes = array();
		$new_table_prefixes = $new_column_prefixes = array();

		// Set table and column prefixes
		if ( qnap_table_prefix() ) {
			$old_table_prefixes[] = $old_column_prefixes[] = qnap_table_prefix();
			$new_table_prefixes[] = $new_column_prefixes[] = qnap_qeek_prefix();
		} else {
			// Set table prefixes based on table name
			foreach ( $tables as $table_name ) {
				$old_table_prefixes[] = $table_name;
				$new_table_prefixes[] = qnap_qeek_prefix() . $table_name;
			}

			// Set table prefixes based on column name
			foreach ( array( 'user_roles', 'capabilities', 'user_level', 'dashboard_quick_press_last_post_id', 'user-settings', 'user-settings-time' ) as $column_prefix ) {
				$old_column_prefixes[] = $column_prefix;
				$new_column_prefixes[] = qnap_qeek_prefix() . $column_prefix;
			}
		}

		$mysql->set_tables( $tables )
			->set_old_table_prefixes( $old_table_prefixes )
			->set_new_table_prefixes( $new_table_prefixes )
			->set_old_column_prefixes( $old_column_prefixes )
			->set_new_column_prefixes( $new_column_prefixes );

		// Exclude site options
		$mysql->set_table_where_query( qnap_table_prefix() . 'options', sprintf( "`option_name` NOT IN ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", QNAP_ACTIVE_PLUGINS, QNAP_ACTIVE_TEMPLATE, QNAP_ACTIVE_STYLESHEET, QNAP_STATUS, QNAP_SECRET_KEY, QNAP_AUTH_USER, QNAP_AUTH_PASSWORD, QNAP_BACKUPS_LABELS, QNAP_SITES_LINKS ) );

		// Replace table prefix on columns
		$mysql->set_table_prefix_columns( qnap_table_prefix() . 'options', array( 'option_name' ) )
			->set_table_prefix_columns( qnap_table_prefix() . 'usermeta', array( 'meta_key' ) );

		// Export database
		if ( $mysql->export( qnap_database_path( $params ), $query_offset, $table_index, $table_offset, $table_rows, $table_rows_count ) ) {

			// Set progress
			QNAP_Status::info( __( 'Done exporting database.', QNAP_PLUGIN_NAME ) );

			// Unset query offset
			unset( $params['query_offset'] );

			// Unset table index
			unset( $params['table_index'] );

			// Unset table offset
			unset( $params['table_offset'] );

			// Unset table rows
			unset( $params['table_rows'] );

			// Unset total tables count
			unset( $params['total_tables_count'] );

			// Unset total tables rows count
			unset( $params['table_rows_count'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// What percent of tables have we processed?
			$progress = (int) ( ( $table_index / $total_tables_count ) * 100 );

			// Set progress
			QNAP_Status::info( sprintf( __( 'Exporting database...<br />%d%% complete<br />%s records saved', QNAP_PLUGIN_NAME ), $progress, number_format_i18n( $table_rows ) ) );

			// Set query offset
			$params['query_offset'] = $query_offset;

			// Set table index
			$params['table_index'] = $table_index;

			// Set table offset
			$params['table_offset'] = $table_offset;

			// Set table rows
			$params['table_rows'] = $table_rows;

			// Set total tables count
			$params['total_tables_count'] = $total_tables_count;

			// Set table rows count
			$params['table_rows_count'] = $table_rows_count;

			// Set completed flag
			$params['completed'] = false;
		}

		return $params;
	}
}
