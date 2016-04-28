<?php

namespace indagare\config;

// Development
class Config {

	public static $baseURL = "www.indagare.com";

	public static $crm_db = "indagare_staging";

	public static $crm_db_server = "172.31.104.11";

	public static $crm_db_user = "staging03";

	public static $crm_db_pwd = "c0mm0nmySQL";

	public static $pay_host = "staging.linkpt.net";

	public static $pay_port = "1129";

	public static $pay_key = "/home/client02/firstdata/1909749438.pem";

	public static $pay_config = "1909749438";

	public static $payloadserver = "staging.api.indagare.com";

	public static $mode = 'dev';

	public static $external_login_redirect = "http://dev.indagare.com/external-login/";

	public static $swifttrip_url = 'https://indagare.qazone.swifttrip.com';

	public static $bookingform_detailed_id = '75465';
}
