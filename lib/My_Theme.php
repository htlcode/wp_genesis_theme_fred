<?php
namespace WpGenesisFred;

// Include classes
require_once('My_Features.php');
require_once('My_Posts.php');
require_once('My_Options.php');
require_once('My_Theme_Options.php');
require_once('My_Theme_Genesis_Options.php');
require_once('My_Theme_Content.php');
require_once('My_Plugin_Social.php');
require_once('My_Plugin_Tabs.php');
require_once('My_Plugin_Accordion.php');
require_once('My_Plugin_Feature_Post.php');
require_once('My_Plugin_Columns.php');

class My_Theme {

	// Singleton
	private static $instance;

	// Resources of the theme
	private $resources = null;

	private $suffix_file = '.min';

	private $version = null;

	// Create Theme object
    public static function get_instance() {
		if(is_null(self::$instance)) {
			self::$instance = new My_Theme();
		}
		return self::$instance;
	}

	// Constructor
	public function __construct(){
		
		if(current_user_can( 'administrator' )){
			$this->suffix_file = '';

			$my_theme_settings = get_option(THEME_SETTINGS_FIELD, '');
			if(!empty($my_theme_settings) && is_array($my_theme_settings)){
				$this->version = sha1(json_encode($my_theme_settings).CHILD_THEME_VERSION);
			}
		}

		// Initialize needed resources
		$this->ressources = array();
		
		$this->ressources['theme_genesis_options'] = new My_Theme_Genesis_Options();
		
		$this->ressources['features'] = new My_Features();
		$this->ressources['posts'] = new My_Posts();
		$this->ressources['theme_options'] = new My_Theme_Options();
		$this->ressources['plugin_social'] = new My_Plugin_Social();
		$this->ressources['plugin_tabs'] = new My_Plugin_Tabs();
    	$this->ressources['plugin_accordion'] = new My_Plugin_Accordion();
    	$this->ressources['plugin_feature_post'] = new My_Plugin_Feature_Post();
    	$this->ressources['plugin_columns'] = new My_Plugin_Columns();
		
		$this->ressources['theme_content'] = new My_Theme_Content();
		
		// Remove defaults components
		$this->remove_genesis_components();

		// Load theme supports
		$this->load_supports();

		// Load css and javascript
		$this->load_scripts();

		// Add and arrange components for child theme
		$this->arrange_genesis_components();

		// Add wordpress image size for child theme
		$this->add_image_size();

		// Add wrap to videos
		$this->add_wrap_videos();

		// Load translation files
		$this->load_languages();
	}

	// Destructor
	public function __destruct(){
		// Flush memory for all created classes
		$this->ressources = null;
	}

	public function add_wrap_videos(){
		// Add wrap to videos to make them responsive
		add_filter('embed_oembed_html', array($this,'wrap_video_for_responsive'), 99, 4 );
	}

	public function remove_genesis_components(){

		// Removes site layouts
		genesis_unregister_layout('content-sidebar-sidebar');
		genesis_unregister_layout('sidebar-content-sidebar');
		genesis_unregister_layout('sidebar-sidebar-content');

		// Remove 1st Menu
		//remove_action('genesis_after_header','genesis_do_nav') ;

		// Remove footer widgets
		remove_action('genesis_before_footer', 'genesis_footer_widget_areas');

		// Remove footer
		remove_action('genesis_footer','genesis_do_footer');

		// Remove 2nd menu
		remove_action('genesis_after_header','genesis_do_subnav');

		// Remove post info
		remove_action('genesis_entry_header','genesis_post_info',12);
		
		// Remove post thumbnail image
		remove_action('genesis_entry_content','genesis_do_post_image',8);

		// Remove author box on single
		remove_action('genesis_after_entry','genesis_do_author_box_single',8);

		// Remove default edit link
		add_filter ('genesis_edit_post_link' , '__return_false');

		// Remove superfish scripts
		//add_action('wp_enqueue_scripts', array($this,'disable_genesis_superfish_script'));
	}
	
	public function wrap_video_for_responsive($cache, $url, $attr, $post_ID) {
	    $classes = array();

	    $classes_all = array('responsive-embed');

	    if (false !== strpos($url, 'vimeo.com')){
	        $classes[] = 'vimeo';
	    }

	    if (false !== strpos($url, 'youtube.com')){
	        $classes[] = 'youtube';
	    }

	    $classes = array_merge( $classes, $classes_all );

	    return '<div class="' . esc_attr( implode( $classes, ' ' ) ) . '">' . $cache . '</div>';
	}

