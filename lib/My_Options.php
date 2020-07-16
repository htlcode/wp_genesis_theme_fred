<?php
namespace WpGenesisFred;

class My_Options {

    protected $values;
    protected $options;
    protected $fields;
    protected $page_parent;
    protected $page_title;
    protected $page_icon;
    protected $menu_index = 59;
    protected $create_menu = true;

    public function __construct(){

        $this->values = get_option($this->option_group_name);
        if(!$this->values){
            $this->values = array();
        }
        
        $this->create_map_options();
        if(is_admin() && $this->create_menu){

            if(empty($this->page_parent)){
                add_action( 'admin_menu', array( $this, 'add_page' ) );
            } else {
                add_action( 'admin_menu', array( $this, 'add_sub_page' ) );
            }

            add_action( 'admin_init', array( $this, 'page_init' ) );
        }
    }

    public function has_options(){
        if(empty($this->values)){
            return false;
        } else {
            return true;
        }
    }

    public function create_map_options(){

        $tmp_options = $this->options;

        $this->options = array();
        $this->fields = array();

        foreach($tmp_options as $option){
            if($option['type'] != 'section'){
                $this->fields[$option['name']] = $option;
            }
            $this->options[$option['name']] = $option;
        }
    }

    public function add_page(){

        add_menu_page(
            __($this->page_title,THEME_NAME), 
            __($this->page_title,THEME_NAME), 
            'manage_options', 
            $this->page_code, 
            array($this,'create_settings_page'),
            $this->page_icon,
            $this->menu_index
        );
    }

    public function add_sub_page(){
        add_submenu_page(
            $this->page_parent, 
            __($this->page_title,THEME_NAME),
            __($this->page_title,THEME_NAME), 
            'manage_options', 
            $this->page_code, 
            array($this,'create_settings_page')
        );
    }

    public function create_settings_page(){
        ?>
        <div class="wrap">

            <?php settings_errors(); ?>
            <h2><?php echo __($this->page_title,THEME_NAME); ?></h2>           
            <form id="form_settings" method="post" action="options.php" enctype="multipart/form-data">
            <?php
                // This prints out all hidden setting fields
                settings_fields( $this->option_group_name );   
                do_settings_sections( $this->page_code );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }
    
    public function page_init(){

        register_setting(
            $this->option_group_name, // Option group
            $this->option_group_name, // Option name
            array($this, 'validate')
        );

        foreach($this->options AS $option){
            if($option['type']=='section'){
                add_settings_section(
                $option['name'], // ID
                $option['title'], // Title
                array($this,'create_'.$option['type'].'_callback'), // Callback
                $this->page_code // Page
                );
                $section_id=$option['name'];
            } else if($option['type']=='callback'){
                add_settings_section(
                $option['name'], // ID
                $option['title'], // Title
                array($this,$option['callback']), // Callback
                $this->page_code, // Page
                $section_id
                );
            } else {
                add_settings_section(
                $option['name'], // ID
                $option['title'], // Title
                array($this,'create_'.$option['type'].'_callback'), // Callback
                $this->page_code, // Page
                $section_id
                );
            }
        }
    }

    public function validate($input){

        $new_input = array();

        $allowed_tags = wp_kses_allowed_html( 'post' );

        foreach($this->fields AS $field){
            $name = $field['name'];
            if($field['type'] == 'editor'){
                $value = $_POST[$name];
                $new_input[$name] = wp_kses_post($value);
            } else if($field['type'] == 'color') {
                $color = '';
                if(preg_match('/^#[a-f0-9]{6}$/i', $input[$name])){
                    $new_input[$name] = wp_kses( $input[$name], $allowed_tags );
                }
            } else if($field['type'] == 'url') {
                if(filter_var($input[$name], FILTER_VALIDATE_URL) !== FALSE){
                    $new_input[$name] = wp_kses( $input[$name], $allowed_tags );
                }
            } else if($field['type'] == 'javascript') {
                $new_input[$name] = $input[$name];
            } else {
                $new_input[$name] = wp_kses( $input[$name], $allowed_tags );
            
            }
            
        }
        return $new_input;
    }

    public function add_description($args){
        $name = $args['id'];
        $properties = $this->options[$name];
        if(isset($properties['description'])){
            echo '<p class="description">'.$properties['description'].'</p>';
        }
    }

