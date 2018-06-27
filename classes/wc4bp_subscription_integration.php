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

class wc4bp_subscription_integration {

	public function __construct() {
		add_filter( 'wc4bp_add_endpoint', array( $this, 'wc4bp_subscription_menu_items' ) );
		add_shortcode( 'woo_subscriptions_page', array( $this, 'wc4bp_my_account_process_shortcode_subscriptions_page' ) );
		add_shortcode( 'woo_subscriptions_view_page', array( $this, 'wc4bp_my_account_process_shortcode_subscriptions_view_page' ) );
		add_filter( 'wcs_get_view_subscription_url', array( $this, 'wc4bp_get_view_subscription_url' ), 1, 2 );
		add_filter( 'wc4bp_screen_function', array( $this, 'screen_function' ), 10, 2 );
		add_filter( 'wc4bp_load_template_path', array( $this, 'load_template_path' ), 99, 2 );
		add_filter( 'wc4bp_members_get_template_directory', array( $this, 'get_template_directory' ), 10, 1 );

	}

	/**
	 * Expose to the core where will be the function to pass to BP to load the template
	 *
	 * @param $id
	 * @param $title
	 * @param $screen_function
	 *
	 * @return array
	 */
	public function screen_function( $screen_function, $id ) {
		if ( 'subscriptions' === $id ) {
			$screen_function = array( $this, 'wc4bp_subscription_screen_function' );
		}
		if($id === 'checkout'){

            if(isset($_GET['change_payment_method'])){

                $screen_function = array( $this, 'wc4bp_subscription_screen_function' );
            }
        }

		return $screen_function;
	}

	/**
	 * This function tell to buddyPress where load the template of the plugin
	 */
	public function wc4bp_subscription_screen_function() {
		bp_core_load_template( apply_filters( 'wc4bp_subscription_template', 'shop/member/plugin' ) );
	}

	/**
	 * TODO Complete by victor
	 *
	 * @param $view_subscription_url
	 * @param $id
	 *
	 * @return string
	 */
	public function wc4bp_get_view_subscription_url( $view_subscription_url, $id ) {
		global $bp;
		$base_url = wc4bp_redirect::get_base_url();
		$current_action = $bp->current_action;
		switch ( $bp->current_action ) {

            case 'checkout':
                $view_subscription_url = $base_url . 'subscriptions/view-subscription/' . $id;
                break;
			case 'subscriptions':
				$view_subscription_url = $base_url . 'subscriptions/view-subscription/' . $id;
				break;
			case 'orders':
				$view_subscription_url = $base_url . 'subscriptions/view-subscription/' . $id;
				break;
		}

		return $view_subscription_url;
	}

	/**
	 * TODO Complete by victor
	 *
	 * @param $dir
	 *
	 * @return string
	 */
	public function get_template_directory( $dir ) {
		global $bp;
		if ( 'subscriptions' === $bp->current_action ) {
			return WC4BP_SUBSCRIPTION_VIEW_PATH;
		}

		return $dir;
	}

	/**
	 * TODO Complete by victor
	 *
	 * @param $path
	 * @param $template_directory
	 *
	 * @return string
	 */
	public function load_template_path( $path, $template_directory ) {
		global $bp;
		if ( 'subscriptions' === $bp->current_action ) {
			$is_view_subscription = array_search( 'view-subscription', $bp->unfiltered_uri, true );
			if ( false !== $is_view_subscription ) {
				$path = 'view-subscription';
			} else {
				$path = 'subscription';
			}
		}

		return $path;
	}

	/**
	 * TODO Complete by victor
	 *
	 * @param $menu_items
	 *
	 * @return array|false
	 */
	public function wc4bp_subscription_menu_items( $menu_items ) {
		// Add our menu item after the Orders tab if it exists, otherwise just add it to the end
		if ( array_key_exists( 'orders', $menu_items ) ) {
			$menu_items = wc4bp_subscription_manager::array_insert_after( 'orders', $menu_items, 'subscriptions', __( 'Subscriptions', 'woocommerce-subscriptions' ) );
		} else {
			$menu_items['subscriptions'] = __( 'Subscriptions', 'woocommerce-subscriptions' );
		}

		return $menu_items;
	}

	/**
	 * TODO Complete by victor
	 *
	 * @param $attr
	 * @param string $content
	 */
	public function wc4bp_my_account_process_shortcode_subscriptions_view_page( $attr, $content = '' ) {
		wc_print_notices();
		wc_get_template( 'myaccount/view-subscription.php', array(), '', plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/' );
	}

	/**
	 * TODO Complete by victor
	 *
	 * @param $attr
	 * @param $content
	 */
	public function wc4bp_my_account_process_shortcode_subscriptions_page( $attr, $content ) {
		wc_print_notices();
		wc_get_template( 'myaccount/subscriptions.php', array(), '', plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/' );
	}
}