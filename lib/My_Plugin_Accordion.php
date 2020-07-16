<?php
namespace WpGenesisFred;

class My_Plugin_Accordion {

	public function __construct() {
		add_shortcode('myaccordion',array($this,'print_accordion'));
	}

	public function print_accordion( $atts, $content = null ) {
		$output = '';
		$label_class = '';
	    $params = shortcode_atts(array(
	      'label_id' => '',
	      'label'=> '',
	      'label_icon' => '',
	      'label_class' => '',
	      'open' => 'false',
	    ), $atts);
	    
	    if(empty($params['label_id'])){
   	  		$id = 'myaccordion-label'.$generated_id;
   	  	} else {
   	  		$id = esc_attr($params['label_id']);
   	  	}

	    $generated_id = uniqid();

	    $content_tmp = do_shortcode($content);
	    $content_tmp = force_balance_tags($content_tmp);
		$content_tmp = preg_replace('#<p></p>#i', '', $content_tmp);
		$output .= '<div class="myaccordion">';
	    $output .= '<input id="myaccordion-'.$generated_id.'" type="checkbox" name="myaccordions" ';
	    if($params['open'] == 'true'){
	    	$output .= 'checked';
	    }
	    $output .= '>';
	    if(!empty($params['label_class'])){
	    	$label_class = ' class="'.esc_attr($params['label_class']).'"';
	    }
      	$output .= '<label id="'.$id.'" for="myaccordion-'.$generated_id.'"'.$label_class.'>';
      	if(!empty($params['label_icon'])){
			$output .= '<i class="'.esc_attr($params['label_icon']).'" aria-hidden="true"></i>&nbsp;';
		}
      	$output .= $params['label'];
      	$output .= '</label>';
      	$output .= '<div class="myaccordion-content">';
      	$output .= $content_tmp;
      	$output .= '</div>';
      	$output .= '</div>';
	    return $output;
	}
}