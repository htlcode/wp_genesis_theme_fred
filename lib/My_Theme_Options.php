<?php
namespace WpGenesisFred;

class My_Theme_Options Extends My_Options {

    public static $checkbox_header_title = 'checkbox_header_title';
    public static $checkbox_header_description = 'checkbox_header_description';
    public static $checkbox_post_meta_date = 'checkbox_post_meta_date';
    public static $checkbox_post_meta_author = 'checkbox_post_meta_author';
    public static $checkbox_post_box_author = 'checkbox_post_box_author';
    public static $checkbox_post_meta_categories = 'checkbox_post_meta_categories';
    public static $checkbox_post_meta_tags = 'checkbox_post_meta_tags';
    public static $checkbox_post_related = 'checkbox_post_related';
    public static $text_site_title_color = 'text_site_title_color';
    public static $text_header_background_color = 'text_header_background_color';
    public static $text_link_color = 'text_link_color';
    public static $text_link_color_on_hover = 'text_link_color_on_hover';
    public static $text_nav_background_color = 'text_nav_background_color';
    public static $text_nav_link_color = 'text_nav_link_color';
    public static $text_nav_link_color_on_hover = 'text_nav_link_color_on_hover';
    public static $text_h1_color = 'text_h1_color';
    public static $text_h2_color = 'text_h2_color';
    public static $text_h3_color = 'text_h3_color';
    public static $text_h4_color = 'text_h4_color';
    public static $text_h5_color = 'text_h5_color';
    public static $text_h6_color = 'text_h6_color';
    public static $text_button_background_color = 'text_button_background_color';
    public static $text_button_font_color = 'text_button_font_color';
    public static $text_button_action_background_color = 'text_button_action_background_color';
    public static $text_button_action_font_color = 'text_button_action_font_color';

    public static $text_sidebar_widget_title_color = 'text_sidebar_widget_title_color';
    public static $text_sidebar_widget_link_color = 'text_sidebar_widget_link_color';

    public static $text_post_meta_title_color = 'text_post_meta_title_color';
    public static $text_post_meta_link_color = 'text_post_meta_link_color';

    public static $text_footer_background_color = 'text_footer_background_color';
    public static $text_footer_font_color = 'text_footer_font_color';
    public static $text_footer_link_color = 'text_footer_link_color';
    public static $text_footer_widget_title_color = 'text_footer_widget_title_color';

    public static $text_facebook_url = 'text_facebook_url';
    public static $text_twitter_url = 'text_twitter_url';
    public static $text_youtube_url = 'text_youtube_url';
    public static $text_instagram_url = 'text_instagram_url';
    public static $text_linkedin_url = 'text_linkedin_url';
    public static $text_snapchat_url = 'text_snapchat_url';
    public static $hidden_updated_at = 'hidden_updated_at';
    public static $text_footer_credits = 'text_footer_credits';

    private $now;
    private $css_files;

    const CSS_FILE_DEFAULT = 'default.css'; 
    const CSS_FILE_CUSTOM = 'custom.css'; 

