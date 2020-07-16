<?php 
namespace WpGenesisFred;

class My_Plugin_Social
{
	public function __construct(){
		
		add_shortcode( 'my_social', array( $this , 'print_social' ) );
	}

	public function print_social(){
		
		$content = '';
		$facebook_url = esc_attr(genesis_get_option(My_Theme_Options::$text_facebook_url,THEME_SETTINGS_FIELD));
		$twitter_url = esc_attr(genesis_get_option(My_Theme_Options::$text_twitter_url,THEME_SETTINGS_FIELD));
		$youtube_url = esc_attr(genesis_get_option(My_Theme_Options::$text_youtube_url,THEME_SETTINGS_FIELD));
		$instagram_url = esc_attr(genesis_get_option(My_Theme_Options::$text_instagram_url,THEME_SETTINGS_FIELD));
		$linkedin_url= esc_attr(genesis_get_option(My_Theme_Options::$text_linkedin_url,THEME_SETTINGS_FIELD));
		$snapchat_url = esc_attr(genesis_get_option(My_Theme_Options::$text_snapchat_url,THEME_SETTINGS_FIELD));
		
		if(!empty($facebook_url)){
			$content .= '<a href="'.$facebook_url.'" title="Facebook" class="social-icon-facebook" target="_blank"><i class="fa fa-lg fa-facebook-square" aria-hidden="true"></i></a>';
		}
		if(!empty($twitter_url)){
			$content .= '<a href="'.$twitter_url.'" title="Twitter" class="social-icon-twitter" target="_blank"><i class="fa fa-lg fa-twitter-square" aria-hidden="true"></i></a>';
		}
		if(!empty($youtube_url)){
			$content .= '<a href="'.$youtube_url.'" title="Youtube" class="social-icon-youtube" target="_blank"><i class="fa fa-lg fa-youtube-square" aria-hidden="true"></i></a>';
		}
		if(!empty($instagram_url)){
			$content .= '<a href="'.$instagram_url.'" title="Instagram" class="social-icon-instagram" target="_blank"><i class="fa fa-lg fa-instagram" aria-hidden="true"></i></a>';
		}
		if(!empty($linkedin_url)){
			$content .= '<a href="'.$linkedin_url.'" title="Linkedin" class="social-icon-linkedin" target="_blank"><i class="fa fa-lg fa-linkedin-square" aria-hidden="true"></i></a>';
		}
		if(!empty($snapchat_url)){
			$content .= '<a href="'.$snapchat_url.'" title="Snapchat" class="social-icon-snapchat" target="_blank"><i class="fa fa-lg fa-snapchat-square" aria-hidden="true"></i></a>';
		}
		if(!empty($content)){
			$content = '<div id="social">'.$content.'</social>';
		}
		return $content;
	}
}