<?php

namespace indagare\config;

// Production, do not override!!!!!!!!!
class Config {

	public static $baseURL = "www.indagare.com";

	public static $crm_db = "indagare_production";

	public static $crm_db_server = "172.31.104.14";

	public static $crm_db_user = "prod01";

	public static $crm_db_pwd = "c0mm0nmySQL";

	public static $pay_host = "secure.linkpt.net";

	public static $pay_port = "1129";

	public static $pay_key = "/home/client02/firstdata/1001177025.pem";

	public static $pay_config = "1001177025";

	public static $payloadserver = "new.api.indagare.com";

	public static $mode = 'prod';

    public static $external_login_redirect = "http://www.indagare.com/external-login/";

	public static $swifttrip_url = 'https://book.indagare.com';

	public static $bookingform_detailed_id = '78802';
}
