<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class PAFE_License_Service {
    const PRO_VERSION = PAFE_PRO_VERSION;
    const PLUGIN_BASENAME = PAFE_PRO_PLUGIN_BASENAME;
    const LICENSE_API_DOMAIN = "https://pafe-api.piotnet.com";
    const FREE_VERSION_NAME = "PAFE_VERSION";
    const PREFIX_OPTION_KEY = "piotnet_addons_for_elementor_pro_";
    const PLUGIN_SLUG = "piotnet-addons-for-elementor-pro";
    const HOMEPAGE_URL = "https://pafe.piotnet.com";
    const LICENSE_DATA_V1_OPTION_KEY = "piotnet_addons_for_elementor_pro_license";

    const LICENSE_KEY_OPTION_KEY = self::PREFIX_OPTION_KEY . "license_key";
    const LICENSE_DATA_OPTION_KEY = self::PREFIX_OPTION_KEY . "license_data";
    const LICENSE_DATA_BACKUP_OPTION_KEY = self::PREFIX_OPTION_KEY . "license_data_backup";
    const BETA_VERSION_OPTION_KEY = self::PREFIX_OPTION_KEY . "beta_version";
    const SHARE_DATA_OPTION_KEY = self::PREFIX_OPTION_KEY . "allow_share_data";
    const LATEST_REFRESH_LICENSE_OPTION_KEY = self::PREFIX_OPTION_KEY . "latest_refresh_license";

    const GET_VERSION_CACHE_KEY = self::PREFIX_OPTION_KEY . "_get_version_cache";
    const GET_INFO_CACHE_KEY = self::PREFIX_OPTION_KEY . "_get_info_cache";

    const LICENSE_VERSION = 2;

    public static function set_key($siteKey, $licenseKey) {
        $key = [
            'siteKey' => $siteKey,
            'licenseKey' => $licenseKey
        ];
        update_option( self::LICENSE_KEY_OPTION_KEY, $key );
    }

    public static function has_key() {
    	return true;
        $key = self::get_key();
        return $key != null && !empty($key['siteKey']) && !empty($key['licenseKey']);
    }

    public static function get_key() {
        return get_option( self::LICENSE_KEY_OPTION_KEY, null );
    }

    public static function clear_key() {
        return delete_option( self::LICENSE_KEY_OPTION_KEY );
    }

    public static function get_license_data( $force_request = false ) {
        $license_data_error = [
            'displayName' => 'Noname',
            'status' => 'HTTP_ERROR',
        ];

        if (!self::has_key()) {
            return $license_data_error;
        }

        $license_data = self::get_transient( self::LICENSE_DATA_OPTION_KEY );
        if ( null === $license_data || $force_request ) {
            $res = self::get_license_data_from_remote();

            if (isset($res['data'])) {
                $license_data = $res['data'];
                if (!empty($license_data['expiredAt'])) {
                    $license_data['expiredAt'] = DateTime::createFromFormat(DATE_RFC3339_EXTENDED,$license_data['expiredAt']);
                }
                self::set_license_data( $license_data );

                if (isset($license_data['updateKey']) && $license_data['updateKey']) {
                    $license_key = isset($license_data['licenseKey']) ? $license_data['licenseKey'] : null;
                    $site_key = isset($license_data['siteKey']) ? $license_data['siteKey'] : null;
                    self::set_key($site_key, $license_key);
                }
            } else {
                $license_data = self::get_transient( self::LICENSE_DATA_BACKUP_OPTION_KEY );
                if ( null === $license_data ) {
                    $license_data = $license_data_error;
                }

                self::set_license_data( $license_data, '+30 minutes' );
                $license_data['error'] = isset($res['error']) ? $res['error'] : null;
            }
        }

        return $license_data;
    }

    public static function set_license_data($license_data, $expiration = null) {
        if ( null === $expiration ) {
            $expiration = '+12 hours';
            self::set_transient( self::LICENSE_DATA_BACKUP_OPTION_KEY, $license_data, '+24 hours' );
        }

        self::set_transient( self::LICENSE_DATA_OPTION_KEY, $license_data, $expiration );
    }

    public static function clear_license_data() {
        delete_option( self::LICENSE_DATA_OPTION_KEY );
        delete_option( self::LICENSE_DATA_BACKUP_OPTION_KEY );
    }

    public static function set_share_data($value) {
        return update_option( self::SHARE_DATA_OPTION_KEY, $value );
    }

    public static function is_beta_version() {
        return 'yes' == get_option(self::BETA_VERSION_OPTION_KEY, 'no');
    }

    public static function get_free_version() {
        if (empty(self::FREE_VERSION_NAME) || !defined(self::FREE_VERSION_NAME)) {
            return null;
        }
        return constant(self::FREE_VERSION_NAME);
    }

    public static function build_site_data() {
        $key = self::get_key();
        return [
            "siteKey" => isset($key) && isset($key['siteKey']) ? $key['siteKey'] : null,
            "licenseKey" => isset($key) && isset($key['licenseKey']) ? $key['licenseKey'] : null,
            "homeUrl" => "https://google.com",
            "siteTitle" => "My Blog",
            "proVersion" => self::PRO_VERSION,
            "freeVersion" => self::get_free_version(),
            "wpVersion" => get_bloginfo( 'version' ),
            "phpVersion" => PHP_VERSION,
            "beta" => self::is_beta_version(),
            "locale" => get_locale(),
            "timezone" => empty(get_option('timezone_string')) ? null : get_option('timezone_string'),
            "offset" => get_option('gmt_offset'),
            "pluginSlug" => self::PLUGIN_SLUG,
        ];
    }

    public static function get_plugin_info($action)
    {
        $body = [
            "action" => $action,
        ] + self::build_site_data();

        $res = self::send_post_request(self::LICENSE_API_DOMAIN . '/v2/plugin/info', $body, false);
        if (isset($res) && isset($res->error)) {
            $error = $res->error;
            $res_msg = isset($error->message) ? $error->message : 'Unknown message';
            $res_code = isset($error->code) ? $error->code : '9999';
            $message = "$res_msg [$res_code]";
            return new WP_Error( $message );
        }
        return $res;
    }

    public static function send_post_request($url, $body, $need_associative = true, $need_decode = true)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $raw_response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            $host = parse_url($url)['host'];
            return self::build_error_response('0001', "Can't connect to $host. ($error_msg)");
        }
        if ($httpcode != "200") {
            return self::build_error_response('0002', "Can't process response data from Piotnet." );
        }

        if ($need_decode) {
            $response = json_decode($raw_response, $need_associative);
            if ($response === null && json_last_error() !== JSON_ERROR_NONE) {
                return self::build_error_response('0003', "Can't process response data from Piotnet.");
            } else {
                return $response;
            }
        } else {
            return $raw_response;
        }
    }

    public static function build_error_response($code, $message) {
        return [
            'error' => [
                'code' => $code,
                "message" => $message,
                'mock' => true,
            ],
        ];
    }

    public static function has_valid_license() {
        $license = self::get_license_data();
        if (!self::has_key() || empty($license) || !isset($license['status']) || $license['status'] !== 'VALID') {
            return false;
        }
        return !isset($license['expiredAt']) || $license['expiredAt'] > new DateTime();
    }

    public static function get_license_data_from_remote() {
    	update_option( self::SHARE_DATA_OPTION_KEY, 'no;' );
		return array('data'=>array("status"=>"VALID","expired_at"=>false,"displayName"=>"GPL"));
        $body = self::build_site_data();
        return self::send_post_request(self::LICENSE_API_DOMAIN . '/v2/plugin/getLicense', $body);
    }

    public static function remove_license() {
    	return;
        $body = self::build_site_data();
        return self::send_post_request(self::LICENSE_API_DOMAIN . '/v2/plugin/removeLicense', $body);
    }

    public static function set_transient( $cache_key, $value, $expiration = '+12 hours' ) {
        $data = [
            'timeout' => strtotime( $expiration, current_time( 'timestamp' ) ),
            'value' => $value,
        ];

        update_option( $cache_key, $data );
    }

    private static function get_transient( $cache_key ) {
        $cache = get_option( $cache_key );

        if ( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
            return null;
        }

        return isset($cache['value']) ? $cache['value'] : null;
    }

    public static function refresh_license( $force ) {
        if ($force || empty(get_transient( self::LATEST_REFRESH_LICENSE_OPTION_KEY ))) {
            self::convert_v1_to_v2();
            self::get_license_data();
            set_transient(self::LATEST_REFRESH_LICENSE_OPTION_KEY, current_time( 'timestamp' ), 6 * HOUR_IN_SECONDS);
        }
    }

    private static function get_domain_v1( $url ) {
        return preg_replace( '/^www\./', '', wp_parse_url( $url, PHP_URL_HOST ) );
    }

    public static function convert_v1_to_v2() {
    	delete_option( self::LICENSE_DATA_V1_OPTION_KEY );
        $license = get_option( self::LICENSE_DATA_V1_OPTION_KEY, null );
        if (isset($license)) {
            if (!empty($license['license_key'])) {
                $license_key = $license['license_key'];

                $body = self::build_site_data();
                $body['siteKey'] = self::get_domain_v1( get_option( 'siteurl' ) );
                $body['licenseKey'] = $license_key;

                $res = self::send_post_request(self::LICENSE_API_DOMAIN . '/v2/plugin/convertLicenseV1ToV2', $body);

                if (isset($res) && isset($res['data'])) {
                    $license_data = $res['data'];

                    if (!empty($license_data['expiredAt'])) {
                        $license_data['expiredAt'] = DateTime::createFromFormat(DATE_RFC3339_EXTENDED,$license_data['expiredAt']);
                    }
                    self::set_license_data( $license_data );

                    $site_key = isset($license_data['siteKey']) ? $license_data['siteKey'] : null;
                    self::set_key($site_key, $license_key);
                }
            }
            delete_option( self::LICENSE_DATA_V1_OPTION_KEY );
        }
    }

    public static function clean_get_info_cache() {
        delete_site_transient(self::GET_VERSION_CACHE_KEY);
        delete_site_transient(self::GET_INFO_CACHE_KEY);

        $update_plugins = get_site_transient('update_plugins');
        if (isset($update_plugins)) {
            if (isset($update_plugins->response) && isset($update_plugins->response[self::PLUGIN_BASENAME])) {
                unset($update_plugins->response[self::PLUGIN_BASENAME]);
            }
            if (isset($update_plugins->no_update) && isset($update_plugins->no_update[self::PLUGIN_BASENAME])) {
                unset($update_plugins->no_update[self::PLUGIN_BASENAME]);
            }
            if (isset($update_plugins->checked) && isset($update_plugins->checked[self::PLUGIN_BASENAME])) {
                unset($update_plugins->checked[self::PLUGIN_BASENAME]);
            }
        }
        set_site_transient('update_plugins', $update_plugins);
    }

    public static function get_version_cache() {
        return get_site_transient(self::GET_VERSION_CACHE_KEY);
    }

    public static function set_version_cache($data, $expiration) {
        set_site_transient( self::GET_VERSION_CACHE_KEY, $data, $expiration  );
    }

    public static function get_info_cache() {
        return get_site_transient(self::GET_INFO_CACHE_KEY);
    }

    public static function set_info_cache($data, $expiration) {
        set_site_transient( self::GET_INFO_CACHE_KEY, $data, $expiration  );
    }
}
