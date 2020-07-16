<?php
namespace WpGenesisFred;

class My_Plugin_Feature_Post {

	public function __construct() {
		add_shortcode('mypost',array($this,'print_post'));
	}

	public function print_post( $atts ) {
		$output = '';
	    $params = shortcode_atts(array(
	      'id'=> '',
	    ), $atts);
	    if(!empty($params['id'])){
	    	$mypost = get_post($params['id']);
	    	$content = $mypost->post_content;
	    	$output = do_shortcode($content);
			$output = apply_filters('the_excerpt',$output);

	    }
	    
	    return $output;
	}
}