    public function create_section_callback($args){
        $this->add_description($args);
        echo '<hr>';
    }

    public function create_text_callback($args,$type = "text"){
        $name = $args['id'];
        $default = '';
        $properties = $this->options[$name];
        if(isset($properties['const'])){
            $value = $properties['const'];
        } else {
            if(isset($properties['default'])){
                $default = $properties['default'];
            }
            $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;
        }
        
        $str = '<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s"/>';
        printf($str, $type, $name, $this->option_group_name.'['.$name.']', $value);
        $this->add_description($args);
    }

    public function create_email_callback($args){
        $this->create_text_callback($args,'email');
    }

    public function create_url_callback($args){
        $this->create_text_callback($args,'url');
    }

    public function create_password_callback($args){
        $this->create_text_callback($args,'password');
    }

    public function create_number_callback($args){
        $this->create_text_callback($args,'number');
    }

    public function create_hidden_callback($args){
        $this->create_text_callback($args,'hidden');
    }

    public function create_javascript_callback($args){
        $name = $args['id'];
        $default = '';
        $properties = $this->options[$name];
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        $value = isset( $this->values[$name] ) ? $this->values[$name] : $default;
        $str = '<textarea rows="4" cols="50" id="%1$s" name="%2$s">%3$s</textarea>';
        printf($str, $name, $this->option_group_name.'['.$name.']', $value);
        $this->add_description($args);
    }



    public function create_textarea_callback($args){
        $name = $args['id'];
        $default = '';
        $properties = $this->options[$name];
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;
        $str = '<textarea rows="4" cols="50" id="%1$s" name="%2$s">%3$s</textarea>';
        printf($str, $name, $this->option_group_name.'['.$name.']', $value);
        $this->add_description($args);
    }

    public function create_color_callback($args){
        $name = $args['id'];
        $properties = $this->options[$name];
        $default = '';
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;
        $str = '<input type="text" class="input-color" id="%1$s" name="%2$s" value="%3$s" />';
        printf($str, $name, $this->option_group_name.'['.$name.']', $value);
        $this->add_description($args);
    }

    public function create_upload_callback($args){
        $name = $args['id'];
        $properties = $this->options[$name];
        $default = '';
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;

        $str = '<input type="text" class="regular-text input-upload" id="%1$s" name="%2$s" value="%3$s" />';
        $str = $str.'<input type="button" class="button btn-upload" id="btn-%1$s" value="%4$s" />';

        printf($str, $name, $this->option_group_name.'['.$name.']', $value, __('Upload'));
       
        $this->add_description($args);
    }

    public function create_editor_callback($args){
        $name = $args['id'];
        $properties = $this->options[$name];
        $default = '';
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        

        $content = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;
        
        wp_editor( html_entity_decode($content) , $name );
    }

    public function create_radio_callback($args){
        $name = $args['id'];
        $properties = $this->options[$name];
        $default = null;
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;

        if(isset($properties['list'])){
            foreach($properties['list'] as $list_key => $list_option){
                $str = '<input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />';
                $str = $str.'<label>%5$s</label>&nbsp;';
                printf($str, $name, $this->option_group_name.'['.$name.']', $list_key, checked( $list_key, $value, false ), $list_option);
            }
        }
    }

    public function create_checkbox_callback($args){
        $name = $args['id'];
        $default = '';
        $properties = $this->options[$name];
        if(isset($properties['default'])){
            $default = $properties['default'];
        }

        $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;
        $str = '<input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s />';
        printf($str, $name, $this->option_group_name.'['.$name.']', checked(1,$value,false));
        $this->add_description($args);
    }

    public function create_dropdown_callback($args){
        $name = $args['id'];
        $properties = $this->options[$name];
        $default = null;
        if(isset($properties['default'])){
            $default = $properties['default'];
        }
        $value = isset( $this->values[$name] ) ? esc_attr( $this->values[$name]) : $default;

        $controller = $this->option_group_name.'['.$name.']';
        if(isset($properties['list'])){
            $str = '<select id="%1$s" name="%2$s">';
            printf($str, $name, $controller);
            foreach($properties['list'] as $list_key => $list_option){
                $str = '<option value="%1$s" %3$s>%2$s</option>';
                printf($str, $list_key, $list_option, selected( $list_key, $value, false ));
            }
            echo '</select>';
        }
    }
}