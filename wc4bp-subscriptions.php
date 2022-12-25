<?php
/**
 * Plugin Name: BuddyPress Integration for WooCommerce Subscriptions
 * Plugin URI:  https://themekraft.com/products/buddypress-woocommerce-subscriptions-integration/
 * Description: BuddyPress Integration for WooCommerce Subscriptions, integrate BuddyPress with WooCommerce Subscription. Ideal for subscription and membership sites such as premium support.
 * Author:      ThemeKraft
 * Author URI: https://themekraft.com/products/woocommerce-buddypress-integration/
 * Version:     1.1.9
 * Licence:     GPLv3
 * Text Domain: wc4bp_subscription
 * Domain Path: /languages
 *
 * @package wc4bp_subscription
 *
 *****************************************************************************
 * WC requires at least: 3.6.4
 * WC tested up to: 4.8.0
 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

if (! defined('WPINC')) {
    die;
}

if (! class_exists('wc4bp_subscriptions')) {
    require_once dirname(__FILE__) . '/classes/wc4bp_subscription_fs.php';
    new wc4bp_subscription_fs();

    class wc4bp_subscriptions
    {
        public static $plugin_file = __DIR__;

        /**
         * Instance of this class.
         *
         * @var object
         */
        protected static $instance = null;

        /**
         * Initialize the plugin.
         */
        public function __construct()
        {
            define('WC4BP_SUBSCRIPTION_CSS_PATH', plugin_dir_url(__FILE__) . 'assets/css/');
            define('WC4BP_SUBSCRIPTION_JS_PATH', plugin_dir_url(__FILE__) . 'assets/js/');
            define('WC4BP_SUBSCRIPTION_VIEW_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
            define('WC4BP_SUBSCRIPTION_CLASSES_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR);
            define('WC4BP_SUBSCRIPTION_BASENAME', basename(__DIR__));
            $this->load_plugin_textdomain();
            require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'resources' . DIRECTORY_SEPARATOR . 'class-tgm-plugin-activation.php';
            require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'wc4bp_subscription_required.php';
            new wc4bp_subscription_required();
            if (wc4bp_subscription_required::is_wc4bp_active()) {
                if (! empty($GLOBALS['wc4bp_loader'])) {
                    /** @var WC4BP_Loader $wc4bp */
                    $wc4bp = $GLOBALS['wc4bp_loader'];
                    $wc4bp_freemius = $wc4bp::getFreemius();
                    if (! empty($wc4bp_freemius) && $wc4bp_freemius->is_plan__premium_only('professional')) {
                        if (wc4bp_subscription_required::is_woo_subscription_active() && wc4bp_subscription_required::is_woocommerce_active()) {
                            require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'wc4bp_subscription_manager.php';
                            new wc4bp_subscription_manager();
                        } else {
                            //In case we  want to print this warning
                            add_action('admin_notices', array($this, 'admin_notice_need_woo_subscription'));
                        }
                    } else {
                        add_action('admin_notices', array($this, 'admin_notice_need_pro'));
                    }
                }
            }
        }

        public function admin_notice_need_pro()
        {
            $class = 'notice notice-warning';
            $message = __('Need WooBuddy -> WooCommerce BuddyPress Integration Professional Plan to work!', 'wc4bp_subscription');

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }

        public function admin_notice_need_woo_subscription()
        {
            $class = 'notice notice-warning';
            $message = __('WC4BP -> Subscription Need WooCommerce Subscription and Woocommerce!', 'wc4bp_subscription');

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }

        /**
         * Return an instance of this class.
         *
         * @return object A single instance of this class.
         */
        public static function get_instance()
        {
            // If the single instance hasn't been set, set it now.
            if (self::$instance === null) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Load the plugin text domain for translation.
         */
        public function load_plugin_textdomain()
        {
            load_plugin_textdomain('wc4bp_subscription', false, basename(dirname(__FILE__)) . '/languages');
        }
    }

    add_action('plugins_loaded', array('wc4bp_subscriptions', 'get_instance'));
}
