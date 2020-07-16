<?php
/*
Template Name: Blank Page with header and footer
*/

remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'genesis_do_loop_for_blank_page' );

function genesis_do_loop_for_blank_page(){
	the_content();
}
genesis();

?>