	public function load_languages(){

		add_action( 'after_setup_theme', array($this,'setup_localization') );
	}

	public function setup_localization() {

		load_child_theme_textdomain(THEME_NAME, get_stylesheet_directory() . '/languages' );
	}

	public function disable_genesis_superfish_script() {

		wp_deregister_script('superfish');
		wp_deregister_script('superfish-args');
	}

	private function load_scripts(){

		add_action('wp_enqueue_scripts', array($this,'prepare_scripts'));
		add_action('admin_enqueue_scripts',array($this,'prepare_admin_scripts'));
	}

	public function prepare_scripts(){

		$this->prepare_css();
		$this->prepare_javascript();
	}

	public function prepare_css(){

		// Remove genesis default style.css
		wp_dequeue_style(THEME_DIR);
		wp_deregister_style(THEME_DIR);
		// Replace default css by this file

		// Get child theme options updated date
		$theme_options = get_option(THEME_SETTINGS_FIELD,'');

		// If child theme options are not updated we read default.css
		if(empty($theme_options)){
			wp_enqueue_style(THEME_DIR, get_stylesheet_directory_uri() . '/css/default.css');
		} else {
			// We read custom.css
			wp_enqueue_style(THEME_DIR, get_stylesheet_directory_uri() . '/css/custom.css',array(),$this->version);
		}
	}

	public function remove_cssjs_ver($src){
   		if(strpos($src,'?ver=')){
        	$src = remove_query_arg('ver', $src);
    	}
    	if(strpos($src,'&ver=')){
        	$src = remove_query_arg('ver', $src);
    	}
	    return $src;
	}

	public function prepare_admin_scripts(){

		$this->prepare_admin_javascript();
	}

	public function prepare_javascript(){
		
		// Declare jquery
		wp_enqueue_script('jquery');

		// Add script for menu
		wp_enqueue_script('genesis-responsive-menu',get_stylesheet_directory_uri()."/js/responsive-menus.min.js", array('jquery'),$this->version,true);
		
		wp_localize_script('genesis-responsive-menu','genesis_responsive_menu', $this->genesis_responsive_menu_settings());

		// Add script for comments
		if (comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}

		// Add child theme public script
    	wp_enqueue_script('public', get_stylesheet_directory_uri()."/js/public{$this->suffix_file}.js",array('jquery'),$this->version,true);
	}

	public function prepare_admin_javascript(){
		wp_enqueue_style('thickbox');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('thickbox');
        wp_enqueue_script('admin', get_stylesheet_directory_uri()."/js/admin{$this->suffix_file}.js",array('wp-color-picker'),$this->version,true);
	}

