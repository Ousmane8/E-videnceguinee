<?php
/**
 * Class CFF_Utils
 *
 * Contains miscellaneous CFF functions
 *
 * @since X.X.X
 */
namespace CustomFacebookFeed;




class CFF_Utils{

	/**
	 * Get JSON object of feed data
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_fetchUrl($url){
		$response = wp_remote_get( $url );

		if ( ! CFF_Utils::cff_is_wp_error( $response ) ) {
			$feedData = wp_remote_retrieve_body( $response );

			if ( ! CFF_Utils::cff_is_fb_error( $feedData ) ) {
				

				\cff_main()->cff_error_reporter->remove_error( 'api' );
				\cff_main()->cff_error_reporter->remove_error( 'wp_remote_get' );

				$feedData = apply_filters( 'cff_filter_api_data', $feedData );

				return $feedData;
			} else {
				if ( strpos( $url, '&limit=' ) !== false ) {
					CFF_Utils::cff_log_fb_error( $feedData, $url );
				}

				return $feedData;
			}

		} else {
			if ( strpos( $url, '&limit=' ) !== false ) {
				CFF_Utils::cff_log_wp_error( $response, $url );
			}

			return '{}';
		}
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_desc_tags($description){
		preg_match_all( "/@\[(.*?)\]/", $description, $cff_tag_matches );
		$replace_strings_arr = array();
		foreach ( $cff_tag_matches[1] as $cff_tag_match ) {
			$cff_tag_parts = explode( ':', $cff_tag_match );
			$replace_strings_arr[] = '<a href="https://facebook.com/'.$cff_tag_parts[0].'">'.$cff_tag_parts[2].'</a>';
		}
		$cff_tag_iterator = 0;
		$cff_description_tagged = '';
		$cff_text_split = preg_split( "/@\[(.*?)\]/" , $description );
		foreach ( $cff_text_split as $cff_desc_split ) {
			if ( $cff_tag_iterator < count( $replace_strings_arr ) ) {
				$cff_description_tagged .= $cff_desc_split . $replace_strings_arr[ $cff_tag_iterator ];
			} else {
				$cff_description_tagged .= $cff_desc_split;
			}
			$cff_tag_iterator++;
		}

		return $cff_description_tagged;
	}
	

	/**
	 * Sort message tags by offset value
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cffSortTags($a, $b){
		return $a['offset'] - $b['offset'];
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_is_wp_error( $response ) {
		return is_wp_error( $response );
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_log_wp_error( $response, $url ) {
		if ( is_wp_error( $response ) ) {
			
			delete_option( 'cff_dismiss_critical_notice' );

			$admin_message_error = '';
			if ( isset( $response ) && isset( $response->errors ) ) {
				foreach ( $response->errors as $key => $item ) {
					$admin_message_error .= ' '.$key . ' - ' . $item[0];
				}
			}

			$admin_message = __( 'Error connecting to the Facebook API:', 'custom-facebook-feed' ) . ' ' . $admin_message_error;
			$public_message =__( 'Unable to make remote requests to the Facebook API. Log in as an admin to view more details.', 'custom-facebook-feed' );
			$frontend_directions = '<p class="cff-error-directions"><a href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a></p>';
			$backend_directions = '<a class="button button-primary" href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>';
			$error = array(
				'accesstoken' => 'none',
				'public_message' => $public_message,
				'admin_message' => $admin_message,
				'frontend_directions' => $frontend_directions,
				'backend_directions' => $backend_directions,
				'post_id' => get_the_ID(),
				'errorno' => 'wp_remote_get'
			);


			\cff_main()->cff_error_reporter->add_error( 'wp_remote_get', $error );
		}
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_is_fb_error( $response ) {
		return (strpos( $response, '{"error":' ) === 0);
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_log_fb_error( $response, $url ) {
		if ( is_admin() ) {
			return;
		}
		
		delete_option( 'cff_dismiss_critical_notice' );

		$access_token_refresh_errors = array( 10, 4, 200 );

		$response = json_decode( $response, true );
		$api_error_code = $response['error']['code'];

    	//Page Public Content Access error
		$ppca_error = false;
		if( strpos($response['error']['message'], 'Public Content Access') !== false ) $ppca_error = true;

		if ( in_array( (int)$api_error_code, $access_token_refresh_errors, true ) && !$ppca_error ) {
			$pieces = explode( 'access_token=', $url );
			$accesstoken_parts = isset( $pieces[1] ) ? explode( '&', $pieces[1] ) : 'none';
			$accesstoken = $accesstoken_parts[0];

			$api_error_number_message = sprintf( __( 'API Error %s:', 'custom-facebook-feed' ), $api_error_code );
			$admin_message = '<strong>' . $api_error_number_message . '</strong><br>' . $response['error']['message'];
			$public_message = __( 'There is a problem with your access token.', 'custom-facebook-feed' ) . ' ' . $api_error_number_message;
			$link = admin_url( 'admin.php?page=cff-top' );
			$frontend_directions = '<p class="cff-error-directions cff-reconnect"><a href="'. esc_url( $link ).'" target="_blank" rel="noopener">' . __( 'Reconnect Your Account in the Admin Area', 'custom-facebook-feed')  . '</a></p>';
			$backend_directions = '<button class="cff-reconnect button button-primary" >' . __( 'Reconnect Your Account', 'custom-facebook-feed')  . '</button>';
			$error = array(
				'accesstoken' => $accesstoken,
				'post_id' => get_the_ID(),
				'errorno' => $api_error_code
			);

			\cff_main()->cff_error_reporter->add_error( 'accesstoken', $error );
		} else {
			$api_error_number_message = sprintf( __( 'API Error %s:', 'custom-facebook-feed' ), $api_error_code );
			$admin_message = '<strong>' . $api_error_number_message . '</strong><br>' . $response['error']['message'];
			$public_message = __( 'Error connecting to the Facebook API.', 'custom-facebook-feed' ) . ' ' . $api_error_number_message;
			$frontend_directions = '<p class="cff-error-directions"><a href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a></p>';
			$backend_directions = '<a class="button button-primary" href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>';

        	//Page Public Content Access admin error
			if( $ppca_error ){
				$api_error_number_message = false;
				$admin_message = '<B>PPCA Error:</b> Due to Facebook API changes it is no longer possible to display a feed from a Facebook Page you are not an admin of. Please use the button below for more information on how to fix this.';
				$public_message = __( 'Error connecting to the Facebook API.', 'custom-facebook-feed' ) . ' ' . $api_error_number_message;
				$frontend_directions = '<p class="cff-error-directions"><a href="https://smashballoon.com/facebook-api-changes-september-4-2020/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a></p>';
				$backend_directions = '<a class="button button-primary" href="https://smashballoon.com/facebook-api-changes-september-4-2020/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>';
			}

			$error = array(
				'accesstoken' => 'none',
				'public_message' => $public_message,
				'admin_message' => $admin_message,
				'frontend_directions' => $frontend_directions,
				'backend_directions' => $backend_directions,
				'post_id' => get_the_ID(),
				'errorno' => $api_error_code
			);

			\cff_main()->cff_error_reporter->add_error( 'api', $error );
		}
	}


	/**
	 * Make links into span instead when the post text is made clickable
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_wrap_span($text) {
		$pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
		return preg_replace_callback($pattern, array('CustomFacebookFeed\CFF_Utils','cff_wrap_span_callback'), $text);
	}
	

	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_wrap_span_callback($matches) {
		$max_url_length = 100;
		$max_depth_if_over_length = 2;
		$ellipsis = '&hellip;';
		$target = 'target="_blank"';
		$url_full = $matches[0];
		$url_short = '';
		if (strlen($url_full) > $max_url_length) {
			$parts = parse_url($url_full);
			$url_short = $parts['scheme'] . '://' . preg_replace('/^www\./', '', $parts['host']) . '/';
			$path_components = explode('/', trim($parts['path'], '/'));
			foreach ($path_components as $dir) {
				$url_string_components[] = $dir . '/';
			}
			if (!empty($parts['query'])) {
				$url_string_components[] = '?' . $parts['query'];
			}
			if (!empty($parts['fragment'])) {
				$url_string_components[] = '#' . $parts['fragment'];
			}
			for ($k = 0; $k < count($url_string_components); $k++) {
				$curr_component = $url_string_components[$k];
				if ($k >= $max_depth_if_over_length || strlen($url_short) + strlen($curr_component) > $max_url_length) {
					if ($k == 0 && strlen($url_short) < $max_url_length) {
                    // Always show a portion of first directory
						$url_short .= substr($curr_component, 0, $max_url_length - strlen($url_short));
					}
					$url_short .= $ellipsis;
					break;
				}
				$url_short .= $curr_component;
			}
		} else {
			$url_short = $url_full;
		}
		return "<span class='cff-break-word'>$url_short</span>";
	}


	/**
	 * Use the timezone to offset the date as all post dates are in UTC +0000
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_set_timezone($original, $cff_timezone){
		$cff_date_time = new \DateTime(date('m/d g:i a'), new \DateTimeZone('UTC'));
		$cff_date_time->setTimeZone(new \DateTimeZone($cff_timezone));
		$cff_date_time_offset = $cff_date_time->getOffset();

		$original = $original + $cff_date_time_offset;

		return $original;
	}
	

	/**
	 * Time stamp functison - used for posts
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_getdate($original, $date_format, $custom_date, $cff_date_translate_strings, $cff_timezone) {
    	//Offset the date by the timezone
		$new_time = CFF_Utils::cff_set_timezone($original, $cff_timezone);
		switch ($date_format) {
			case '2':
				$print = date_i18n('F jS, g:i a', $new_time);
			break;
			case '3':
				$print = date_i18n('F jS', $new_time);
			break;
			case '4':
				$print = date_i18n('D F jS', $new_time);
			break;
			case '5':
				$print = date_i18n('l F jS', $new_time);
			break;
			case '6':
				$print = date_i18n('D M jS, Y', $new_time);
			break;
			case '7':
				$print = date_i18n('l F jS, Y', $new_time);
			break;
			case '8':
				$print = date_i18n('l F jS, Y - g:i a', $new_time);
			break;
			case '9':
				$print = date_i18n("l M jS, 'y", $new_time);
			break;
			case '10':
				$print = date_i18n('m.d.y', $new_time);
			break;
			case '11':
				$print = date_i18n('m/d/y', $new_time);
			break;
			case '12':
				$print = date_i18n('d.m.y', $new_time);
			break;
			case '13':
				$print = date_i18n('d/m/y', $new_time);
			break;
			default:

			$cff_second = $cff_date_translate_strings['cff_translate_second'];
			$cff_seconds = $cff_date_translate_strings['cff_translate_seconds'];
			$cff_minute = $cff_date_translate_strings['cff_translate_minute'];
			$cff_minutes = $cff_date_translate_strings['cff_translate_minutes'];
			$cff_hour = $cff_date_translate_strings['cff_translate_hour'];
			$cff_hours = $cff_date_translate_strings['cff_translate_hours'];
			$cff_day = $cff_date_translate_strings['cff_translate_day'];
			$cff_days = $cff_date_translate_strings['cff_translate_days'];
			$cff_week = $cff_date_translate_strings['cff_translate_week'];
			$cff_weeks = $cff_date_translate_strings['cff_translate_weeks'];
			$cff_month = $cff_date_translate_strings['cff_translate_month'];
			$cff_months = $cff_date_translate_strings['cff_translate_months'];
			$cff_year = $cff_date_translate_strings['cff_translate_years'];
			$cff_years = $cff_date_translate_strings['cff_translate_years'];
			$cff_ago = $cff_date_translate_strings['cff_translate_ago'];

			$periods = array($cff_second, $cff_minute, $cff_hour, $cff_day, $cff_week, $cff_month, $cff_year, "decade");
			$periods_plural = array($cff_seconds, $cff_minutes, $cff_hours, $cff_days, $cff_weeks, $cff_months, $cff_years, "decade");

			$lengths = array("60","60","24","7","4.35","12","10");
			$now = time();

            // is it future date or past date
			if($now > $original) {    
				$difference = $now - $original;
				$tense = $cff_ago;
			} else {
				$difference = $original - $now;
				$tense = $cff_ago;
			}
			for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
				$difference /= $lengths[$j];
			}

			$difference = round($difference);

			if($difference != 1) {
				$periods[$j] = $periods_plural[$j];
			}
			$print = "$difference $periods[$j] {$tense}";

			break;

		}
		if ( !empty($custom_date) ){
			$print = date_i18n($custom_date, $new_time);
		}

		return $print;
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_eventdate($original, $date_format, $custom_date) {
		switch ($date_format) {
			case '2':
				$print = date_i18n('F jS, g:ia', $original);
			break;
			case '3':
				$print = date_i18n('g:ia - F jS', $original);
			break;
			case '4':
				$print = date_i18n('g:ia, F jS', $original);
			break;
			case '5':
				$print = date_i18n('l F jS - g:ia', $original);
			break;
			case '6':
				$print = date_i18n('D M jS, Y, g:iA', $original);
			break;
			case '7':
				$print = date_i18n('l F jS, Y, g:iA', $original);
			break;
			case '8':
			$print = date_i18n('l F jS, Y - g:ia', $original);
			break;
			case '9':
				$print = date_i18n("l M jS, 'y", $original);
			break;
			case '10':
				$print = date_i18n('m.d.y - g:iA', $original);
			break;
			case '11':
				$print = date_i18n('m/d/y, g:ia', $original);
			break;
			case '12':
				$print = date_i18n('d.m.y - g:iA', $original);
			break;
			case '13':
				$print = date_i18n('d/m/y, g:ia', $original);
			break;
			default:
				$print = date_i18n('F j, Y, g:ia', $original);
			break;
		}
		if ( !empty($custom_date) ){
			$print = date_i18n($custom_date, $original);
		}
		return $print;
	}


	/**
	 * Use custom stripos function if it's not available (only available in PHP 5+)
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function stripos($haystack, $needle){
		if( empty( stristr( $haystack, $needle ) ) )
			return false;
		return strpos($haystack, stristr( $haystack, $needle ) );
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_stripos_arr($haystack, $needle) {
		if(!is_array($needle)) $needle = array($needle);
		foreach($needle as $what) {
			if(($pos = CFF_Utils::stripos($haystack, ltrim($what) ))!==false) return $pos;
		}
		return false;
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_mb_substr_replace($string, $replacement, $start, $length=NULL) {
		if (is_array($string)) {
			$num = count($string);
       	 	// $replacement
			$replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
        	// $start
			if (is_array($start)) {
				$start = array_slice($start, 0, $num);
				foreach ($start as $key => $value)
					$start[$key] = is_int($value) ? $value : 0;
			}
			else {
				$start = array_pad(array($start), $num, $start);
			}
        	// $length
			if (!isset($length)) {
				$length = array_fill(0, $num, 0);
			}
			elseif (is_array($length)) {
				$length = array_slice($length, 0, $num);
				foreach ($length as $key => $value)
					$length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
			}
			else {
				$length = array_pad(array($length), $num, $length);
			}
        	// Recursive call
			return array_map(__FUNCTION__, $string, $replacement, $start, $length);
		}
		preg_match_all('/./us', (string)$string, $smatches);
		preg_match_all('/./us', (string)$replacement, $rmatches);
		if ($length === NULL) $length = mb_strlen($string);
		array_splice($smatches[0], $start, $length, $rmatches[0]);
		return join($smatches[0]);
	}


	/**
	 * Push to assoc array
	 * @access public
  	 * @static
	 * @since X.X.X
	 */	
	static function cff_array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}


	/**
	 * Convert string to slug
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_to_slug($string){
		return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
	}


	/**
	 * Convert string to slug
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_get_utc_offset() {
		return get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
	}


	static function cff_schedule_report_email() {
		$options = get_option('cff_style_settings');
		$input = isset( $options[ 'email_notification' ] ) ? $options[ 'email_notification' ] : 'monday';
		$timestamp = strtotime( 'next ' . $input );
		$timestamp = $timestamp + (3600 * 24 * 7);
		$six_am_local = $timestamp + CFF_Utils::cff_get_utc_offset() + (6*60*60);
		wp_schedule_event( $six_am_local, 'cffweekly', 'cff_feed_issue_email' );
	}


	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_is_pro_version() {
		return defined( 'CFFWELCOME_VER' );
	}

	/**
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function cff_get_set_cache($cff_posts_json_url, $transient_name, $cff_cache_time, $cache_seconds, $data_att_html, $cff_show_access_token, $access_token, $backup=false) {
		if( $cff_show_access_token && strlen($access_token) > 130 ){
			//If using a Page Access Token then set caching time to be minimum of 1 minutes
			if( $cache_seconds < 60 || !isset($cache_seconds) ) $cache_seconds = 60;
		} else {
			//Set caching time to be minimum of 30 minutes
			if( $cache_seconds < 1800 || !isset($cache_seconds) ) $cache_seconds = 1800;

			//Temporarily increase default caching time to be 3 hours
			if( $cache_seconds == 3600 ) $cache_seconds = 10800;
		}

		//Don't use caching if the cache time is set to zero
		if ($cff_cache_time != 0){

			// Get any existing copy of our transient data
			if ( false === ( $posts_json = get_transient( $transient_name ) ) || $posts_json === null ) {

				//Get the contents of the Facebook page
				$posts_json = CFF_Utils::cff_fetchUrl($cff_posts_json_url);

				//Check whether any data is returned from the API. If it isn't then don't cache the error response and instead keep checking the API on every page load until data is returned.
				$FBdata = json_decode($posts_json);

				//Check whether the JSON is wrapped in a "data" property as if it doesn't then it's a featured post
				$prefix_data = '{"data":';
				(substr($posts_json, 0, strlen($prefix_data)) == $prefix_data) ? $cff_featured_post = false : $cff_featured_post = true;

				//Add API URL to beginning of JSON array
				$prefix = '{';
				if (substr($posts_json, 0, strlen($prefix)) == $prefix) $posts_json = substr($posts_json, strlen($prefix));

				//Encode and replace quotes so can be stored as a string
				$data_att_html = str_replace( '"', '&quot;', json_encode($data_att_html) );
				$posts_json = '{"api_url":"'.$cff_posts_json_url.'", "shortcode_options":"'.$data_att_html.'", ' . $posts_json;

				//If it's a featured post then it doesn't contain 'data'
				( $cff_featured_post ) ? $FBdata = $FBdata : $FBdata = $FBdata->data;

				//Check the API response
				if( !empty($FBdata) ) {

					//Error returned by API
					if( isset($FBdata->error) ){

						//Cache the error JSON so doesn't keep making repeated requests
						//See if a backup cache exists
						if ( false !== get_transient( '!cff_backup_' . $transient_name ) ) {

							$posts_json = get_transient( '!cff_backup_' . $transient_name );

							//Add error message to backup cache so can be displayed at top of feed
							isset( $FBdata->error->message ) ? $error_message = $FBdata->error->message : $error_message = '';
							isset( $FBdata->error->type ) ? $error_type = $FBdata->error->type : $error_type = '';
							$prefix = '{';
							if (substr($posts_json, 0, strlen($prefix)) == $prefix) $posts_json = substr($posts_json, strlen($prefix));
							$posts_json = '{"cached_error": { "message": "'.$error_message.'", "type": "'.$error_type.'" }, ' . $posts_json;
						}

						//Posts data returned by API
					} else {

						//If a backup should be created for this data then create one
						if( $backup ){
							set_transient( '!cff_backup_' . $transient_name, $posts_json, YEAR_IN_SECONDS );
						}
					}

					//Set regular cache
					set_transient( $transient_name, $posts_json, $cache_seconds );

				}

			} else {

				$posts_json = get_transient( $transient_name );

				if( strpos($posts_json, '"error":{"message":') !== false && false !== get_transient( '!cff_backup_' . $transient_name ) ){
					//Use backup cache if exists
					$posts_json = get_transient( '!cff_backup_' . $transient_name );
				}

				//If we can't find the transient then fall back to just getting the json from the api
				if ($posts_json == false){
					$posts_json = CFF_Utils::cff_fetchUrl($cff_posts_json_url);
				}
			}

		} else {
			$posts_json = CFF_Utils::cff_fetchUrl($cff_posts_json_url);

		}

		return $posts_json;
	}


	/**
	 * Check if On
	 * Function to check if a shortcode options is set to ON or TRUE
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 * @return boolean
	 */
	static function check_if_on( $value ){
		return ( isset( $value ) && !empty( $value ) && ( $value == 'true' || $value == 'on') ) ?  true : false;
	}

	/**
	 * Check if On
	 * Function to check if a shortcode options is set to ON or TRUE
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 * @return boolean
	 */
	static function check_if_onexist( $value ){
		return ( ( isset( $value ) )  ) ?  true : false;
	}

	/**
	 * Check Value
	 * Function to check a value if exists or return a default one 
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 * @return mixed
	 */
	static function return_value( $value , $default = ''){
		return ( isset( $value ) && !empty( $value ) ) ?  $value  : $default;
	}


	/**
	 * Get CSS value
	 * Checks if the value is a valid CSS distance
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 * @return string
	 */
	static function get_css_distance( $value ){
		return ( is_numeric(substr($value, -1, 1)) ) ? $value . 'px' : $value;
	}


	/**
	 * 
	 * 
	 * This function will get the Profile Pic, Cover, Name, About  	 
	 * For the visual header display
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function fetch_header_data( $page_id, $cff_is_group, $access_token, $cff_cache_time ){
		 // Create Transient Name
		    $transient_name = 'cff_header_' . $page_id;
		    $transient_name = substr($transient_name, 0, 45);

	        //These fields only apply to pages
	        !$cff_is_group ? $page_only_fields = ',fan_count,about' : $page_only_fields = '';

	        $header_access_token = $access_token;
	        if( is_array($access_token) ){
	            $header_access_token = reset($access_token);
	            if( empty($header_access_token) ) $header_access_token = key($access_token);
	        }
		    $header_details_json_url = 'https://graph.facebook.com/v4.0/'.$page_id.'?fields=id,picture.height(150).width(150),cover,name,link'.$page_only_fields.'&access_token='. $header_access_token;

		    //Get the data
			$header_details = CFF_Utils::cff_get_set_cache( $header_details_json_url, $transient_name, $cff_cache_time, WEEK_IN_SECONDS, '', false, $access_token );
			$header_details = json_decode( $header_details );
			return $header_details;
	}


	/**
	 * 
	 * 
	 * Print Template
	 * returns an HTML Template
	 * 
	 * @access public
  	 * @static
	 * @since X.X.X
	 */
	static function print_template_part( $template_name, $args = array(), $this_class = null){
		$this_class = $this_class;
		extract($args);
		ob_start();
		include trailingslashit( CFF_PLUGIN_DIR ) . 'templates/' . $template_name . '.php';
		$template = ob_get_contents();
		ob_get_clean();
		return $template;
	}

}