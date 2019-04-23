<?php
/*
Plugin Name: zelon-like-plugin
Plugin URI:
Description: add like/dislike func of articles
Version:     0.0.2
Author:      zelonli
Author URI: http://47.101.182.245
Text Domain:
Domain Path: /languages
License:     GPL2

zelon-likes-func is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

zelon-likes-func is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with zelon-likes-func. If not, see {License URI}.
*/
define( 'ZL_LIKE_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'ZL_LIKE_PLUGIN_FILE', __FILE__ );
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( !class_exists( 'zl_like_plugin' ) ) {
    class zl_like_plugin{
        // add css style file
        // https://developer.wordpress.org/reference/functions/plugins_url/
        // https://developer.wordpress.org/reference/functions/wp_enqueue_style/
        function __construct() {
            //[lzl][init]https://codex.wordpress.org/Plugin_API/Action_Reference/init
            //[widgets_init]https://developer.wordpress.org/reference/hooks/widgets_init/
//          add_action( 'widgets_init', array( $this,'register_sidebar'));
//          //[add_meta_boxes]https://developer.wordpress.org/reference/hooks/add_meta_boxes/
//          add_action( 'add_meta_boxes', array( $this,'after_content_create_metabox') );
//          //[save_post]https://developer.wordpress.org/reference/hooks/save_post/
//          add_action( 'save_post', array( $this,'after_content_save_meta') );
//          //[the_content]https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content
            add_filter( 'the_content', array( $this, 'insert_after_content'), $this->get_content_filter_priority());
            add_action( 'wp_enqueue_scripts', array( $this, 'zelon_add_style'));
            add_action( 'wp_ajax_zl_like_press', array( $this, 'my_ajax_handler') );
//          $this->settings = new AddWidgetAfterContentAdmin($this->plugin_slug, $this->plugin_version );
        }

        public static function activate() {
            if (get_option('zllp_priority') === false){
                update_option('zllp_priority', '10');
            }
            update_option('zllp_extensions', array());
        }

        public static function get_content_filter_priority(){
            if (get_option('zllp_priority') === false){
                update_option('zllp_priority', '10');
            }

            return get_option('zllp_priority');

        }

        public function zelon_add_style() {
            if(is_single()){
                //only enqueue your script when you need it
                //https://developer.wordpress.org/reference/functions/is_single/
                wp_enqueue_style( 'zelon-like-style', plugins_url('./zelon-like-style.css', __FILE__) );
                wp_enqueue_script( 'zelon-like-script', plugins_url('/button-response.js', __FILE__), array('jquery'));
                $zl_nonce = wp_create_nonce( 'zl_like_nonce' );
                //https://codex.wordpress.org/Function_Reference/wp_localize_script
                wp_localize_script( 'zelon-like-script', 'zl_press_action', array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => $zl_nonce,
                ) );
            }
            return;
        }
        //JSON
        function my_ajax_handler() {
//             check_ajax_referer('zl_like_nonce');
            echo 'testtttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt';
            // update_user_meta( get_current_user_id(), 'title_preference', $_POST['title'] );
            // $args = array(
            //     'tag' => $_POST['title'],
            // );
            // $the_query = new WP_Query( $args );
            // wp_send_json( $_POST['title'] . ' (' . $the_query->post_count . ') ' );
        }

        public function insert_after_content( $postcontent ) {
            if($this->zl_like_show(get_the_ID())){
                $zl_3btn = $this->zl_likes_get3bn();
                $postcontent.= apply_filters('zl_3btn', $zl_3btn );
            }
            return $postcontent;
        }

        /**
                 * Get what ever is to be in the widget area, but don't display it yet
                 * @return string the content of the add-widget-after-content sidebar/widget
                 */
        public function zl_likes_get3bn() {
            //  ob_start();
            //  dynamic_sidebar( 'add-widget-after-content' );
            //  $sidebar = ob_get_contents();
            //  ob_end_clean();
            global $likes;
            if(get_post_meta(get_the_ID(), 'likes',true)){
                            $likes=get_post_meta(get_the_ID(), 'likes',true);
                        }else{
                            $likes=0;
                        }
            echo $likes;
            $test='<div align="center"><button id="zl-like" class="lzl_like_func_style_1">喜欢<span><?php echo 0></span></button>';
            $test.=' | <button id="zl-dislike" class="lzl_like_func_style_1">不喜欢</button>';
            $test.=' | <button id="zl-donate" class="lzl_like_func_style_1">打赏</button></div>';
            return $test;
        }

        //https://codex.wordpress.org/Conditional_Tags
        public static function zl_like_show($post_id){
            if(!is_singular()){
                return false;
            }
            return(true);
        }
    }
}

if (class_exists( 'zl_like_plugin' ) ) {
    register_activation_hook( __FILE__, array( 'zl_like_plugin', 'activate' ) );
    $zl_LP = new zl_like_plugin();
}