	public function arrange_genesis_components(){

		// Modify site title : add logo, show/hide titles depending theme settings
		add_filter('genesis_seo_title', array($this,'modify_genesis_title'), 10, 3);
		add_filter('genesis_attr_site-description', array($this,'modify_genesis_attr_site_description'));

		// Move 1st menu right after logo and title
		//add_action('genesis_header','genesis_do_nav');

		// Modify Breadcrumb args
		add_filter('genesis_breadcrumb_args', array($this,'modify_genesis_breadcrumb_sep'));
		add_filter('genesis_single_crumb', array($this,'modify_genesis_breadcrumb_links'), 10, 2 );

		//add_action('genesis_before', array($this,'remove_genesis_breadcrumbs_conditionally') );

		// Modify author box
		add_filter('genesis_author_box', array($this,'modify_genesis_author_box'));

		// Move Post info (date, author, comment link) under excerpt
		add_action('genesis_entry_footer', 'genesis_post_info', 7);
		
		// Move featured image (thumbnail) at the begining of entry
		add_action('genesis_entry_header', 'genesis_do_post_image', 1);

		// Create div to divide featured image from entry text
		add_action('genesis_entry_header', array($this,'modify_genesis_entry_header_markup_open'), 1);
		add_action('genesis_entry_footer', array($this,'modify_genesis_entry_footer_markup_close'), 15);

		// Add class to entry
		add_filter('genesis_attr_entry', array($this,'modify_genesis_attr_entry'));

		// Add class to main content section
		add_filter('genesis_attr_content-sidebar-wrap', array($this,'modify_genesis_attr_content_sidebar_wrap'));

		// Add class to sidebar
		add_filter('genesis_attr_sidebar-primary', array($this,'modify_genesis_attr_sidebar_primary'));

		// Add class to excerpt
		add_filter('genesis_attr_content', array($this,'modify_genesis_attr_content'));

		// Add feature before single entry (post/page) 
		add_action('genesis_entry_header',  array($this,'add_feature_before_post'), 2);

		// Modify entry meta general info/author box for posts (date, author, comment link)
		add_filter('genesis_post_info', array($this,'modify_genesis_post_info'));

		// Modify entry meta for posts (categories and tags)
		add_filter('genesis_post_meta', array($this,'modify_genesis_post_meta'));

		// Add entry meta general info to pages
		add_action('genesis_entry_footer', array($this,'add_page_post_meta'), 4);

		// Add feature after single entry (post/page) 
		add_action('genesis_entry_footer',  array($this,'add_feature_after_post'), 2);

		// Add post edit link when logged
		add_action('genesis_entry_footer', array($this,'add_post_edit_link'), 1);

		// Add author box after entry footer
		add_action('genesis_entry_footer', 'genesis_do_author_box_single', 2);

		add_filter('get_the_author_genesis_author_box_single','__return_true');
		add_filter('genesis_author_box_title', array($this,'modify_genesis_author_box_title'));

		// Add related posts
		add_action('genesis_entry_footer', array($this,'add_related_posts'), 3);

		// Check if we show or hide comments
		add_action('genesis_after_entry', array($this, 'show_hide_genesis_comments'), 0);

		// Modify comments title
		add_filter('genesis_title_comments', array($this,'modify_genesis_title_comments'));

		// Modify comment form
		add_filter('comment_form_defaults',  array($this,'modify_comment_form_defaults'));

		// Modify pager
		add_filter('genesis_prev_link_text', array($this,'modify_previous_link_text'));
		add_filter('genesis_next_link_text', array($this,'modify_next_link_text'));

		// Remove comments author url
		add_filter('get_comment_author_url', array($this,'remove_comment_author_url'), 10, 3);

		// Remove read more link
		add_filter('get_the_content_more_link', array($this,'remove_read_more_link'));

		// Modify widget title
		add_filter('genesis_register_sidebar_defaults' , array($this,'modify_widget_title'));

		// Move footer widget to footer
		add_action('genesis_footer', 'genesis_footer_widget_areas',10);

		// Move secondary menu to footer
		add_action('genesis_footer', 'genesis_do_subnav',11);
		
		// Add custom footer credits
		add_action('genesis_footer', array($this,'modify_genesis_footer'),12);

		// Add some script code after footer
		add_action('genesis_footer', array($this,'add_code_after_footer'),99);

		// Remove superfish for footer menu
		add_filter('wp_nav_menu_args', array($this,'remove_superfish_nav_secondary'));

		// Allow shortcode to categories,tags,author list
		add_filter('genesis_author_intro_text_output','do_shortcode');
		add_filter('genesis_term_intro_text_output','do_shortcode');
		add_filter('genesis_cpt_archive_intro_text_output','do_shortcode');
	}

	public function remove_superfish_nav_secondary( $args) {
	 	if('secondary' == $args['theme_location']) {
	 		$args['menu_class'] = 'menu genesis-nav-menu menu-secondary';
	   	}
		return $args;
	}
	
	public function genesis_responsive_menu_settings() {

		$settings = array(
			'mainMenu'         => '',
			'menuIconClass'    => 'fa fa-bars',
			'subMenu'          => '',
			'subMenuIconClass' => '',
			'menuClasses'      => array(
				'combine' => array(
					'.nav-primary',
				),
				'others'  => array(),
			),
		);

		return $settings;
	}

	public function add_post_edit_link(){
		echo edit_post_link();
	}

	public function modify_genesis_breadcrumb_sep($args){
		$args['sep'] = '<span class="separator">&nbsp;&raquo;&nbsp;</span>';

		return $args;
	}

