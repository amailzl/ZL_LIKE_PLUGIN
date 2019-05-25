<?php

/**
 * Admin settings page
 *
 *
 * @link       https://pintopsolutions.com
 * @since      2.2
 *
 * @package    ZL Post Likes
 * @subpackage ZL Post Likes /partials
 */
?>
<div class= "wrap">
    <h2><span class="dashicons dashicons-admin-generic"></span>  ZL Post Likes Options</h2>
    <hr/>
    <?php foreach ($tabs as $tab => $value) { ?>
    <a href="?page=ZLPL-options&tab=<?php echo $tab?>" class="nav-tab <?php echo $tab == $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo $value ?></a>
    <?php   }  ?>

    <div id="ps_admin" class="metabox-holder has-right-sidebar">
        <div class="inner-sidebar">

            <div class="meta-box-sortables">
                <div class="postbox">
                    <div class="inside">
                        <p><?php $url1 = 'https://pintopsolutions.com/contact/?utm_source=awacadmin&utm_medium=link&utm_content=contact&utm_campaign=plugin';
                            $link1     = sprintf( 'Need help? Or have an idea how this plugin can be made better. Reach out <a href=%s>on our website?</a>', esc_url( $url1 ) );
                            echo $link1; ?></p>

                        <p><?php $url2 = 'https://wordpress.org/support/view/plugin-reviews/add-widget-after-content?filter=5#postform';
                            $link2        = sprintf( __( 'We invite you to <a href=%s>leave an honest review.</a>', $this->plugin_name ), esc_url( $url2 ) );
                            echo $link2; ?></p>



                    </div>

                </div>
            </div>
        </div>

        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
                <div id="normal-sortables" class="meta-box-sortables">

                    <div class="postbox">
                        <div class="inside">
                            <form method="post" action="options.php">
                                <?php
                                settings_fields( 'ZLPL-options' );
                                do_settings_sections('ZLPL-options');
                                submit_button('save');
                                ?>
                            </form>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="inside">
                            <h4>Upload QR Image</h4>
                            <form  method="post" enctype="multipart/form-data">
                                <input type='file' id='upload_img' name='upload_img'></input>
                            <p class="description">Upload Your Collect QR Code for donations</p>
                            <?php submit_button('Upload'); ?>
                            </form>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php do_action( 'ps_awac_settings_bottom' ); ?>
</div>