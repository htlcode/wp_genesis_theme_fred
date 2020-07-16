<?php
namespace WpGenesisFred;

class My_Posts {

	private $feature_columns;

	// posts and pages
	public static $checkbox_post_meta_date = 'checkbox_post_meta_date';
    public static $checkbox_post_meta_author = 'checkbox_post_meta_author';
    public static $checkbox_post_box_author = 'checkbox_post_box_author';
    public static $checkbox_post_comments = 'checkbox_post_comments';

    // posts only
    public static $checkbox_post_meta_categories = 'checkbox_post_meta_categories';
    public static $checkbox_post_meta_tags = 'checkbox_post_meta_tags';
    public static $checkbox_post_related = 'checkbox_post_related';

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {

		$this->feature_columns = MY_Features::get_feature_list();

		$feature_fields = array_keys($this->feature_columns);

		$post_fields = array(
        	self::$checkbox_post_meta_date,
        	self::$checkbox_post_meta_author,
        	self::$checkbox_post_box_author,
        	self::$checkbox_post_comments,
        	self::$checkbox_post_meta_categories,
        	self::$checkbox_post_meta_tags,
        	self::$checkbox_post_related,
        );
        $this->post_fields = array_merge($post_fields,$feature_fields);

        $page_fields = array(
        	self::$checkbox_post_meta_date,
        	self::$checkbox_post_meta_author,
        	self::$checkbox_post_box_author,
        	self::$checkbox_post_comments,
        );
        $this->page_fields = array_merge($page_fields,$feature_fields);
		
		add_action('add_meta_boxes', array($this,'add_meta_boxes'));
		add_action('save_post', array($this,'save'));
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_boxes($post_type) {

        if ($post_type == 'post') {
        	add_meta_box(
				'post_metabox_hide_fields_for_post'
				,__('Hide fields',THEME_NAME)
				,array($this, 'render_meta_hide_fields_for_post')
				,$post_type
				,'side'
				,'default'
			);
        	
        }elseif($post_type == 'page') {
        	add_meta_box(
				'post_metabox_hide_fields_for_page'
				,__('Hide fields',THEME_NAME)
				,array($this, 'render_metabox_hide_fields_for_page')
				,$post_type
				,'side'
				,'default'
			);
        }

        if ( in_array( $post_type, array('post','page') )) {

			add_meta_box(
				'post_metabox_feature'
				,__('Feature',THEME_NAME)
				,array($this, 'render_meta_box_feature')
				,$post_type
				,'normal'
				,'high'
			);
        }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save($post_id) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		$fields = array();
		if(!isset($_POST['post_metabox_hide_fields_for_post_nonce']) && !isset($_POST['post_metabox_hide_fields_for_page_nonce'])){
			return $post_id;
		} else {
			if(isset($_POST['post_metabox_hide_fields_for_post_nonce'])){
				if (!wp_verify_nonce($_POST['post_metabox_hide_fields_for_post_nonce'], 'post_metabox_hide_fields_for_post')){
					return $post_id;
				}

				if (!current_user_can('edit_post',$post_id)){
					return $post_id;
				}
				$fields = $this->post_fields;

			} elseif(isset($_POST['post_metabox_hide_fields_for_page_nonce'])){
				if (!wp_verify_nonce($_POST['post_metabox_hide_fields_for_page_nonce'], 'post_metabox_hide_fields_for_page')){
					return $post_id;
				}

				if (!current_user_can('edit_page',$post_id)){
					return $post_id;
				}
				$fields = $this->page_fields;
			}
		}

		if (!isset($_POST['post_metabox_feature_nonce'])){
			return $post_id;
		} else {
			if (!wp_verify_nonce($_POST['post_metabox_feature_nonce'],'post_metabox_feature')){
				return $post_id;
			}
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return $post_id;
		}
		
		foreach($fields as $field){
			$value = sanitize_text_field( $_POST[$field] );
			update_post_meta( $post_id, $field, $value);
		}
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_hide_fields_for_post($post){

		// Add an nonce field so we can check for it later.
		wp_nonce_field('post_metabox_hide_fields_for_post', 'post_metabox_hide_fields_for_post_nonce');

		$value = get_post_meta( $post->ID, self::$checkbox_post_box_author , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_box_author.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_box_author.'">'.__('Hide about author', THEME_NAME).'</label></div>';

		$value = get_post_meta( $post->ID, self::$checkbox_post_related , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_related.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_related.'">'.__('Hide related', THEME_NAME).'</label></div>';

	   	$value = get_post_meta( $post->ID, self::$checkbox_post_meta_date , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_meta_date.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_meta_date.'">'.__('Hide published date', THEME_NAME).'</label></div>';

	   	$value = get_post_meta( $post->ID, self::$checkbox_post_meta_author , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_meta_author.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_meta_author.'">'.__('Hide meta author', THEME_NAME).'</label></div>';

	   	$value = get_post_meta( $post->ID, self::$checkbox_post_meta_categories , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_meta_categories.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_meta_categories.'">'.__('Hide meta categories', THEME_NAME).'</label></div>';

	   	$value = get_post_meta( $post->ID, self::$checkbox_post_meta_tags , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_meta_tags.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_meta_tags.'">'.__('Hide meta tags', THEME_NAME).'</label></div>';

		$value = get_post_meta( $post->ID, self::$checkbox_post_comments , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_comments.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for="'.self::$checkbox_post_comments.'">'.__('Hide comments', THEME_NAME).'</label></div>';
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox_hide_fields_for_page($post){

		wp_nonce_field('post_metabox_hide_fields_for_page', 'post_metabox_hide_fields_for_page_nonce');

		$value = get_post_meta( $post->ID, self::$checkbox_post_box_author , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_box_author.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_box_author.'">'.__('Hide about author', THEME_NAME).'</label></div>';

	   	$value = get_post_meta( $post->ID, self::$checkbox_post_meta_date , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_meta_date.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_meta_date.'">'.__('Hide published date', THEME_NAME).'</label></div>';

	   	$value = get_post_meta( $post->ID, self::$checkbox_post_meta_author , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_meta_author.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for='.self::$checkbox_post_meta_author.'">'.__('Hide meta author', THEME_NAME).'</label></div>';

		$value = get_post_meta( $post->ID, self::$checkbox_post_comments , true );
		$value = intval($value);
		echo '<div><input type="checkbox" name="'.self::$checkbox_post_comments.'" value="1" '.checked( $value, 1, false).'>&nbsp;';
	   	echo '<label for="'.self::$checkbox_post_comments.'">'.__('Hide comments', THEME_NAME).'</label></div>';
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_feature($post) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'post_metabox_feature', 'post_metabox_feature_nonce' );

		// Display the form, using the current value.
        $wp_features = get_posts( 'numberposts=-1&post_type='.My_Features::POST_TYPE.'&post_status=publish&orderby=ID&order=ASC' );

        foreach($this->feature_columns as $feature_column => $feature_label){
        	// Use get_post_meta to retrieve an existing value from the database.
        	$value = get_post_meta( $post->ID, $feature_column, true );
		   	echo '<label for="'.$feature_column.'">'.$feature_label.'</label><br>';
		   	echo '<select id="'.$feature_column.'" name="'.$feature_column.'">';
		   	echo '<option value="-1" '.selected($value, '-1' , false).'>'.__('None', THEME_NAME).'</option>';
			
			if(intval($value) == 0){
				$value = '0';
			}
		   	echo '<option value="0" '.selected($value, '0' , false).'>'.__('Default', THEME_NAME).'</option>';
		    
		   	foreach($wp_features as $wp_feature){
		   		$id = strval($wp_feature->ID);
		   		echo '<option value="'.$id.'" '.selected($value, $id , false).'>'.($wp_feature->post_title).'</option>';
		   	}
		   	echo '</select><br>';

        }
	}
}