	public function modify_genesis_breadcrumb_links($crumb, $args){

		$dom = new \DOMDocument;
		
		@$dom->loadHTML(mb_convert_encoding($crumb, 'HTML-ENTITIES', 'UTF-8'));
		$results = $dom->getElementsByTagName('a');
		 
		$links = array();
		$replaced = false;

		foreach($results as $result){
		    
		    $link = $result->getAttribute('href');
		 
		    if(strlen(trim($link)) == 0){
		        continue;
		    }
		 
		    
		    if($link[0] == '#'){
		        continue;
		    }

		    $slug = basename($link);

			$args = array(
			  'name'        => $slug,
			  'post_type'   => 'page',
			  'post_status' => 'publish',
			  'numberposts' => 1
			);
			
			$pages = get_posts($args);
			$new_link = '';

			if(!empty($pages)){
				$page = $pages[0];
				$new_link = get_permalink($page->ID);
				$result->setAttribute('href',$new_link);
				$replaced = true;
			}	
		}

		if($replaced){

			$crumb = $dom->saveHTML();
		}
		
		return $crumb;
	}

	public function remove_genesis_breadcrumbs_conditionally(){
		//remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
	}
	
	public function show_hide_genesis_comments() {
		if(is_single() || is_page()){
			$is_hide = get_post_meta(get_the_ID(), My_Posts::$checkbox_post_comments, true);
			if(!empty($is_hide)){
				remove_action('genesis_after_entry', 'genesis_get_comments_template');
			}
		}
	}

	public function modify_widget_title( $defaults) {
		$defaults['before_title'] = '<span class="widget-title">';
		$defaults['after_title'] = '</span>';
		return $defaults;
	}

	public function remove_read_more_link() {
		return "";
	}

	public function remove_comment_author_url( $url, $id, $commentr) { 
		return ""; 
	}


	public function modify_comment_form_defaults($defaults) {
	    $defaults['title_reply_before'] = '<div id="reply-title" class="comment-reply-title">';
	    $defaults['title_reply_before'] .= '<i class="fa fa-edit" aria-hidden="true"></i>&nbsp;';
	    $defaults['title_reply_after'] = '</div>';

	    return $defaults;
	}

	public function modify_genesis_title_comments() {

		$title = '<div class="comments-title">';
		$title .= '<i class="fa fa-comments" aria-hidden="true"></i>&nbsp;';
		$title .= __('Comments');
		$title .= '</div>';
		return $title;
	}

	public function modify_genesis_attr_site_description( $attributes) {

		if(!empty(genesis_get_option(My_Theme_Options::$checkbox_header_description,THEME_SETTINGS_FIELD))){
			$attributes['class'] .= " screen-reader-text";
		}

		return $attributes;
	}

	public function modify_genesis_title( $title, $inside, $wrap) {

		if(is_home() || is_front_page()){
			$wrap = 'h1';
		}

		$class_hide_title = '';
		if(!empty(genesis_get_option(My_Theme_Options::$checkbox_header_title,THEME_SETTINGS_FIELD))){
			$class_hide_title = ' screen-reader-text';
		}

		// If the custom logo function and custom logo exist, set the logo image element inside the wrapping tags.
		if ( function_exists('has_custom_logo') && has_custom_logo()) {
			$inside = '<div class="logo">'.get_custom_logo().'</div>';
			$inside .= "<$wrap".' class="site-title noresize'.$class_hide_title.'">';
			$inside .= '<a href="'.trailingslashit( home_url()).'">';
			$inside .= esc_html( get_bloginfo('name'));
			$inside .= '</a>';
			$inside .= "</$wrap>";
			
			//$inside = sprintf('%s<span class="screen-reader-text">%s</span>', get_custom_logo() ,));
		} else {
			// If no custom logo, wrap around the site name.
			$inside = "<$wrap".' class="site-title'.$class_hide_title.'">';
			$inside	.= sprintf('<a href="%s">%s</a>', trailingslashit( home_url()), esc_html( get_bloginfo('name')));
			$inside .= "</$wrap>";
		}

		// Build the title.
		$title = genesis_markup( array(
			'open'    => "",
			'close'   => "",
			'content' => $inside,
			'context' => 'site-title',
			'echo'    => false,
			'params'  => array(
				'wrap' => $wrap,
			),
		));

		return $title;
	}

	public function add_feature_before_post(){
		if(is_single() || is_page()){
			$feature = $this->get_feature('before_post');
			if(!empty($feature)): 
				$background = $this->get_feature_background('before_post');
				if(!empty($background)):
			?>
			<style>#feature-before-post{background:url(<?php echo $background;?>);background-repeat:repeat}</style>
			<?php endif; ?>
			<section id="feature-before-post">
			</section>
			<?php
			endif;
		}
	}

