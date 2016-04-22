<?php

namespace indagare\config;

// Stage
class Config {

	public static $baseURL = "www.indagare.com";

	public static $crm_db = "indagare_staging";

	public static $crm_db_server = "172.31.104.11";

	public static $crm_db_user = "staging02";

	public static $crm_db_pwd = "c0mm0nmySQL";

	public static $pay_host = "staging.linkpt.net";

	public static $pay_port = "1129";

	public static $pay_key = "/home/client02/firstdata/1909749438.pem";

	public static $pay_config = "1909749438";

	public static $payloadserver = "staging.api.indagare.com";

	public static $mode = 'stage';

    public static $external_login_redirect = "http://staging.indagare.com/external-login/";

	public static $swifttrip_url = 'https://book.indagare.com';

	public static $bookingform_detailed_id = '75341';
}
