<?php
namespace qnap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'not here' );
}

class QNAP_Cron {

	/**
	 * Schedules a hook which will be executed by the WordPress
	 * actions core on a specific interval
	 *
	 * @param  string  $hook       Event hook
	 * @param  string  $recurrence How often the event should reoccur
	 * @param  integer $timestamp  Preferred timestamp (when the event shall be run)
	 * @param  array   $args       Arguments to pass to the hook function(s)
	 * @return mixed
	 */
	public static function add( $hook, $recurrence, $timestamp, $args = array() ) {
		$schedules = wp_get_schedules();

		// Schedule event
		if ( isset( $schedules[ $recurrence ] ) && ( $current = $schedules[ $recurrence ] ) ) {
			if ( $timestamp <= ( $current_timestamp = time() ) ) {
				while ( $timestamp <= $current_timestamp ) {
					$timestamp += $current['interval'];
				}
			}

			return wp_schedule_event( $timestamp, $recurrence, $hook, $args );
		}
	}

	/**
	 * Un-schedules all previously-scheduled cron jobs using a particular
	 * hook name or a specific combination of hook name and arguments.
	 *
	 * @param  string  $hook Event hook
	 * @return boolean
	 */
	public static function clear( $hook ) {
		$cron = get_option( QNAP_CRON, array() );
		if ( empty( $cron ) ) {
			return false;
		}

		foreach ( $cron as $timestamp => $hooks ) {
			if ( isset( $hooks[ $hook ] ) ) {
				unset( $cron[ $timestamp ][ $hook ] );

				// Unset empty timestamps
				if ( empty( $cron[ $timestamp ] ) ) {
					unset( $cron[ $timestamp ] );
				}
			}
		}

		return update_option( QNAP_CRON, $cron );
	}

	/**
	 * Checks whether cronjob already exists
	 *
	 * @param  string  $hook Event hook
	 * @return boolean
	 */
	public static function exists( $hook ) {
		$cron = get_option( QNAP_CRON, array() );
		if ( empty( $cron ) ) {
			return false;
		}

		foreach ( $cron as $timestamp => $hooks ) {
			if ( isset( $hooks[ $hook ] ) ) {
				return true;
			}
		}

		return false;
	}
}