	public function add_feature_after_post(){
		if(is_single() || is_page()){
			$feature = $this->get_feature('after_post');
			if(!empty($feature)): 
				$background = $this->get_feature_background('after_post');
				if(!empty($background)):
			?>
			<style>#feature-after-post{background:url(<?php echo $background;?>);background-repeat:repeat}</style>
			<?php endif; ?>
			<section id="feature-after-post">
			</section>
			<?php
			endif;
		}
	}

	public function add_related_posts(){
		$nb_posts = 6;
		$nb_posts_per_line = 3;
		if($this->check_show_hide(My_Theme_Options::$checkbox_post_related,My_Posts::$checkbox_post_related)){
			if(is_single()){
				$related_posts = $this->ressources['theme_content']->get_related_posts($nb_posts);
				$i = 0;
				if(count($related_posts) > 0):
				?>
				<section class="related-posts">
					<span class="related-title"><i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp;<?php echo __('Related posts',THEME_NAME) ?></span>
					<?php foreach(array_chunk($related_posts, $nb_posts_per_line) as $related_posts_set):
						$i++;
						if($i > 1){
							echo '<div class="mts"></div>';
						}
					?>
					<ul class="grid has-gutter">
						<?php foreach($related_posts_set as $related_post): ?>
						<li class="related">
							<div class="related-image"><a href="<?php echo $related_post['permalink']; ?>" class="ripple"><?php echo $related_post['thumbnail']; ?></a></div>
							<div class="related-link"><a href="<?php echo $related_post['permalink']; ?>"><?php echo $related_post['post_title']; ?></a></div>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php endforeach; ?>
				</section>
				<?php
				endif;
			}
		}
	}

	public function check_show_hide($general_prop,$single_prop){

		$is_show = true;
		if(!empty($single_prop)){
			if(is_single() || is_page()){
				$is_post_hide = get_post_meta(get_the_ID(), My_Posts::${$single_prop}, true);
				if(!empty($is_post_hide)){
					$is_show = false;
				}
			}
		}

		if(!empty($general_prop)){
			$is_general_hide = genesis_get_option(My_Theme_Options::${$general_prop},THEME_SETTINGS_FIELD);
			if($is_general_hide){ 
				$is_show = false;
			}
		}

		return $is_show;
	}

	public function modify_genesis_author_box($content) {

		$hide_class = '';
		if(!$this->check_show_hide(My_Theme_Options::$checkbox_post_box_author,My_Theme_Options::$checkbox_post_box_author)){
			$hide_class = ' screen-reader-text';
		}

		$str0 = 'class="author-box"';
		$rep0 = 'class="author-box'.$hide_class.'"';
		$content = str_replace($str0, $rep0, $content);

		$str1 = 'itemtype="https://schema.org/Person">';
		$rep1 = '<div class="author-box-heading">';
		$rep1 .= '<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;';
		
		$rep1 .= __('About the author', THEME_NAME);
		$rep1 .= '</div>';

		$content = str_replace($str1, $str1.$rep1, $content);

		$str2 = 'h4';
		$rep2 = 'span';

		$content = str_replace($str2, $rep2, $content);

		$str3 = 'class="author-box-title"';
		$rep3 = 'class="author-box-title" itemprop="name"';

		$content = str_replace($str3, $rep3, $content);
		
		return $content;
	}


	public function modify_genesis_author_box_title() {

		$author_name = get_the_author();
		return $author_name;
	}

	public function modify_genesis_footer() {
		if(genesis_get_option(My_Theme_Options::$text_footer_credits,THEME_SETTINGS_FIELD)):
			$text_footer_credits = genesis_get_option(My_Theme_Options::$text_footer_credits,THEME_SETTINGS_FIELD);
		?>
		<div class="credits"><?php echo nl2br(do_shortcode($text_footer_credits)) ?></div>
		<?php
		endif;
	}



	public function modify_previous_link_text($text) {
        $value = __('Previous Page', THEME_NAME);
        $text = "&larr; $value";
        return $text;
	}

