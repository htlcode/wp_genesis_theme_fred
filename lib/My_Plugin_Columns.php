<?php
namespace WpGenesisFred;

class My_Plugin_Columns {

	public function __construct(){
		add_shortcode('mycolumn',array($this,'print_column'));
		add_shortcode('mycolumns',array($this,'print_columns'));
	}

	public function print_column( $atts, $content = '' ) {
		
	    $params = shortcode_atts(array(
	      'class'=> '',
	    ), $atts);

	    $output = trim(do_shortcode($content));
	    global $single_column_array;
	    $single_column_array[] = array('class' => $atts['class'],
	    							   'content' => $output);

	    return $output;
	}

	public function print_columns( $atts, $content = '' ) {
		
		global $single_column_array;
	    $single_column_array = array(); // clear the array

	 	$columns = '';

	    // execute the '[mycolumn]' shortcode first to get the title and content - acts on global $single_column_array
	    do_shortcode($content);
	    
	    foreach ($single_column_array as $i => $column_attr_array) {

	    	$class = '';
	    	if(!empty($column_attr_array['class'])){
	    		$class = ' '.$column_attr_array['class'];
	    	}
			$columns .= '<div class="pas'.$class.'">';
			$content_tmp = force_balance_tags($column_attr_array['content']);
			$content_tmp = preg_replace('#<p></p>#i', '', $content_tmp);
			$columns .= $content_tmp;
			$columns .= '</div>';
	      
	    }
	    $output = '<div class="grid has-gutter mtm mbm">';
	    $output .=	$columns;
	    $output .= '</div>';

	    return $output;
	}
}