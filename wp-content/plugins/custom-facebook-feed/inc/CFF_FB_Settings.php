<?php
/**
 * Class CFF_FB_Options
 *
 * Creates a list of necessary options and atts for the Shortcode class
 *
 * @since X.X.X
 */
namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class CFF_FB_Settings {
	/**
	 * @var array
	 */
	protected $atts;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var id
	 */
	protected $page_id;

	/**
	 * @var string
	 */
	protected $access_token;


	/**
	 * CFF_FB_Options constructor.
	 *
	 *
	 * @param array $atts shortcode settings
	 * @param array $options settings from the wp_options table
	 *
 	 * @since X.X.X
	 */
	public function __construct( $atts, $options ) {
		$this->atts 		= $atts;
		$this->options   	= $options;

		$include_string = $this->get_include_string();

		$this->settings = shortcode_atts(
		    array(
		        'accesstoken' 			=> trim( get_option('cff_access_token') ),
		        'id' 					=> get_option('cff_page_id'),
		        'pagetype' 				=> get_option('cff_page_type'),
		        'num' 					=> get_option('cff_num_show'),
		        'limit' 				=> get_option('cff_post_limit'),
		        'others' 				=> '',
		        'showpostsby' 			=> get_option('cff_show_others'),
		        'cachetime' 			=> get_option('cff_cache_time'),
		        'cacheunit' 			=> get_option('cff_cache_time_unit'),
		        'locale' 				=> get_option('cff_locale'),
		        'ajax' 					=> get_option('cff_ajax'),
		        'offset' 				=> '',
		        'account' 				=> '',

		        //General
		        'width' 				=> isset($options[ 'cff_feed_width' ]) ? $options[ 'cff_feed_width' ] : '',
		        'widthresp' 			=> isset($options[ 'cff_feed_width_resp' ]) ? $options[ 'cff_feed_width_resp' ] : '',
		        'height' 				=> isset($options[ 'cff_feed_height' ]) ? $options[ 'cff_feed_height' ] : '',
		        'padding' 				=> isset($options[ 'cff_feed_padding' ]) ? $options[ 'cff_feed_padding' ] : '',
		        'bgcolor' 				=> isset($options[ 'cff_bg_color' ]) ? $options[ 'cff_bg_color' ] : '',
		        'showauthor' 			=> '',
		        'showauthornew' 		=> isset($options[ 'cff_show_author' ]) ? $options[ 'cff_show_author' ] : '',
		        'class' 				=> isset($options[ 'cff_class' ]) ? $options[ 'cff_class' ] : '',
		        'layout' 				=> isset($options[ 'cff_preset_layout' ]) ? $options[ 'cff_preset_layout' ] : '',
		        'include' 				=> $include_string,
		        'exclude' 				=> '',
        		'gdpr' 					=> isset($options[ 'gdpr' ]) ? $options[ 'gdpr' ] : 'auto',

		        //Cols
		        'cols' 					=> isset($options[ 'cff_cols' ]) ? $options[ 'cff_cols' ] : '',
		        'colsmobile' 			=> isset($options[ 'cff_cols_mobile' ]) ? $options[ 'cff_cols_mobile' ] : '',
		        'colsjs' 				=> true,

			    //Mobile settings
		        'nummobile' 			=> isset($options[ 'cff_num_mobile' ]) ? max( 0, (int)$options[ 'cff_num_mobile' ] ) : '',

		        //Post Style
		        'poststyle' 			=> isset($options[ 'cff_post_style' ]) ? $options[ 'cff_post_style' ] : '',
		        'postbgcolor' 			=> isset($options[ 'cff_post_bg_color' ]) ? $options[ 'cff_post_bg_color' ] : '',
		        'postcorners' 			=> isset($options[ 'cff_post_rounded' ]) ? $options[ 'cff_post_rounded' ] : '',
		        'boxshadow' 			=> isset($options[ 'cff_box_shadow' ]) ? $options[ 'cff_box_shadow' ] : '',

		        //Typography
		        'textformat' 			=> isset($options[ 'cff_title_format' ]) ? $options[ 'cff_title_format' ] : '',
		        'textsize' 				=> isset($options[ 'cff_title_size' ]) ? $options[ 'cff_title_size' ] : '',
		        'textweight' 			=> isset($options[ 'cff_title_weight' ]) ? $options[ 'cff_title_weight' ] : '',
		        'textcolor' 			=> isset($options[ 'cff_title_color' ]) ? $options[ 'cff_title_color' ] : '',
		        'textlinkcolor' 		=> isset($options[ 'cff_posttext_link_color' ]) ? $options[ 'cff_posttext_link_color' ] : '',
		        'textlink' 				=> isset($options[ 'cff_title_link' ]) ? $options[ 'cff_title_link' ] : '',
		        'posttags' 				=> isset($options[ 'cff_post_tags' ]) ? $options[ 'cff_post_tags' ] : '',
		        'linkhashtags' 			=> isset($options[ 'cff_link_hashtags' ]) ? $options[ 'cff_link_hashtags' ] : '',

		        //Description
		        'descsize' 				=> isset($options[ 'cff_body_size' ]) ? $options[ 'cff_body_size' ] : '',
		        'descweight' 			=> isset($options[ 'cff_body_weight' ]) ? $options[ 'cff_body_weight' ] : '',
		        'desccolor' 			=> isset($options[ 'cff_body_color' ]) ? $options[ 'cff_body_color' ] : '',
		        'linktitleformat' 		=> isset($options[ 'cff_link_title_format' ]) ? $options[ 'cff_link_title_format' ] : '',
		        'linktitlesize' 		=> isset($options[ 'cff_link_title_size' ]) ? $options[ 'cff_link_title_size' ] : '',
		        'linkdescsize' 			=> isset($options[ 'cff_link_desc_size' ]) ? $options[ 'cff_link_desc_size' ] : '',
		        'linkurlsize' 			=> isset($options[ 'cff_link_url_size' ]) ? $options[ 'cff_link_url_size' ] : '',
		        'linkdesccolor'			=> isset($options[ 'cff_link_desc_color' ]) ? $options[ 'cff_link_desc_color' ] : '',
		        'linktitlecolor' 		=> isset($options[ 'cff_link_title_color' ]) ? $options[ 'cff_link_title_color' ] : '',
		        'linkurlcolor' 			=> isset($options[ 'cff_link_url_color' ]) ? $options[ 'cff_link_url_color' ] : '',
		        'linkbgcolor' 			=> isset($options[ 'cff_link_bg_color' ]) ? $options[ 'cff_link_bg_color' ] : '',
		        'linkbordercolor'		=> isset($options[ 'cff_link_border_color' ]) ? $options[ 'cff_link_border_color' ] : '',
		        'disablelinkbox' 		=> isset($options[ 'cff_disable_link_box' ]) ? $options[ 'cff_disable_link_box' ] : '',

		        //Author
		        'authorsize' 			=> isset($options[ 'cff_author_size' ]) ? $options[ 'cff_author_size' ] : '',
		        'authorcolor' 			=> isset($options[ 'cff_author_color' ]) ? $options[ 'cff_author_color' ] : '',

		        //Event title
		        'eventtitleformat' 		=> isset($options[ 'cff_event_title_format' ]) ? $options[ 'cff_event_title_format' ] : '',
		        'eventtitlesize' 		=> isset($options[ 'cff_event_title_size' ]) ? $options[ 'cff_event_title_size' ] : '',
		        'eventtitleweight' 		=> isset($options[ 'cff_event_title_weight' ]) ? $options[ 'cff_event_title_weight' ] : '',
		        'eventtitlecolor' 		=> isset($options[ 'cff_event_title_color' ]) ? $options[ 'cff_event_title_color' ] : '',
		        'eventtitlelink' 		=> isset($options[ 'cff_event_title_link' ]) ? $options[ 'cff_event_title_link' ] : '',
		        //Event date
		        'eventdatesize' 		=> isset($options[ 'cff_event_date_size' ]) ? $options[ 'cff_event_date_size' ] : '',
		        'eventdateweight' 		=> isset($options[ 'cff_event_date_weight' ]) ? $options[ 'cff_event_date_weight' ] : '',
		        'eventdatecolor' 		=> isset($options[ 'cff_event_date_color' ]) ? $options[ 'cff_event_date_color' ] : '',
		        'eventdatepos' 			=> isset($options[ 'cff_event_date_position' ]) ? $options[ 'cff_event_date_position' ] : '',
		        'eventdateformat' 		=> isset($options[ 'cff_event_date_formatting' ]) ? $options[ 'cff_event_date_formatting' ] : '',
		        'eventdatecustom' 		=> isset($options[ 'cff_event_date_custom' ]) ? $options[ 'cff_event_date_custom' ] : '',
		        //Event details
		        'eventdetailssize' 		=> isset($options[ 'cff_event_details_size' ]) ? $options[ 'cff_event_details_size' ] : '',
		        'eventdetailsweight' 	=> isset($options[ 'cff_event_details_weight' ]) ? $options[ 'cff_event_details_weight' ] : '',
		        'eventdetailscolor' 	=> isset($options[ 'cff_event_details_color' ]) ? $options[ 'cff_event_details_color' ] : '',
		        'eventlinkcolor' 		=> isset($options[ 'cff_event_link_color' ]) ? $options[ 'cff_event_link_color' ] : '',
		        //Date
		        'datepos' 				=> isset($options[ 'cff_date_position' ]) ? $options[ 'cff_date_position' ] : '',
		        'datesize' 				=> isset($options[ 'cff_date_size' ]) ? $options[ 'cff_date_size' ] : '',
		        'dateweight' 			=> isset($options[ 'cff_date_weight' ]) ? $options[ 'cff_date_weight' ] : '',
		        'datecolor' 			=> isset($options[ 'cff_date_color' ]) ? $options[ 'cff_date_color' ] : '',
		        'dateformat' 			=> isset($options[ 'cff_date_formatting' ]) ? $options[ 'cff_date_formatting' ] : '',
		        'datecustom' 			=> isset($options[ 'cff_date_custom' ]) ? $options[ 'cff_date_custom' ] : '',
		        'beforedate' 			=> isset($options[ 'cff_date_before' ]) ? $options[ 'cff_date_before' ] : '',
		        'afterdate' 			=> isset($options[ 'cff_date_after' ]) ? $options[ 'cff_date_after' ] : '',
		        'timezone' 				=> isset($options[ 'cff_timezone' ]) ? $options[ 'cff_timezone' ] : 'America/Chicago',

		        //Link to Facebook
		        'linksize' 				=> isset($options[ 'cff_link_size' ]) ? $options[ 'cff_link_size' ] : '',
		        'linkweight' 			=> isset($options[ 'cff_link_weight' ]) ? $options[ 'cff_link_weight' ] : '',
		        'linkcolor' 			=> isset($options[ 'cff_link_color' ]) ? $options[ 'cff_link_color' ] : '',
		        'viewlinktext' 			=> isset($options[ 'cff_view_link_text' ]) ? $options[ 'cff_view_link_text' ] : '',
		        'linktotimeline' 		=> isset($options[ 'cff_link_to_timeline' ]) ? $options[ 'cff_link_to_timeline' ] : '',
		        //Social
		        'iconstyle' 			=> isset($options[ 'cff_icon_style' ]) ? $options[ 'cff_icon_style' ] : '',
		        'socialtextcolor' 		=> isset($options[ 'cff_meta_text_color' ]) ? $options[ 'cff_meta_text_color' ] : '',
		        'socialbgcolor' 		=> isset($options[ 'cff_meta_bg_color' ]) ? $options[ 'cff_meta_bg_color' ] : '',
		        //Misc
		        'textlength' 			=> get_option('cff_title_length'),
		        'desclength' 			=> get_option('cff_body_length'),
		        'likeboxpos' 			=> isset($options[ 'cff_like_box_position' ]) ? $options[ 'cff_like_box_position' ] : '',
		        'likeboxoutside' 		=> isset($options[ 'cff_like_box_outside' ]) ? $options[ 'cff_like_box_outside' ] : '',
		        'likeboxcolor' 			=> isset($options[ 'cff_likebox_bg_color' ]) ? $options[ 'cff_likebox_bg_color' ] : '',
		        'likeboxtextcolor' 		=> isset($options[ 'cff_like_box_text_color' ]) ? $options[ 'cff_like_box_text_color' ] : '',
		        'likeboxwidth' 			=> isset($options[ 'cff_likebox_width' ]) ? $options[ 'cff_likebox_width' ] : '',
		        'likeboxheight' 		=> isset($options[ 'cff_likebox_height' ]) ? $options[ 'cff_likebox_height' ] : '',
		        'likeboxfaces' 			=> isset($options[ 'cff_like_box_faces' ]) ? $options[ 'cff_like_box_faces' ] : '',
		        'likeboxborder' 		=> isset($options[ 'cff_like_box_border' ]) ? $options[ 'cff_like_box_border' ] : '',
		        'likeboxcover' 			=> isset($options[ 'cff_like_box_cover' ]) ? $options[ 'cff_like_box_cover' ] : '',
		        'likeboxsmallheader' 	=> isset($options[ 'cff_like_box_small_header' ]) ? $options[ 'cff_like_box_small_header' ] : '',
		        'likeboxhidebtn' 		=> isset($options[ 'cff_like_box_hide_cta' ]) ? $options[ 'cff_like_box_hide_cta' ] : '',

		        'credit' 				=> isset($options[ 'cff_show_credit' ]) ? $options[ 'cff_show_credit' ] : '',
		        'nofollow' 				=> 'true',
		        'disablestyles' 		=> isset($options[ 'cff_disable_styles' ]) ? $options[ 'cff_disable_styles' ] : '',
		        'textissue' 			=> isset($options[ 'cff_format_issue' ]) ? $options[ 'cff_format_issue' ] : '',
		        'restrictedpage' 		=> isset($options[ 'cff_restricted_page' ]) ? $options[ 'cff_restricted_page' ] : '',

		        //Page Header
		        'showheader' 			=> isset($options[ 'cff_show_header' ]) ? $options[ 'cff_show_header' ] : '',
		        'headeroutside' 		=> isset($options[ 'cff_header_outside' ]) ? $options[ 'cff_header_outside' ] : '',
		        'headertype' 			=> isset($options[ 'cff_header_type' ]) ? $options[ 'cff_header_type' ] : '',
		        'headercover' 			=> isset($options[ 'cff_header_cover' ]) ? $options[ 'cff_header_cover' ] : '',
		        'headeravatar' 			=> isset($options[ 'cff_header_avatar' ]) ? $options[ 'cff_header_avatar' ] : '',
		        'headername' 			=> isset($options[ 'cff_header_name' ]) ? $options[ 'cff_header_name' ] : '',
		        'headerbio' 			=> isset($options[ 'cff_header_bio' ]) ? $options[ 'cff_header_bio' ] : '',
		        'headercoverheight' 	=> isset($options[ 'cff_header_cover_height' ]) ? $options[ 'cff_header_cover_height' ] : '',
		        'headertext' 			=> isset($options[ 'cff_header_text' ]) ? $options[ 'cff_header_text' ] : '',
		        'headerbg' 				=> isset($options[ 'cff_header_bg_color' ]) ? $options[ 'cff_header_bg_color' ] : '',
		        'headerpadding' 		=> isset($options[ 'cff_header_padding' ]) ? $options[ 'cff_header_padding' ] : '',
		        'headertextsize' 		=> isset($options[ 'cff_header_text_size' ]) ? $options[ 'cff_header_text_size' ] : '',
		        'headertextweight' 		=> isset($options[ 'cff_header_text_weight' ]) ? $options[ 'cff_header_text_weight' ] : '',
		        'headertextcolor' 		=> isset($options[ 'cff_header_text_color' ]) ? $options[ 'cff_header_text_color' ] : '',
		        'headericon' 			=> isset($options[ 'cff_header_icon' ]) ? $options[ 'cff_header_icon' ] : '',
		        'headericoncolor' 		=> isset($options[ 'cff_header_icon_color' ]) ? $options[ 'cff_header_icon_color' ] : '',
		        'headericonsize' 		=> isset($options[ 'cff_header_icon_size' ]) ? $options[ 'cff_header_icon_size' ] : '',
		        'headerinc' 			=> '',
		        'headerexclude' 		=> '',

		        'videoheight' 			=> isset($options[ 'cff_video_height' ]) ? $options[ 'cff_video_height' ] : '',
		        'videoaction' 			=> isset($options[ 'cff_video_action' ]) ? $options[ 'cff_video_action' ] : '',
		        'sepcolor' 				=> isset($options[ 'cff_sep_color' ]) ? $options[ 'cff_sep_color' ] : '',
		        'sepsize' 				=> isset($options[ 'cff_sep_size' ]) ? $options[ 'cff_sep_size' ] : '',

		        //Translate
		        'seemoretext' 			=> isset( $options[ 'cff_see_more_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_see_more_text' ] ) ) : '',
		        'seelesstext' 			=> isset( $options[ 'cff_see_less_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_see_less_text' ] ) ) : '',
		        'photostext' 			=> isset( $options[ 'cff_translate_photos_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_photos_text' ] ) ) : '',
		        'phototext' 			=> isset( $options[ 'cff_translate_photo_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_photo_text' ] ) ) : '',
		        'videotext' 			=> isset( $options[ 'cff_translate_video_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_video_text' ] ) ) : '', 

		        'learnmoretext' 		=> isset( $options[ 'cff_translate_learn_more_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_learn_more_text' ] ) ) : '',    
		        'shopnowtext' 			=> isset( $options[ 'cff_translate_shop_now_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_shop_now_text' ] ) ) : '',    
		        'messagepage' 			=> isset( $options[ 'cff_translate_message_page_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_message_page_text' ] ) ) : '',     

		        'facebooklinktext' 		=> isset( $options[ 'cff_facebook_link_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_facebook_link_text' ] ) ) : '',
		        'sharelinktext' 		=> isset( $options[ 'cff_facebook_share_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_facebook_share_text' ] ) ) : '',
		        'showfacebooklink' 		=> isset($options[ 'cff_show_facebook_link' ]) ? $options[ 'cff_show_facebook_link' ] : '',
		        'showsharelink' 		=> isset($options[ 'cff_show_facebook_share' ]) ? $options[ 'cff_show_facebook_share' ] : '',

		        'secondtext' 			=> isset( $options[ 'cff_translate_second' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_second' ] ) ) : 'second',
		        'secondstext' 			=> isset( $options[ 'cff_translate_seconds' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_seconds' ] ) ) : 'seconds',
		        'minutetext' 			=> isset( $options[ 'cff_translate_minute' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_minute' ] ) ) : 'minute',
		        'minutestext' 			=> isset( $options[ 'cff_translate_minutes' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_minutes' ] ) ) : 'minutes',
		        'hourtext' 				=> isset( $options[ 'cff_translate_hour' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_hour' ] ) ) : 'hour',
		        'hourstext' 			=> isset( $options[ 'cff_translate_hours' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_hours' ] ) ) : 'hours',
		        'daytext' 				=> isset( $options[ 'cff_translate_day' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_day' ] ) ) : 'day',
		        'daystext' 				=> isset( $options[ 'cff_translate_days' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_days' ] ) ) : 'days',
		        'weektext' 				=> isset( $options[ 'cff_translate_week' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_week' ] ) ) : 'week',
		        'weekstext' 			=> isset( $options[ 'cff_translate_weeks' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_weeks' ] ) ) : 'weeks',
		        'monthtext' 			=> isset( $options[ 'cff_translate_month' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_month' ] ) ) : 'month',
		        'monthstext' 			=> isset( $options[ 'cff_translate_months' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_months' ] ) ) : 'months',
		        'yeartext' 				=> isset( $options[ 'cff_translate_year' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_year' ] ) ) : 'year',
		        'yearstext' 			=> isset( $options[ 'cff_translate_years' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_years' ] ) ) : 'years',
		        'agotext' 				=> isset( $options[ 'cff_translate_ago' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_ago' ] ) ) : 'ago'

		    ), $atts);
	}

	/**
	 * @return array
	 *	
	 * @since X.X.X
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get Include String for the Shortcode	
	 * @return array
	 *	
	 * @since X.X.X
	 */
	public function get_include_string() {
		$include_string = '';
		$include_str_array = [
			'cff_show_author'				=> 'author,',
			'cff_show_text'					=> 'text,',
			'cff_show_desc'					=> 'desc,',
			'cff_show_shared_links'			=> 'sharedlinks,',
			'cff_show_date'					=> 'date,',
			'cff_show_media'				=> 'media,',
			'cff_show_media_link'			=> 'medialink,',
			'cff_show_event_title'			=> 'eventtitle,',
			'cff_show_event_details'		=> 'eventdetails,',
			'cff_show_meta'					=> 'social,',
			'cff_show_link'					=> 'link,',
			'cff_show_like_box'				=> 'likebox,'
		];
		foreach ($include_str_array as $key => $value) {			
			if( isset( $this->options[$key] ) &&  $this->options[$key]) $include_string .= $value;
		}
		return $include_string;
	}

	/**
	 * @return array
	 *	
	 * @since X.X.X
	 */
	public function get_id_and_token() {
		$id_and_token = [
			'id' 	=> trim($this->settings['id']),
			'token' => $this->settings['accesstoken']
		];
		//If an 'account' is specified then use that instead of the Page ID/token from the settings
	    $cff_account = trim($this->settings['account']);
	    if( !empty( $cff_account ) ){
	        $cff_connected_accounts = get_option('cff_connected_accounts');
	        if( !empty($cff_connected_accounts) ){
	            $cff_connected_accounts = json_decode( str_replace('\"','"', $cff_connected_accounts) );
	            //Grab the ID and token from the connected accounts setting
	            $id_and_token = [
					'id' 	=> $cff_connected_accounts->{ $cff_account }->{'id'},
					'token' => $cff_connected_accounts->{ $cff_account }->{'accesstoken'}
				];	            
	            //Replace the encryption string in the Access Token
	            if (strpos($id_and_token['token'], '02Sb981f26534g75h091287a46p5l63') !== false) {
	                $id_and_token['token'] = str_replace("02Sb981f26534g75h091287a46p5l63","",$id_and_token['token']);
	            }
	        }
	    }	    
	    $id_and_token['id'] 	= $this->check_page_id( $id_and_token['id'] );
		$this->page_id 	 		= $id_and_token['id'];
		$this->access_token 	= $id_and_token['token'];
		return $id_and_token;
	}



	/**
	 *
	 * Check the Page ID
	 * @return array
	 * @since X.X.X
	 */
	function check_page_id( $page_id ){
		 //If user pastes their full URL into the Page ID field then strip it out
	    $cff_page_id_url_check = CFF_Utils::stripos($page_id, 'facebook.com' );
	    if ( $cff_page_id_url_check ) {
	    	$fb_url_pattern = '/^https?:\/\/(?:www|m)\.facebook.com\/(?:profile\.php\?id=)?([a-zA-Z0-9\.]+)$/';
			$page_id = ( !preg_match($fb_url_pattern, $page_id, $matches) ) ? '' : $matches[1];
	    }
		return $page_id;
	}

}