	public function modify_next_link_text($text) {
		$value = __('Next Page', THEME_NAME);
        $text = "$value &rarr;";
        return $text;
	}

	
	public function modify_genesis_post_meta($post_meta) {
		$post_meta = '';

		if($this->check_show_hide(My_Theme_Options::$checkbox_post_meta_categories,
							      My_Posts::$checkbox_post_meta_categories)){
			$category_before = "<span class='meta-info mrs'><i class='fa fa-archive' aria-hidden='true'></i>&nbsp;";
			$category_after = "</span>";

			$meta_categories = '[post_categories sep=", " before="'.$category_before.'" after="'.$category_after.'"]';
		} else {
			$meta_categories = '';
		}
	
		$post_meta = $meta_categories;

		if($this->check_show_hide(My_Theme_Options::$checkbox_post_meta_tags,
							      My_Posts::$checkbox_post_meta_tags)){
			$tag_before = "<span class='meta-info mrs'><i class='fa fa-tag' aria-hidden='true'></i>&nbsp;";
			$tag_after = "</span>";
			$meta_tags ='[post_tags sep=", " before="'.$tag_before.'" after="'.$tag_after.'"]';
		} else {
			$meta_tags = '';
		}
		
		if(!empty($post_meta)){
			$post_meta = $post_meta . ' ';
		}
		$post_meta = $post_meta . $meta_tags;

		return $post_meta;
	}

	public function add_page_post_meta() {

		if ( is_page()) {
			$post_info = $this->modify_genesis_post_info('');
			genesis_author_box('single');
			printf('<div class="entry-footer"><p class=”entry-meta”>%s</p></div>', do_shortcode( $post_info));
		}
	}

	public function modify_genesis_post_info($post_info) {

		$str_updated = __('Updated', THEME_NAME);
		if($this->check_show_hide(My_Theme_Options::$checkbox_post_meta_date,
								  My_Posts::$checkbox_post_meta_date)){

			$date_before = "<span class='meta-info mrs'><i class='fa fa-clock-o' aria-hidden='true'></i>&nbsp;";
			$date_after = "</span>";
			$date_before2 = "<span class='meta-info mrs'>(".$str_updated."&nbsp;:&nbsp;";
			$date_after2 = ")</span>";
		} else {
			$date_before = "<span class='screen-reader-text meta-info'><i class='fa fa-clock-o' aria-hidden='true'></i>&nbsp;";
			$date_after = "</span>";
			$date_before2 = "<span class='screen-reader-text meta-info'>(".$str_updated."&nbsp;:&nbsp;";
			$date_after2 = ")</span>";
		}

		if($this->check_show_hide(My_Theme_Options::$checkbox_post_meta_author,
								  My_Posts::$checkbox_post_meta_author)){
			$author_before = "<span class='meta-info mrs'><i class='fa fa-user' aria-hidden='true'></i>&nbsp;";
			$author_after = "</span>";
		} else {
			$author_before = "<span class='screen-reader-text meta-info'><i class='fa fa-user' aria-hidden='true'></i>&nbsp;";
			$author_after = "</span>";
		}

		$comment_before = "<span class='meta-info mrs'><i class='fa fa-comments' aria-hidden='true'></i>&nbsp;";
		$comment_after = "</span>";

		$post_info = '[post_date before="'.$date_before.'" after="'.$date_after.'"][post_modified_date before="'.$date_before2.'" after="'.$date_after2.'"]';

		$is_show = false;
		if(is_single() || is_page()){
			$val = get_post_meta(get_the_ID(), My_Posts::$checkbox_post_comments, true);
			if(!empty($val)){
				$is_show = !get_post_meta(get_the_ID(), My_Posts::$checkbox_post_comments, true);
			}
		}
		if($is_show){
			if(is_single()){
				$is_show = genesis_get_option('comments_posts');
			} elseif(is_page()) {
				$is_show = genesis_get_option('comments_pages');
			} else {
				$is_show = genesis_get_option('comments_posts');
			}
		}
		if($is_show){
			$is_show = comments_open();
		}
		if(!empty($is_show)){
			$comment_str = __('Leave a Comment', THEME_NAME);
			$post_info .= '[post_comments zero="'.$comment_str.'" one="1" more="%" hide_if_off="disabled" before="'.$comment_before.'" after="'.$comment_after.'"] ';
		}
		
		$post_info .= '[post_author_posts_link before="'.$author_before.'" after="'.$author_after.'"] ';

		return $post_info;
	}

	public function modify_genesis_attr_entry($attributes){
		if(!is_singular()){
			$attributes['class'] = $attributes['class'].' flex-container feed-entry';
		}
 		return $attributes;
	}

	public function modify_genesis_attr_content_sidebar_wrap($attributes) {
		
		if(has_nav_menu('primary')){
			$class = 'site-content-canvas-with-menu';
		} else {
			$class = 'site-content-canvas-without-menu';
		}

		$attributes['class'] = $attributes['class']." flex-container $class";

 		return $attributes;
	}

