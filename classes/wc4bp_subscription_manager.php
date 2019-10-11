<?php
/**
 * @package        WordPress
 * @subpackage     BuddyPress, Woocommerce, WC4BP
 * @author         ThemKraft Dev Team
 * @copyright      2017, Themekraft
 * @link           http://themekraft.com/store/woocommerce-buddypress-integration-wordpress-plugin/
 * @license        http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if (! defined('ABSPATH')) {
    exit;
}

class wc4bp_subscription_manager
{
    protected static $version = '1.1.6';

    private static $plugin_slug = 'wc4bp_subscriptions';

    public function __construct()
    {
        require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'wc4bp_subscription_log.php';
        new wc4bp_subscription_log();
        try {
            //loading_dependency
            require_once WC4BP_SUBSCRIPTION_CLASSES_PATH . 'wc4bp_subscription_integration.php';
            new wc4bp_subscription_integration();
        } catch (Exception $ex) {
            wc4bp_subscription_log::log(array(
                'action' => static::class,
                'object_type' => self::getSlug(),
                'object_subtype' => 'loading_dependency',
                'object_name' => $ex->getMessage(),
            ));
        }
    }

    /**
     * Get plugins version
     *
     * @return mixed
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * Get plugins slug
     *
     * @return string
     */
    public static function getSlug()
    {
        return self::$plugin_slug;
    }

    /**
     * Retrieve the translation for the plugins. Wrapper for @see __()
     *
     * @return string
     */
    public static function translation($str)
    {
        return __($str, 'wc4bp_subscription');
    }

    /**
     * Display the translation for the plugins. Wrapper for @see _e()
     */
    public static function echo_translation($str)
    {
        _e($str, 'wc4bp_subscription');
    }

    /**
     * Display the translation for the plugins.
     */
    public static function echo_esc_attr_translation($str)
    {
        echo esc_attr(self::translation($str));
    }

    /*
     * Inserts a new key/value after the key in the array.
     *
     *   The key to insert after.
     *   An array to insert in to.
     *   The key to insert.
     *   An value to insert.
     * @return
     *   The new array if the key exists, FALSE otherwise.
     *
     * @see array_insert_before()
     */
    public static function array_insert_after($key, array &$array, $new_key, $new_value)
    {
        if (array_key_exists($key, $array)) {
            $new = array();
            foreach ($array as $k => $value) {
                $new[$k] = $value;
                if ($k === $key) {
                    $new[$new_key] = $new_value;
                }
            }

            return $new;
        }

        return false;
    }
}
