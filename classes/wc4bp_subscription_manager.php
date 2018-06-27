<?php
/**
 * @package        WordPress
 * @subpackage     BuddyPress, Woocommerce, WC4BP
 * @author         ThemKraft Dev Team
 * @copyright      2017, Themekraft
 * @link           http://themekraft.com/store/woocommerce-buddypress-integration-wordpress-plugin/
 * @license        http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wc4bp_subscription_manager {

	private static $plugin_slug = 'wc4bp_subscriptions';
	protected static $version = '1.1.2';
	private $end_points;

	public function __construct() {
		require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'wc4bp_subscription_log.php';
		new wc4bp_subscription_log();
		try {
			//loading_dependency
			require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'wc4bp_subscription_integration.php';
			new wc4bp_subscription_integration();

		} catch ( Exception $ex ) {
			wc4bp_subscription_log::log( array(
				'action'         => get_class( $this ),
				'object_type'    => self::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $ex->getMessage(),
			) );
		}
	}

	/**
	 * Get plugins version
	 *
	 * @return mixed
	 */
	static function getVersion() {
		return self::$version;
	}

	/**
	 * Get plugins slug
	 *
	 * @return string
	 */
	static function getSlug() {
		return self::$plugin_slug;
	}

	/**
	 * Retrieve the translation for the plugins. Wrapper for @see __()
	 *
	 * @param $str
	 *
	 * @return string
	 */
	public static function translation( $str ) {
		return __( $str, 'wc4bp_subscription' );
	}


	/**
	 * Display the translation for the plugins. Wrapper for @see _e()
	 *
	 * @param $str
	 */
	public static function echo_translation( $str ) {
		_e( $str, 'wc4bp_subscription' );
	}

	/**
	 * Display the translation for the plugins.
	 *
	 * @param $str
	 */
	public static function echo_esc_attr_translation( $str ) {
		echo esc_attr( self::translation( $str ) );
	}

	/*
	 * Inserts a new key/value after the key in the array.
	 *
	 * @param $key
	 *   The key to insert after.
	 * @param $array
	 *   An array to insert in to.
	 * @param $new_key
	 *   The key to insert.
	 * @param $new_value
	 *   An value to insert.
	 *
	 * @return
	 *   The new array if the key exists, FALSE otherwise.
	 *
	 * @see array_insert_before()
	 */
	static function array_insert_after( $key, array &$array, $new_key, $new_value ) {
		if ( array_key_exists( $key, $array ) ) {
			$new = array();
			foreach ( $array as $k => $value ) {
				$new[ $k ] = $value;
				if ( $k === $key ) {
					$new[ $new_key ] = $new_value;
				}
			}

			return $new;
		}

		return false;
	}
}
