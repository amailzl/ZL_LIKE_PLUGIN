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
            add_filter( 'the_content', array( $this, 'insert_after_content'), $this->get_content_filter_priority());
            add_action( 'wp_enqueue_scripts', array( $this, 'zelon_add_style'));
            add_action( 'wp_ajax_zl_like_press', array( $this, 'like_press_handler'));
            add_action( 'wp_ajax_zl_dislike_press', array( $this, 'dislike_press_handler'));
            add_action( 'wp_ajax_zl_donate_press', array( $this, 'donate_press_handler'));
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
                wp_enqueue_script( 'zelon-like-script', plugins_url('./button-response.js', __FILE__), array('jquery'));
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
        function like_press_handler() {
            $id = $_POST["post_id"];
            $like = 'zl_likes';
            $dislike = 'zl_dislikes';
            $expire = time() + 60*60*24*365*10;
            $domain = ($_SERVER['HTTP_HOST'] != 'localhost')?$_SERVER['HTTP_HOST']:false;
            if(isset($_COOKIE[$like.$id])) {
                if(isset($_COOKIE[$dislike.$id])) {
                    $this->update_post_meta_minus_1($id, $dislike);
                    setcookie($dislike.$id, '', time()-3600);
                }
                echo 'done';
                wp_die();
            }
            setcookie($like.$id, $id, $expire. '/', $domain, false);
            if(isset($_COOKIE[$dislike.$id])) {
                $this->update_post_meta_minus_1($id, $dislike);
                setcookie($dislike.$id, '', time()-3600);
            }
            $this->update_post_meta_plus_1($id, $like);
            echo get_post_meta($id, "$like", true);
            wp_die();
        }

        //JSON
        function dislike_press_handler() {
            $id = $_POST["post_id"];
            $like = 'zl_likes';
            $dislike = 'zl_dislikes';
            $expire = time() + 60*60*24*365*10;
            $domain = ($_SERVER['HTTP_HOST'] != 'localhost')?$_SERVER['HTTP_HOST']:false;
            if(isset($_COOKIE[$dislike.$id])) {
                if(isset($_COOKIE[$like.$id])) {
                    $this->update_post_meta_minus_1($id, $like);
                    setcookie($like.$id, '', time()-3600);
                }
                echo 'done';
                wp_die();
            }
            setcookie($dislike.$id, $id, $expire. '/', $domain, false);
            if(isset($_COOKIE[$like.$id])) {
                $this->update_post_meta_minus_1($id, $like);
                setcookie($like.$id, '', time()-3600);
            }
            $this->update_post_meta_plus_1($id, $dislike);
            echo get_post_meta($id, "$dislike", true);
            wp_die();
        }

        function donate_press_handler() {
            $id = $_POST["post_id"];
            $expire = time() + 60*60*24*365*10;
            $domain = ($_SERVER['HTTP_HOST'] != 'localhost')?$_SERVER['HTTP_HOST']:false;
            $donate = 'donate'.$id;
            if(isset($_COOKIE[$donate])) {
                setcookie($donate, '', time()-3600);
                echo 'hide';
                wp_die();
            }
            setcookie($donate, $id, $expire. '/', $domain, false);
            echo 'show';
            wp_die();
        }

        public function update_post_meta_plus_1($id, $field){
            $count = get_post_meta($id, "$field", true);
            if(!$count || !is_numeric($count)){
                update_post_meta($id, $field, 1);
            } else {
                update_post_meta($id, $field, $count+1);
            }
        }

        public function update_post_meta_minus_1($id, $field){
            $count = get_post_meta($id, $field, true);
            if(!$count || !is_numeric($count)){
                update_post_meta($id, $field, 0);
            } else {
                update_post_meta($id, $field, ($count-1));
            }
        }

        public function insert_after_content( $postcontent ) {
            if($this->zl_like_show(get_the_ID())){
                $zl_3btn = $this->zl_likes_get3bn();
                $postcontent.= apply_filters('zl_3btn', $zl_3btn );
            }
            return $postcontent;
        }


        /**
        * Get 3_button form for html
        */
        public function zl_likes_get3bn() {
			$QRurl = plugins_url('./user/1556508976.png', __FILE__);
			$ToSponsor = 'THANKS';
            $likes = get_post_meta(get_the_ID(), "zl_likes", true);
            $dislikes = get_post_meta(get_the_ID(), "zl_dislikes", true);
            if(!$likes || !is_numeric($likes)){
                $likes = 0;
                update_post_meta(get_the_ID(), 'zl_likes', 0);
            }
            if(!$dislikes || !is_numeric($dislikes)){
                $dislikes = 0;
                update_post_meta(get_the_ID(), '$dislikes', 0);
            }
            $fmt_3bt='<div align="center"><button id="zl-like" data-id="'.get_the_ID().'" class="lzl_like_func_style_1">喜欢<span class="like_counts">('.$likes.')</span></button>';
            //'.php.' use ('.) (.')to wrap the php content you want to use
            $fmt_3bt.=' | <button id="zl-dislike" data-id="'.get_the_ID().'" class="lzl_like_func_style_1">不喜欢<span class="dislike_counts">('.$dislikes.')</span></button>';
            $fmt_3bt.=' | <button id="zl-donate" data-id="'.get_the_ID().'"  class="lzl_like_func_style_1">打赏</button></div>';
            $fmt_3bt.='<div id="theQR" align="center" ><br/><br/><img src="'.$QRurl.'"  alt="oops..." width="100" height="100"/></div>';
            $fmt_3bt.='<div id="comment" align="center" ><br/>'.$ToSponsor.'</div>';
            return $fmt_3bt;
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

