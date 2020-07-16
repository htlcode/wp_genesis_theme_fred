<?php
namespace WpGenesisFred;

class My_Plugin_Tabs {
	
	public function __construct(){
		add_shortcode('mytab',array($this,'print_tab'));
		add_shortcode('mytabs',array($this,'print_tabs'));
	}

	public function print_tab( $atts, $content = '' ) {

	    $params = shortcode_atts(array(
	      'label_id'=> '',
	      'label'	=> '',
	      'label_icon'    => '',
	      'label_class'   => '',
	    ), $atts);
	    
	    $output = trim(do_shortcode($content));
	    global $single_tab_array;
	    $single_tab_array[] = array('label_id' => esc_attr($params['label_id']), 
	    							'label' => $params['label'],
	    							'label_icon' => esc_attr($params['label_icon']),
	    							'label_class' => esc_attr($params['label_class']), 
	    							'content' => $output);
	    return $output;
	}

	public function print_tabs( $atts, $content = '' ) {
		
		global $single_tab_array;
	    $single_tab_array = array(); // clear the array
	    
	    $tab_style = array();
	    $tabs_nav = '';
	    $tabs_content = '';
	    $tabs_output = '';
	 	
	    // execute the '[tab]' shortcode first to get the label title and content - acts on global $single_tab_array
	    do_shortcode($content);

	    foreach ($single_tab_array as $i => $tab_attr_array) {
	   		$label_class = '';
	   		$generated_id = uniqid ();
	   		
	   	  	if(empty($tab_attr_array['label_id'])){
	   	  		$id = 'mytab-label'.$generated_id;
	   	  	} else {
	   	  		$id = $tab_attr_array['label_id'];
	   	  	}

	   	  	$checked = "";
	   	  	if ($i == 0){
	   	  		$checked = " checked";
	   	  	}

			$tab_style[] .= '#mytab'.$generated_id.':checked ~ #mytab-content'.$generated_id;

			$tabs_nav .= '<input type="radio" name="mytab" id="mytab'.$generated_id.'"'.$checked.' />';
			if(!empty($tab_attr_array['label_class'])){
	    		$label_class = ' class="'.$tab_attr_array['label_class'].'"';
	    	}
			$tabs_nav .= '<label id="'.$id.'" class="mytab-label" for="mytab'.$generated_id.'"'.$label_class.'>';
			if(!empty($tab_attr_array['label_icon'])){
				$tabs_nav .= '<i class="'.$tab_attr_array['icon'].'" aria-hidden="true"></i>&nbsp;';
			}

			$tabs_nav .= $tab_attr_array['label'];
			$tabs_nav .= '</label>';

			$tabs_content .= '<div id="mytab-content'.$generated_id.'" class="mytab-content pas">';
			$content_tmp = force_balance_tags($tab_attr_array['content']);
			$content_tmp = preg_replace('#<p></p>#i', '', $content_tmp);
			$tabs_content .= $content_tmp;
			$tabs_content .= '</div>';
	      
	    }

	    $tabs_output = '<div class="mytabs mts mbs">';
	    $tabs_output .= '<style>'.implode(',',$tab_style).'{display:block}</style>';
	    $tabs_output .= $tabs_nav.'<div class="mytab-sep"></div>';
	    $tabs_output .=	$tabs_content;
	    $tabs_output .= '</div>';

	    return $tabs_output;
	}
}

