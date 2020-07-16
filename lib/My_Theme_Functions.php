<?php
namespace WpGenesisFred;

// Overwrite the function genesis_author_box
function genesis_author_box( $context = '', $echo = true ) {

	global $authordata;

	$authordata    = is_object( $authordata ) ? $authordata : get_userdata( get_query_var( 'author' ) );
	$gravatar_size = apply_filters( 'genesis_author_box_gravatar_size', 70, $context );
	$gravatar      = get_avatar( get_the_author_meta( 'email' ), $gravatar_size );
	$description   = wpautop( get_the_author_meta( 'description' ) );

	// The author box markup, contextual.
	if ( genesis_html5() ) {

		$title = __( 'About', 'genesis' ) . ' <span itemprop="name">' . get_the_author() . '</span>';

		/**
		 * Author box title filter.
		 *
		 * Allows you to filter the title of the author box. $context passed as second parameter to allow for contextual filtering.
		 *
		 * @since unknown
		 *
		 * @param string $title   Assembled Title.
		 * @param string $context Context.
		 */
		$title = apply_filters( 'genesis_author_box_title', $title, $context );

		$heading_element = 'span';

		if ( 'single' === $context && ! genesis_get_seo_option( 'semantic_headings' ) ) {
			$heading_element = 'span';
		} elseif ( genesis_a11y( 'headings' ) || get_the_author_meta( 'headline', (int) get_query_var( 'author' ) ) ) {
			$heading_element = 'span';
		}

		$pattern  = sprintf( '<section %s>', genesis_attr( 'author-box' ) );
		$pattern .= '%s<' . $heading_element . ' class="author-box-title">%s</' . $heading_element . '>';
		$pattern .= '<div class="author-box-content" itemprop="description">%s</div>';
		$pattern .= '</section>';

	} else {

		$title = apply_filters( 'genesis_author_box_title', sprintf( '<strong>%s %s</strong>', __( 'About', 'genesis' ), get_the_author() ), $context );

		$pattern = '<div class="author-box">%s<span>%s</span><div>%s</div></div>';

		if ( 'single' === $context || get_the_author_meta( 'headline', (int) get_query_var( 'author' ) ) ) {
			$pattern = '<div class="author-box"><div>%s %s<br />%s</div></div>';
		}

	}

	$output = sprintf( $pattern, $gravatar, $title, $description );

	/**
	 * Author box output filter.
	 *
	 * Allows you to filter the full output of the author box.
	 *
	 * @since unknown
	 *
	 * @param string $output  Assembled output.
	 * @param string $context Context.
	 * @param string $pattern (s)printf pattern.
	 * @param string $context Gravatar.
	 * @param string $context Title.
	 * @param string $context Description.
	 */
	$output = apply_filters( 'genesis_author_box', $output, $context, $pattern, $gravatar, $title, $description );

	if ( $echo ) {
		echo $output;

		return null;
	} else {
		return $output;
	}

}