	public function modify_genesis_attr_sidebar_primary($attributes) {
		
		$site_layout = genesis_site_layout();

		$sidebar_class = '';	
		if($site_layout == 'sidebar-content'){
			$sidebar_class = ' left-sidebar';
		} elseif($site_layout == 'content-sidebar'){
			$sidebar_class = ' right-sidebar';
		}
		
		$attributes['class'] = $attributes['class'].' w400p'.$sidebar_class;
		
 		return $attributes;
	}

	public function modify_genesis_attr_content($attributes) {
		$class = ' item-fluid';
		if(!is_single() && !is_page()){
			$class .= ' main-feed';
		} else {
			if(basename(get_page_template()) != 'template-blank-with-header-footer.php'){
				if(genesis_site_layout() == 'full-width-content'){
					$class .= ' main-single main-single-full';
				} else {
					$class .= ' main-single main-single-half';
				}
			} else {
				$class = ' main-blank-full';
			}
		}

		$attributes['class'] = $attributes['class'].$class;

 		return $attributes;
	}

	public function modify_genesis_entry_header_markup_open() {
		if(!is_single() && !is_page()){
			echo '<div class="feed-entry-info">';
		}
	}

	public function modify_genesis_entry_footer_markup_close() {
		if(!is_single() && !is_page()){
			echo '</div>';
		}
	}

	public function add_code_after_footer(){

$feature_before_post = My_Theme::get_instance()->get_feature('before_post');
$feature_after_post = My_Theme::get_instance()->get_feature('after_post');

if(!empty($feature_before_post) || !empty($feature_after_post)):
?>
<script>
var $j = jQuery.noConflict();
var feature_before_post = <?php echo json_encode($feature_before_post) ?>;
var feature_after_post = <?php echo json_encode($feature_after_post) ?>;
$j(function(){if(feature_before_post){$j('#feature-before-post').append(feature_before_post);}if(feature_after_post){$j('#feature-after-post').append(feature_after_post);}});
</script>
<?php endif;
?>
<div id="scrollup"><i class="fa fa-arrow-up noresize" aria-hidden="true"></i>
</div>
<?php
	}

