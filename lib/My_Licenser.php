<?php
namespace WpGenesisFred;

class My_Licenser Extends My_Options {

	private $product_code = 'YphlY';
	private $server = 'https://api.gumroad.com/v2/licenses/verify';

	public static $text_license = 'text_license';

	public $license = '';

	public function __construct(){
		add_action('after_switch_theme', array($this,'set_theme_date'));

		$this->option_group_name = THEME_NAME.'-license';
		$this->page_parent = 'genesis';
		$this->page_title = CHILD_THEME_NAME.' '.__('License',THEME_NAME);
        $this->page_code = THEME_NAME.'-license';
        $this->page_icon = 'dashicons-admin-network';

		$this->options = array(
			array(
			'type'=>'section',
			'name'=>'section1',
			'title'=>__('License Settings',THEME_NAME),
			),
			    array(
			    'type'=>'text',
			    'name'=> self::$text_license,
			    'title'=>__('Your license key', THEME_NAME),
			    'default'=> '',
			    )
		);

		$this->license = $this->get_licence();

        parent::__construct();
        add_filter('pre_update_option_'.$this->option_group_name, array($this,'do_before_save'), 10, 2 );
	}

	public function get_licence(){
		$data = get_option($this->option_group_name, '');

		if(!empty($data) && isset($data[self::$text_license])){
			return $data[self::$text_license];
		}
		return '';
	}

	public function do_before_save($new_value, $old_value){

		$license_key = '';
		if(isset($new_value[self::$text_license])){
			$license_key = $new_value[self::$text_license];
		}

		$post = array(
		    'product_permalink' => $this->product_code,
		    'license_key' => $license_key,
		);
		$q = http_build_query($post);
		$ch = curl_init($this->server);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $q);

		$response = curl_exec($ch);
		curl_close($ch);

		$res = json_decode($response,true);
		if(json_last_error() == JSON_ERROR_NONE){

			if(isset($res['success']) && ($res['success'] == true)){
				if(isset($res['purchase']) && ($res['purchase']['refunded'] == false)){
					
					return $new_value; 
				}
			}
		} 
		add_settings_error(
            self::$text_license,
            esc_attr( 'settings_updated' ),
            __('This license key is not valid',THEME_NAME),
            'error'
        );
		return array(self::$text_license => ''); 
	}

	public function alert(){
		?>
		<div class="error notice">
        <p><?php echo __( 'There has been an error. Bummer!'); ?></p>
    	</div>
		<?php
	}

	public function set_theme_date(){
		$now = date('Y-m-d H:i:s');
		delete_option($this->option_group_name);
		update_option(THEME_NAME.'-date', $now);
	}
}