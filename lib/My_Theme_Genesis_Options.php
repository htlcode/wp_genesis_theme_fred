<?php
namespace WpGenesisFred;

class My_Theme_Genesis_Options {

	public function __construct(){

	    add_action('after_switch_theme', array($this,'set_default_options'));
	    add_filter('genesis_theme_settings_defaults', array($this,'initialize_genesis_theme_settings_defaults'));

	    $this->defaults = array(
           'update'                    => 0,  // bool (check update)
           'update_email_address'      => '', // string (email)
           'feed_uri'                  => '', // string (url)
           'redirect_feed'             => 0,  // bool 
           'comments_feed_uri'         => '', // string (url)
           'redirect_comments_feed'    => 0,  // bool
           'site_layout'               => 'sidebar-content', // radio ( content-sidebar, sidebar-content, full-width-content )
           'blog_title'                => 'text', // select ( text, image )
           'breadcrumb_home'           => 0,  // bool (breadcrumb on homepage)
           'breadcrumb_single'         => 1,  // bool (breadcrumb on post)
           'breadcrumb_page'           => 1,  // bool (breadcrumb on page)
           'breadcrumb_archive'        => 1,  // bool (breadcrumb on archive)
           'breadcrumb_404'            => 1,  // bool (breadcrumb on 404 page)
           'breadcrumb_attachment'     => 0,  // bool (breadcrumb on attachement)
           'comments_posts'            => 1,  // bool (allow comments on post)
           'comments_pages'            => 1,  // bool (allow comments on page)
           'trackbacks_posts'          => 0,  // bool (trackback comments on post)
           'trackbacks_pages'          => 0,  // bool (trackback comments on page)
           'content_archive'           => 'excerpts', // select ( full, excerpts )
           'content_archive_limit'     => '0', // int only applicable when using excerpts on content_archive
           'content_archive_thumbnail' => 1,  // bool (use thumbnail for archive)
           'image_size'                => 'facebook_wide', // image size (theme image sizes)
           'image_alignment'           => '', // select ( alignleft, alignright )
           'posts_nav'                 => 'numeric', // select ( prev-next, numeric )
         );
	}

	public function set_default_options(){

         if(!function_exists('genesis_update_settings')){
            return;
         }
         $arr = $this->defaults;
         genesis_update_settings($arr);
    }

    public function initialize_genesis_theme_settings_defaults($args) {
    	
        $arr = $this->defaults;
        foreach($arr as $k => $v){
            $args[$k] = $v;
        }
        return $args;
    }
}
