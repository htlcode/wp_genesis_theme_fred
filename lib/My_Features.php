<?php
namespace WpGenesisFred;

class My_Features {

	const POST_TYPE = 'feature';

	const FEATURE_PLACE_1 = '_feature_place_1';
	const FEATURE_PLACE_2 = '_feature_place_2';
	const FEATURE_PLACE_3 = '_feature_place_3';

	public function __construct() {
	
		add_action('init', array($this,'create_post_type'));
		add_action('save_post', array($this,'save'));
		add_action('manage_'.self::POST_TYPE.'_posts_columns', array($this,'render_header_columns'));
		add_action('manage_'.self::POST_TYPE.'_posts_custom_column', array($this,'render_columns'), 10, 2 );
	}

	public static function get_feature_list(){
		$list = array(MY_Features::FEATURE_PLACE_1 => __('Feature before post',THEME_NAME), 
					  MY_Features::FEATURE_PLACE_2 => __('Feature after post',THEME_NAME)
					  //MY_Features::FEATURE_PLACE_3 => __('Feature in popup',THEME_NAME)." #2"
					);
		return $list;
	}

	public function render_header_columns($columns) {
		return array_merge(
			$columns,
			self::get_feature_list()
		);
	}

	public function render_columns($column, $post_id) {
		if(self::FEATURE_PLACE_1 == $column) {
				$default_feature = get_post_meta($post_id, self::FEATURE_PLACE_1, true);
				if(intval($default_feature) > 0){
					echo '<span style="font-size:large;color:green;">&#10004;</span>';
				}
		} elseif (self::FEATURE_PLACE_2 == $column) {
				$default_feature2 = get_post_meta($post_id, self::FEATURE_PLACE_2, true);
				if(intval($default_feature2) > 0){
					echo '<span style="font-size:large;color:green;">&#10004;</span>';
				}
		} elseif (self::FEATURE_PLACE_3 == $column) {
				$default_feature3 = get_post_meta($post_id, self::FEATURE_PLACE_3, true);
				if(intval($default_feature3) > 0){
					echo '<span style="font-size:large;color:green;">&#10004;</span>';
				}
		}
	}

	public function create_post_type() {
		$labels = array(
			'name'               => __('Features', THEME_NAME),
			'singular_name'      => __('Feature', THEME_NAME),
			'menu_name'          => __('Features', THEME_NAME),
			'name_admin_bar'     => __('Feature', THEME_NAME),
			'add_new'            => __('Add New', THEME_NAME),
			'add_new_item'       => __('Add New Feature', THEME_NAME),
			'new_item'           => __('New Feature', THEME_NAME),
			'edit_item'          => __('Edit Feature', THEME_NAME),
			'view_item'          => __('View Feature', THEME_NAME),
			'all_items'          => __('All Features', THEME_NAME),
			'search_items'       => __('Search Features', THEME_NAME),
			'parent_item_colon'  => __('Parent Features', THEME_NAME),
			'not_found'          => __('No Features Found', THEME_NAME),
			'not_found_in_trash' => __('No Features Found in Trash', THEME_NAME)
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-format-aside',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'thumbnail','title', 'editor' ),
			'has_archive'         => true,
			'query_var'           => true,
			'register_meta_box_cb' => array($this, 'add_meta_boxes'),
		);

		register_post_type( self::POST_TYPE , $args );
	}

	public function add_meta_boxes() {
	    add_meta_box('feature_metabox_options', __('Default feature place', THEME_NAME), array($this, 'render_feature_metabox_options'), self::POST_TYPE , 'side');
	}

	public function render_feature_metabox_options() {
		global $post;
		$feature_columns = self::get_feature_list();
		echo '<div id="feature_metabox_options">';
		foreach($feature_columns as $feature_column => $feature_label){
			$feature_column_post = substr($feature_column, 1);
			$value = get_post_meta($post->ID, $feature_column, true);
			$checked = '';
			if(intval($value) == 1) {
				$checked = ' checked="checked" ';
			}
			echo '<input type="checkbox" id="'.$feature_column_post.'" name="'.$feature_column_post.'" value="1"'.$checked.' >&nbsp;';
			echo '<label for="$feature_column_post">'.__($feature_label, THEME_NAME).'</label><br>';
		}
		echo "</div>";
	}

	public function save($post_id){
		
		$wp_features = get_posts('numberposts=-1&post_type='.self::POST_TYPE.'&post_status=any');

		$feature_columns = array_keys(self::get_feature_list());

		foreach($feature_columns as $feature_column){
			$feature_column_post = substr($feature_column, 1);
			if(isset($_POST[$feature_column_post])){
				$value = $_POST[$feature_column_post];

				if(isset($value)){
					foreach( $wp_features as $wp_feature ) {
						delete_post_meta( $wp_feature->ID, $feature_column );
					}
				    add_post_meta($post_id, $feature_column, intval($value) );
				} else {
					delete_post_meta($post_id, $feature_column);
				}
			}
		}
	}
}