    public function __construct(){
        $this->now = date('Y-m-d H:i:s');

        $this->css_files = array('fontawsome.min.css','knacssfork.min.css','animate.min.css','template.min.css');
        $this->page_parent = 'genesis';
        $this->option_group_name = THEME_SETTINGS_FIELD;
        $this->page_title = __('Genesis Fred Theme Settings',THEME_NAME);
        $this->page_code = THEME_NAME;
        $this->page_icon = 'dashicons-art';

        $this->options = array(
            array(
            'type'=>'section',
            'name'=>'section1',
            'title'=>__('General Settings',THEME_NAME),
            ),
                
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_header_title,
                'title'=>__('Hide title', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_header_description,
                'title'=>__('Hide description', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_post_meta_date,
                'title'=>__('Hide post meta date', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_post_meta_author,
                'title'=>__('Hide post meta author', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_post_box_author,
                'title'=>__('Hide post author box', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_post_meta_categories,
                'title'=>__('Hide post meta categories', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_post_meta_tags,
                'title'=>__('Hide post meta tags', THEME_NAME),
                'default'=> 0,
                ),
                array(
                'type'=>'checkbox',
                'name'=> self::$checkbox_post_related,
                'title'=>__('Hide related posts', THEME_NAME),
                'default'=> 0,
                ),
            array(
            'type'=>'section',
            'name'=>'section2',
            'title'=>__('Color Settings',THEME_NAME),
            ),
                array(
                'type'=>'color',
                'name'=>self::$text_site_title_color,
                'title'=>__('Site title color',THEME_NAME),
                'default' => '#CC0000'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_header_background_color,
                'title'=>__('Header background color',THEME_NAME),
                'default' => '#000000'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_link_color,
                'title'=>__('Link color',THEME_NAME),
                'default' => '#CC0000'
                ), 
                array(
                'type'=>'color',
                'name'=>self::$text_link_color_on_hover,
                'title'=>__('Link color on hover',THEME_NAME),
                'default' => '#FF0000'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_nav_background_color,
                'title'=>__('Navigation background color',THEME_NAME),
                'default' => '#FFFFFF'
                ), 
                array(
                'type'=>'color',
                'name'=>self::$text_nav_link_color,
                'title'=>__('Navigation link color',THEME_NAME),
                'default' => '#000000'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_nav_link_color_on_hover,
                'title'=>__('Navigation link color on hover',THEME_NAME),
                'default' => '#CC0000'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_h1_color,
                'title'=>__('H1 color',THEME_NAME),
                'default' => '#373D3F'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_h2_color,
                'title'=>__('H2 color',THEME_NAME),
                'default' => '#EF9A9A'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_h3_color,
                'title'=>__('H3 color',THEME_NAME),
                'default' => '#E53935'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_h4_color,
                'title'=>__('H4 color',THEME_NAME),
                'default' => '#455A64'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_h5_color,
                'title'=>__('H5 color',THEME_NAME),
                'default' => '#373D3F'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_h6_color,
                'title'=>__('H6 color',THEME_NAME),
                'default' => '#9E9E9E'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_button_background_color,
                'title'=>__('Primary button background color',THEME_NAME),
                'default' => '#FAFAFA'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_button_font_color,
                'title'=>__('Primary button font color',THEME_NAME),
                'default' => '#373D3F'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_button_action_background_color,
                'title'=>__('CTA button background color',THEME_NAME),
                'default' => '#FFC107'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_button_action_font_color,
                'title'=>__('CTA button font color',THEME_NAME),
                'default' => '#201C5E'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_sidebar_widget_title_color,
                'title'=>__('Sidebar widget title color',THEME_NAME),
                'default' => '#373D3F'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_sidebar_widget_link_color,
                'title'=>__('Sidebar widget link color',THEME_NAME),
                'default' => '#B0BEC5'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_post_meta_title_color,
                'title'=>__('Post meta titles color',THEME_NAME),
                'default' => '#373D3F'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_post_meta_link_color,
                'title'=>__('Post meta informations color',THEME_NAME),
                'default' => '#B0BEC5'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_footer_background_color,
                'title'=>__('Footer background color',THEME_NAME),
                'default' => '#333333'
                ),
                 array(
                'type'=>'color',
                'name'=>self::$text_footer_font_color,
                'title'=>__('Footer font color',THEME_NAME),
                'default' => '#FFFFFF'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_footer_link_color,
                'title'=>__('Footer link color',THEME_NAME),
                'default' => '#FFFFFF'
                ),
                array(
                'type'=>'color',
                'name'=>self::$text_footer_widget_title_color,
                'title'=>__('Footer widget title color',THEME_NAME),
                'default' => '#FFFFFF'
                ),
            array(
            'type'=>'section',
            'name'=>'section3',
            'title'=>__('Social networks',THEME_NAME),
            ),
                array(
                'type'=>'url',
                'name'=>self::$text_facebook_url,
                'title'=>__('Facebook url',THEME_NAME),
                ),
                array(
                'type'=>'url',
                'name'=>self::$text_twitter_url,
                'title'=>__('Twitter url',THEME_NAME),
                ),
                array(
                'type'=>'url',
                'name'=>self::$text_youtube_url,
                'title'=>__('Youtube url',THEME_NAME),
                ),
                array(
                'type'=>'url',
                'name'=>self::$text_instagram_url,
                'title'=>__('Instagram url',THEME_NAME),
                ),
                array(
                'type'=>'url',
                'name'=>self::$text_linkedin_url,
                'title'=>__('Linkedin url',THEME_NAME),
                ),
                array(
                'type'=>'url',
                'name'=>self::$text_snapchat_url,
                'title'=>__('Snapchat url',THEME_NAME),
                ),
            array(
            'type'=>'section',
            'name'=>'section4',
            'title'=>__('Footer Settings',THEME_NAME),
            ),
                array(
                'type'=>'textarea',
                'name'=>self::$text_footer_credits,
                'title'=>__('Footer credits',THEME_NAME),
                ),
                array(
                'type'=>'hidden',
                'name'=>self::$hidden_updated_at,
                'title'=>'',
                'const'=> $this->now,
                ),
        );
        
        parent::__construct();

        // After save hook
        add_action('update_option_'.THEME_SETTINGS_FIELD, array($this,'do_after_save'), 99, 2 );
    }

    public function do_after_save( $old_value, $new_value ) {
        if ( $old_value[self::$hidden_updated_at] != $new_value[self::$hidden_updated_at] ) {

            $path = get_stylesheet_directory().'/css/';
            $path_custom_css_file = $path.self::CSS_FILE_CUSTOM;
            $path_default_css_file = $path.self::CSS_FILE_DEFAULT;

            $replaces = array();
            foreach($this->fields as $k => $field){
                $pos = strpos($k,'color');
                if($pos !== false){
                    
                    $value = esc_attr(strtoupper(genesis_get_option($k,THEME_SETTINGS_FIELD)));
                    if(empty($value)){
                        $value = $field['default'];
                    }
                    $replaces['#'.$k.';'] = array('value' => $value, 'default' => $field['default']);
                }
            }

            $default_css_file = fopen($path_default_css_file,"w") or die("Unable to open file!");
            if ($default_css_file !== false){
                foreach($this->css_files as $css_file){
                    $content = file_get_contents($path.$css_file);
                    $content = rtrim($content, "\n");
                    foreach($replaces as $var_to_replace => $replacement){
                        $str_replacement = $replacement['default'];
                        if(!empty($str_replacement)){
                            $str_replacement = $str_replacement.';';
                        } else {
                            $var_to_replace = 'color:'.$var_to_replace;
                            $str_replacement = '';
                        }
                        $content = str_replace($var_to_replace, $str_replacement, $content);
                    }
                    fwrite($default_css_file, $content);
                }
                fclose($default_css_file);
            }

            $custom_css_file = fopen($path_custom_css_file,"w") or die("Unable to open file!");
            if ($custom_css_file !== false){
                foreach($this->css_files as $css_file){
                    $content = file_get_contents($path.$css_file);
                    $content = rtrim($content, "\n");
                    foreach($replaces as $var_to_replace => $replacement){
                        $str_replacement = $replacement['value'];
                        if(!empty($str_replacement)){
                            $str_replacement = $str_replacement.';';
                        } else {
                            $var_to_replace = 'color:'.$var_to_replace;
                            $str_replacement = '';
                        }
                        $content = str_replace($var_to_replace, $str_replacement, $content);
                    }
                    fwrite($custom_css_file, $content);
                }
                fclose($custom_css_file);
            }
        }
    }
}