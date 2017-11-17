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
        add_filter( 'wc4bp_members_get_template_directory', array( $this, 'get_template_directory' ), 10, 1 );
        add_filter( 'wc4bp_load_template_path', array( $this, 'load_template_path' ), 99, 2 );
        add_filter( 'wc4bp_add_endpoint', array( $this, 'wc4bp_subscription_menu_items' ) );
        add_shortcode( 'woo_subscriptions_page', array( $this, "wc4bp_my_account_process_shortcode_subscriptions_page" ) );
        add_shortcode( 'woo_subscriptions_view_page', array( $this, "wc4bp_my_account_process_shortcode_subscriptions_view_page" ) );
        add_filter( 'wcs_get_view_subscription_url',array( $this, 'wc4bp_get_view_subscription_url' ), 1, 2 );
        add_filter( 'woocommerce_get_view_order_url',array( $this, 'wc4bp_woocommerce_get_view_order_url' ), 1, 2 );
	}

	public function wc4bp_woocommerce_get_view_order_url($url,$container){
        global $bp;
        $c_action= $bp->current_action;
        $status = $container->status;
        $current_user = wp_get_current_user();
        $userdata     = get_userdata( $current_user->ID );
	    $link = $url;
	    return $link;

    }
    public function wc4bp_get_view_subscription_url( $view_subscription_url,$id){
        global $bp;
        $c_action= $bp->current_action;
        $current_user = wp_get_current_user();
        $userdata     = get_userdata( $current_user->ID );
        $link = $view_subscription_url;
        if ($c_action === 'wc4pb_subscriptions'){
            $link = get_bloginfo( 'url' ) . '/' . $bp->pages->members->slug . '/' . $userdata->user_nicename .'/shop/wc4pb_subscriptions/view-subscription/'.$id;
        }
        if($c_action === 'wc4pb_orders'){

            $link = get_bloginfo( 'url' ) . '/' . $bp->pages->members->slug . '/' . $userdata->user_nicename .'/shop/wc4pb_subscriptions/view-subscription/'.$id;
        }
        return $link;
    }
    public function get_template_directory( $dir ) {
        global $bp;
        if ( $bp->current_action === 'subscriptions' ) {
            return WC4BP_SUBSCRIPTION_VIEW_PATH;
        }
        return $dir;
    }

    public function load_template_path( $path, $template_directory ) {
        global $bp;
        if ( $bp->current_action === 'subscriptions' ) {
            $is_view_subscription = array_search( 'view-subscription', $bp->unfiltered_uri );
            if ( $is_view_subscription !== false ) {
                $path = 'view-subscription';
            } else {
                $path = 'subscription';
            }
        }
        return $path;
    }

    public function wc4bp_subscription_menu_items( $menu_items ) {
        // Add our menu item after the Orders tab if it exists, otherwise just add it to the end
        if ( array_key_exists( 'orders', $menu_items ) ) {
            $menu_items = wcs_array_insert_after( 'orders', $menu_items, 'subscriptions', __( 'Subscriptions', 'woocommerce-subscriptions' ) );
        } else {
            $menu_items['subscriptions'] = __( 'Subscriptions', 'woocommerce-subscriptions' );
        }
        return $menu_items;
    }

    public function wc4bp_my_account_process_shortcode_subscriptions_view_page( $attr, $content = "" ) {
        wc_print_notices();
        wc_get_template( 'myaccount/view-subscription.php', array(), '', plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/' );
    }
    public function wc4bp_my_account_process_shortcode_subscriptions_page( $attr, $content ) {
        wc_print_notices();
        wc_get_template( 'myaccount/subscriptions.php', array(), '', plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/' );
    }
}