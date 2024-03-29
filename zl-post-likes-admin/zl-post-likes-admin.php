<?php
/**
 * Settings page located under the Settings Menu.
 * Settings page gives you even more control.
 * Use the options  to prevent the widget from showing on a specific post type or post format.
 *
 * @package     ZL_POST_LIKES
 * @subpackage  zl_post_likes_admin
 * @copyright   Copyright (c) 2015-2019, zelonli
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}


if ( !class_exists( 'zl_post_likes_admin' ) ) {
    class zl_post_likes_admin {
        /**
         * The ID of this plugin.
         * @access   private
         * @var      string
         */
        private $plugin_name;
        /**
         * The version of this plugin.
         * @access   private
         * @var      string
         */
        private $version;



        /**
         * Initialize the settings page
         * @access public
         */
        public function __construct( $plugin_name, $version ) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            add_action('admin_menu', array( $this,'zl_add_options_page'));
            add_action('admin_init', array( $this,'zl_initialize_options'));
            add_action( 'admin_enqueue_scripts', array( $this, 'zl_add_style'));
            add_filter('admin_footer_text', array( $this,'zl_display_admin_footer'));

        }

        public function zl_add_style() {
            if(is_admin()){
                //https://developer.wordpress.org/reference/functions/is_single/
                wp_enqueue_script( 'zl-post-likes-script', plugins_url('../res/js/admin-response.js', __FILE__), array('jquery'));
                $zl_nonce = wp_create_nonce( 'zl_like_nonce' );
                //https://codex.wordpress.org/Function_Reference/wp_localize_script
                wp_localize_script( 'zl-post-likes-script', 'zl_press_action', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => $zl_nonce,
                ) );
            }
            return;
        }

        /**
         * Adds the 'ZL Post Likes Options' to the Appearance menu in the Dashboard
         * https://codex.wordpress.org/Roles_and_Capabilities#manage_options
         */
        public function zl_add_options_page(){
            add_menu_page(
                'ZL Post Likes Options',
                'ZL Post Likes',
                'manage_options',
                'ZLPL-options',
                array($this, 'zlpl_options_display')
            );
        }

        public function upload_handler(){
            // First check if the file appears on the _FILES array
            if(isset($_FILES['upload_img'])){

                $upload_img = $_FILES['upload_img'];
                $upload_overrides = array( "test_form" => false );
                $uploaded_file = wp_handle_upload ($upload_img, $upload_overrides);
                $uploaded=media_handle_upload('upload_img', 0);

                if (!empty( $uploaded_file ["file"])){
                    $upload_dir = wp_upload_dir();
                    $path = $upload_dir['path'];
                    $path .= '/*';
                    $files =  glob($path); // get all file names
                    foreach($files as $file){ // iterate files
                        if($file != $uploaded_file ["file"]){
                            unlink($file); // delete file
                        }
                    }
                }

                $path = 'QR_img.txt';
                if(isset($uploaded_file ["url"]) && isset($path)){
                    file_put_contents($path, $uploaded_file ["url"]);
                }
                if(is_wp_error($uploaded)){
                    echo "Error uploading file: " . $uploaded->get_error_message();
                }else{
                    echo "upload successful!";
                }
            }

            if(isset($_REQUEST['upload_txt'])){
                $upload_txt = $_REQUEST['upload_txt'];
                $path = 'a_word.txt';
                if (!empty($upload_txt)){
                    file_put_contents($path, $upload_txt);
                }
            }

        }
        /**
         * Renders the content of the zl options page
         */
        public function zlpl_options_display(){
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            $this->upload_handler();
            $tabs = $this->zl_get_tabs();
            require_once(ZL_POST_LIKES_DIR . 'zl-post-likes-admin/zlpl_options_display.php');
        }

        public function zl_get_tabs(){

            $tabs['zl_basic']  = __( 'General', $this->plugin_name );
            return $tabs;
        }


        /**
         * Registers settings fields
         */
        public function zl_initialize_options(){
            add_settings_section(
                'zl_basic',
                __('Customization', $this->plugin_name),
                array($this, 'zl_basic_section_display'),
                'ZLPL-options'
            );
            /**
             * all_buttons written by @doncullen
             */
            add_settings_field(
                'all_buttons',
                __('BUTTON SELECTION<p class="description">you should choose the button you need.like button is displayed by default</p>', $this->plugin_name ),
                array($this, 'zl_button_selection'),
                'ZLPL-options',
                'zl_basic'
            );
            register_setting(
                'ZLPL-options',
                'all_buttons'
            );

            add_settings_field(
                'button_style',
                __('BUTTON STYLE<p class="description">customize the button style(the style start with * do not display dislike button even if you selected it in button selection)</p>', $this->plugin_name ),
                array($this, 'zl_button_style'),
                'ZLPL-options',
                'zl_basic',
                [
                    'label_for' => 'zl_button_style',
                    'class' => 'zl_opt_list',
                    'zl_custom_data' => 'custom',
                ]
            );
            register_setting(
                'ZLPL-options',
                'button_style'
            );

            add_settings_field(
                'upload_donate_QR_image'.
                __('QR image<p class="description">select the QR image for receiving donations </p>', $this->plugin_name ),
                array($this, 'QR_image'),
                'ZLPL-options',
                'zl_basic',
                array('type'=>'radio')

            );
            register_setting(
                'ZLPL-options',
                'upload_donate_QR_image'
            );
        }

        /**
         * Display the categories for posts
         * @doncullen
         */
        public function zl_button_selection(){
            $button_selection = array('dislike_button', 'donate_button');
            $options = (array)get_option('all_buttons');
            echo '<label><input name="default_like" id="like_button" type="checkbox" value="1" class="code" ' . checked( 1, 1, false ) . '/>like_button</label><br />' ;

            foreach ( $button_selection as $selected ) {
                if( !isset($options[$selected]) ){
                    $options[$selected] = 0;
                }
                echo '<label><input name="all_buttons['. $selected .']" id="all_buttons['. $selected .']" type="checkbox" value="1" class="code" ' . checked( 1, $options[$selected], false ) . ' />'. $selected.'</label><br />' ;

            }

        }

        public function zl_button_style($args){
            $options = get_option( 'button_style' );
            // output the field
?>
<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
        data-custom="<?php echo esc_attr( $args['zl_custom_data'] ); ?>"
        name="button_style[<?php echo esc_attr( $args['label_for'] ); ?>]"
        >
    <option value="dark" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'dark', false ) ) : ( '' ); ?>>
        <?php esc_html_e( 'dark style', 'ZLPL-options' ); ?>
    </option>
    <option value="bright" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'bright', false ) ) : ( '' ); ?>>
        <?php esc_html_e( 'bright style', 'ZLPL-options' ); ?>
    </option>
    <option value="heart" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'bright', false ) ) : ( '' ); ?>>
        <?php esc_html_e( '*heart style', 'ZLPL-options' ); ?>
    </option>
</select>
<?php
        }

        public function QR_image(){

        }

        /**
         * Display the number field for setting the priority of the_content filter insert_after_content
         */

        /**
         * Display rate us message in footer only on settings page.
         * @param  string $text wordpress admin footer text
         * @return string       updated footer text
         */
        public function zl_display_admin_footer($text) {

            $currentScreen = get_current_screen();
            if ( $currentScreen->id == 'toplevel_page_ZLPL-options' ) {
                $thk_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">ZL Post Likes</a>! Feel free to <a href="%2$s" target="_blank">get source code</a> and do whatever you like with it',  $this->plugin_name ),
                                     'https://zelonLi.com',
                                     'https://github.com/amailzl/ZL_LIKE_PLUGIN'
                                    );

                return str_replace( '</span>', '', $text ) . ' | ' . $thk_text . '</span>';
            } else {
                return $text;
            }
        }

    } /*End class AddWidgetAfterContentAdmin*/


}
?>