	private function load_supports(){

		// Adds support post's thumbnail
		add_theme_support('post-thumbnails');

		// Add RSS feed links to HTML <head>
		add_theme_support('automatic-feed-links');
		
		// Adds support for HTML5 markup structure.
		add_theme_support(
			'html5', array(
				'caption',
				'comment-form',
				'comment-list',
				'gallery',
				'search-form',
			)
		);

		// Adds support for accessibility.
		add_theme_support(
			'genesis-accessibility', array( 
				'404-page', 
				'drop-down-menu', 
				'headings', 
				'rems', 
				'search-form', 
				'skip-links' 
			) 
		);

		// Adds viewport meta tag for mobile browsers.
		add_theme_support(
			'genesis-responsive-viewport'
		);

		// Adds custom logo in Customizer > Site Identity.
		add_theme_support(
			'custom-logo', array(
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		// Adds custom header in Customizer
		add_theme_support(
			'custom-header', array(
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		// Renames primary and secondary navigation menus.
		add_theme_support(
			'genesis-menus', array(
				'primary'   => __('Header Menu', THEME_NAME),
				'secondary' => __('Footer Menu', THEME_NAME),
			)
		);

		// Adds support for after entry widget.
		add_theme_support('genesis-after-entry-widget-area');

		// Adds support for 3-column footer widgets.
		add_theme_support('genesis-footer-widgets', 3);

		// Remove genesis <div class="wrap">
		remove_theme_support('genesis-structural-wraps');
	}

	public function get_feature($param){
		$feature_content = '';
		$key = null;
		$feature_id = 0;
		
		if($param == 'before_post'){
			$key = My_Features::FEATURE_PLACE_1;
		} elseif ($param == 'after_post') {
			$key = My_Features::FEATURE_PLACE_2;
		}

		if(!empty($key)){
			
	        if(is_home()){
	            //$feature_id = $this->get_option($key);
	        } elseif(is_single() || is_page()){
	            $post_id = get_the_ID();
	            $feature_id = get_post_meta($post_id , $key, true);

	        } elseif(is_category()){
	        	$term_id = get_queried_object_id();
	        	$feature_id = get_term_meta($term_id , $key, true);
	        }

	        if(intval($feature_id) != 0){
	            if(intval($feature_id)>0){
	                $wp_feature = get_post($feature_id); 
	                $feature_content = $wp_feature->post_content;
	            }
	        } else {
	        	$args = array(
	                'posts_per_page' => 1,
	                'post_type' => My_Features::POST_TYPE,
	                'meta_query' => array(
	                    array(
	                        'key' => $key,
	                        'value' => "1",
	                  )
	              )
	          );
	           $wp_features = get_posts($args);
	           if(count($wp_features) > 0){
	           		$feature_content = $wp_features[0]->post_content;
	           }
	        }
    	}

    	if(!empty($feature_content)){
    		//For WP Offload Media
    		if(has_filter('as3cf_filter_post_local_to_provider')){
    			$feature_content = apply_filters('as3cf_filter_post_local_to_provider',$feature_content);
    		}
    	}

        return $feature_content;
	}

	public function get_feature_background($param){
		$thumb = '';
		$key = null;
		$feature_id = 0;
		
		if($param == 'before_post'){
			$key = My_Features::FEATURE_PLACE_1;
		} elseif ($param == 'after_post') {
			$key = My_Features::FEATURE_PLACE_2;
		}

		if(!empty($key)){
			
	        if(is_home()){
	            //$feature_id = $this->get_option($key);
	        } elseif(is_single() || is_page()){
	            $post_id = get_the_ID();
	            $feature_id = get_post_meta($post_id , $key, true);
	        } elseif(is_category()){
	        	$term_id = get_queried_object_id();
	        	$feature_id = get_term_meta($term_id , $key, true);
	        }

	        if(intval($feature_id) != 0){
	            if(intval($feature_id)>0){
	            	$thumb_id = get_post_thumbnail_id($feature_id);
	            	if($thumb_id){
	            		$array = wp_get_attachment_image_src($thumb_id, 'full');
	            		$thumb = $array[0];
	            	}
	            }
	        }  else {
				$args = array(
				    'posts_per_page' => 1,
				    'post_type' => My_Features::POST_TYPE,
				    'meta_query' => array(
				        array(
				            'key' => $key,
				            'value' => "1",
				      )
				  )
				);
				$wp_features = get_posts($args);
				if(count($wp_features) > 0){
					$feature_id = $wp_features[0]->ID;
					$thumb_id = get_post_thumbnail_id($feature_id);
					if($thumb_id){
						$array = wp_get_attachment_image_src($thumb_id, 'full');
						$thumb = $array[0];
					}
				}
	        }
    	}
        return $thumb;
	}

	private function add_image_size(){

		$this->image_sizes = array('medium_large' => array('width'=>'768','height'=>'0','label'=> __('Medium Large',THEME_NAME)),
								   'facebook_wide' => array('width'=>'476','height'=>'0','label'=> __('Facebook Wide',THEME_NAME)),
								   'thumb_wide' => array('width'=>'300','height'=>'200','label'=> __('Thumb Wide',THEME_NAME)));

		foreach($this->image_sizes as $tag => $image_size){
			add_image_size($tag, $image_size['width'], $image_size['height'], false); 
		}

		add_filter('image_size_names_choose', array($this,'add_custom_thumb'));

		add_filter('img_caption_shortcode', array($this,'resize_caption_shortcode_width'), 10, 3);
	}

	public function resize_caption_shortcode_width($dummy, $attr, $content){
	    $atts = shortcode_atts( array(
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => '',
			'class'   => '',
		), $attr, 'caption' );
		$atts['width'] = (int) $atts['width'];
		if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
			return $content;
		if ( ! empty( $atts['id'] ) )
			$atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';
		$class = trim( 'wp-caption ' . $atts['align'] . ' ' . $atts['class'] );
		if ( current_theme_supports( 'html5', 'caption' ) ) {
			$figure = apply_filters( 'ccd_resonsive_figures', '<figure ' . $atts['id'] . 'style="max-width: ' . (int) $atts['width'] . 'px;" class="' . esc_attr( $class ) . '">' . do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>' );
			return $figure;
		}
		return '';
	}

	public function add_custom_thumb($sizes) {
		$addsizes = array();
		foreach($this->image_sizes as $tag => $image_size){
			$addsizes[$tag] = $image_size['label'];
		}
		
		$newsizes = array_merge($sizes, $addsizes);
		return $newsizes;
	}
}