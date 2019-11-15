<?php
/*
Plugin Name:  vikmypay
Description:  MyPay integration to collect payments through the Vik plugins
Version:      1.0.0
Author:       E4J s.r.l.
Author URI:   https://vikwp.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  vikmypay
Domain Path:  /languages
*/

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

// require utils functions
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'utils.php';

define('VIKMYPAYVERSION', '1.0.0');

/**
* EXAMPLE
* VIKUPDATER HOOKS
*/

function mypay_version_callback() {
    return VIKMYPAYVERSION;
}
add_filter("vikwp_vikupdater_mypay_version", "mypay_version_callback");

function mypay_path_callback() {
    return plugin_dir_path(__FILE__)."..";
}
add_filter("vikwp_vikupdater_mypay_path", "mypay_path_callback");

/**
 * EXAMPLE
 * VIKBOOKING HOOKS
 */

// push the mypay gateway within the supported payments
add_filter('get_supported_payments_vikbooking', function($drivers)
{
	$driver = vikmypay_get_payment_path('vikbooking');

	// make sure the driver exists
	if ($driver)
	{
		$drivers[] = $driver;
	}

	return $drivers;
});

// load mypay payment handler when dispatched
add_action('load_payment_gateway_vikbooking', function(&$drivers, $payment)
{
	// make sure the classname hasn't been generated yet by a different hook
	// and the request payment matches 'mypay' string
	if ($payment == 'mypay')
	{
		$classname = vikmypay_load_payment('vikbooking');

		if ($classname)
		{
			$drivers[] = $classname;
		}
	}
}, 10, 2);

// filter the array containing the logo details to retrieve the correct image
add_filter('vikbooking_oconfirm_payment_logo', function($logo)
{
	if ($logo['name'] == 'mypay')
	{
		$logo['path'] = VIKMYPAY_DIR . DIRECTORY_SEPARATOR . 'vikbooking' . DIRECTORY_SEPARATOR . 'mypay_logo.png';
		$logo['uri']  = VIKMYPAY_URI . 'vikbooking/mypay_logo.png';
	}

	return $logo;
});