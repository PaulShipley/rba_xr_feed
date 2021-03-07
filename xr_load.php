<?php
/**********************************************************************
* 
* Load current day Exchange Rates from external provider using cron
* 
* run using:
* cd <fa root dir>
* php xr_load.php
* 
***********************************************************************/

// set environment
$path_to_root=".";
$page_security = 'SA_EXCHANGERATE';


// configure fake server environment for cron
$_SERVER = array();
$_SERVER['REMOTE_ADDR']='batch';
$_SERVER['HTTP_USER_AGENT']='cron';
$_SERVER['REQUEST_URI']='';
$_SERVER['HTTPS']='';
define('FA_LOGOUT_PHP_FILE', 'cronjob');
include_once($path_to_root . "/includes/session.inc");
install_hooks();


// load rates function
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");

/**
* 
* Load Exchange Rates from external provider
*
* @param {object} $company The FA Company to by loaded
* @param {object} $usr     FA User
* @param {object} $pwd     FA Password
* 
* @return
* 
*/
function xr_load($company, $usr, $pwd)
{
	global $_SESSION;

	$_SESSION['wa_current_user']->login($company,$usr,$pwd);

	$date_ = Today();

	foreach (get_currencies() as $curr) {
		$curr_abrev = $curr['curr_abrev'];

		if (!is_company_currency($curr_abrev)) {
			$BuyRate = maxprec_format(retrieve_exrate($curr_abrev, $date_));

			if (get_date_exchange_rate($curr_abrev, $date_) == 0)
			{
				add_exchange_rate($curr_abrev, $date_, $BuyRate, $BuyRate);
			} else {
				update_exchange_rate($curr_abrev, $date_, $BuyRate, $BuyRate);
			}
		}
	}
}

// load rates
xr_load(0,'user','password');

