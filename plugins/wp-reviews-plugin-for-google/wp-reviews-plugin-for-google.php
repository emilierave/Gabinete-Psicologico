<?php
/*
Plugin Name: Widgets for Google Reviews
Plugin Title: Widgets for Google Reviews Plugin
Plugin URI: https://wordpress.org/plugins/wp-reviews-plugin-for-google/
Description: Embed Google reviews fast and easily into your WordPress site. Increase SEO, trust and sales using Google reviews.
Tags: google, google places reviews, reviews, widget, google business, review, testimonial, testimonials, slider, rating, google my business, customer review
Author: Trustindex.io <support@trustindex.io>
Author URI: https://www.trustindex.io/
Contributors: trustindex
License: GPLv2 or later
Version: 8.2.2
Text Domain: wp-reviews-plugin-for-google
Domain Path: /languages/
Donate link: https://www.trustindex.io/prices/
*/
/*
Copyright 2019 Trustindex Kft (email: support@trustindex.io)
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once plugin_dir_path( __FILE__ ) . 'trustindex-plugin.class.php';
$trustindex_pm_google = new TrustindexPlugin_google("google", __FILE__, "8.2.2", "Widgets for Google Reviews", "Google");
register_activation_hook(__FILE__, array($trustindex_pm_google, 'activate'));
register_deactivation_hook(__FILE__, array($trustindex_pm_google, 'deactivate'));
add_action('admin_menu', array($trustindex_pm_google, 'add_setting_menu'), 10);
add_filter('plugin_action_links', array($trustindex_pm_google, 'add_plugin_action_links'), 10, 2);
add_filter('plugin_row_meta', array($trustindex_pm_google, 'add_plugin_meta_links'), 10, 2);
if(!function_exists('register_block_type'))
{
add_action('widgets_init', array($trustindex_pm_google, 'init_widget'));
add_action('widgets_init', array($trustindex_pm_google, 'register_widget'));
}
if(is_file($trustindex_pm_google->getCssFile()))
{
add_action('init', function() {
global $trustindex_pm_google;
if(!isset($trustindex_pm_google) || is_null($trustindex_pm_google))
{
require_once plugin_dir_path( __FILE__ ) . 'trustindex-plugin.class.php';
$trustindex_pm_google = new TrustindexPlugin_google("google", __FILE__, "8.2.2", "Widgets for Google Reviews", "Google");
}
$path = wp_upload_dir()['baseurl'] .'/'. $trustindex_pm_google->getCssFile(true);
if(is_ssl())
{
$path = str_replace('http://', 'https://', $path);
}
wp_register_style('ti-widget-css-google', $path, [], filemtime($trustindex_pm_google->getCssFile()));
});
}
add_action('init', array($trustindex_pm_google, 'init_shortcode'));
add_filter('script_loader_tag', function($tag, $handle) {
if(strpos($tag, 'trustindex.io/loader.js') !== false && strpos($tag, 'defer async') === false) {
$tag = str_replace(' src', ' defer async src', $tag );
}
return $tag;
}, 10, 2);
add_action('init', array($trustindex_pm_google, 'register_tinymce_features'));
add_action('init', array($trustindex_pm_google, 'output_buffer'));
add_action('wp_ajax_list_trustindex_widgets', array($trustindex_pm_google, 'list_trustindex_widgets_ajax'));
add_action('admin_enqueue_scripts', array($trustindex_pm_google, 'trustindex_add_scripts'));
add_action('rest_api_init', array($trustindex_pm_google, 'init_restapi'));
add_action('admin_notices', function() {
$rate_us = get_option('trustindex-google-rate-us', time() - 1);
if($rate_us == 'hide' || (int)$rate_us > time())
{
return;
}
$dir = plugin_dir_path( __FILE__ );
$usage_time = time() + 10;
if(is_dir($dir))
{
$usage_time = filemtime($dir) + (1 * 86400);
}
if($usage_time > time())
{
return;
}
?>
<div class="notice notice-warning is-dismissible trustindex-popup" style="position: fixed; top: 50px; right: 20px; padding-right: 30px; z-index: 1">
<p>
<?php echo TrustindexPlugin_google::___("Hello, I am happy to see that you've been using our <strong>%s</strong> plugin for a while now!", ["Widgets for Google Reviews"]); ?><br>
<?php echo TrustindexPlugin_google::___("Could you please help us and give it a 5-star rating on WordPress?"); ?><br><br>
<?php echo TrustindexPlugin_google::___("-- Thanks, Gabor M."); ?>
</p>
<p>
<a href="<?php echo admin_url("admin.php?page=wp-reviews-plugin-for-google/settings.php&rate_us=open"); ?>" class="trustindex-rateus" style="text-decoration: none" target="_blank">
<button class="button button-primary"><?php echo TrustindexPlugin_google::___("Sure, you deserve it"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=wp-reviews-plugin-for-google/settings.php&rate_us=later"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary"><?php echo TrustindexPlugin_google::___("Maybe later"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=wp-reviews-plugin-for-google/settings.php&rate_us=hide"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary" style="float: right"><?php echo TrustindexPlugin_google::___("Do not remind me again"); ?></button>
</a>
</p>
</div>
<?php
});
if(class_exists('Woocommerce') && !class_exists('TrustindexWoocommercePlugin') && !function_exists('ti_woocommerce_notice'))
{
function ti_woocommerce_notice() {
$rate_us = get_option('trustindex-wc-notification', time() - 1);
if($rate_us == 'hide' || (int)$rate_us > time())
{
return;
}
?>
<div class="notice notice-warning is-dismissible" style="margin: 5px 0 15px">
<p><strong><?php echo TrustindexPlugin_google::___("Download our new <a href='%s' target='_blank'>%s</a> plugin and get features for free!", [ 'https://wordpress.org/plugins/customer-reviews-for-woocommerce/', TrustindexPlugin_google::___('Customer Reviews for WooCommerce') ]); ?></strong></p>
<ul style="list-style-type: disc; margin-left: 10px; padding-left: 15px">
<li><?php echo TrustindexPlugin_google::___('set up Trustindex company profile and get high quality backlink'); ?></li>
<li><?php echo TrustindexPlugin_google::___('set up review-collector campaigns to get new ratings / reviews / recommendations automatically'); ?></li>
<li><?php echo TrustindexPlugin_google::___('show customer reviews in fancy widgets'); ?></li>
</ul>
<p>
<a href="<?php echo admin_url("admin.php?page=wp-reviews-plugin-for-google/settings.php&wc_notification=open"); ?>" target="_blank" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-primary"><?php echo TrustindexPlugin_google::___("Download plugin"); ?></button>
</a>
<a href="<?php echo admin_url("admin.php?page=wp-reviews-plugin-for-google/settings.php&wc_notification=hide"); ?>" class="trustindex-rateus" style="text-decoration: none">
<button class="button button-secondary"><?php echo TrustindexPlugin_google::___("Do not remind me again"); ?></button>
</a>
</p>
</div>
<?php
}
add_action('admin_notices', 'ti_woocommerce_notice');
}
add_action('plugins_loaded', array($trustindex_pm_google, 'plugin_loaded'));
?>