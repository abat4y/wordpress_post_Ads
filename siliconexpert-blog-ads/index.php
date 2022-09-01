<?php
/*
 * Plugin Name:  Blog Ads
 * Description: A  WordPress plugin that will place “ads” into blog posts with many option.
 * Version: 4.0
 * Author: sameh helal
 * Author URI: https://www.linkedin.com/in/sameh-helal/
 */

if (!function_exists('add_action')) {
    echo "Hi there! I'm just a plugin, not much I can do when called directly.";
    exit;
}

// Setup
define('RECIPE_PLUGIN_URL', __FILE__);

// Includes
include('includes/activate.php');
include('includes/init.php');
include('includes/front/enqueue.php');


// Hooks
register_activation_hook(__FILE__, 'ads_activate_plugin');
add_action('init', 'ads_init');
add_action('save_post', 'save');
add_filter('the_content', 'checkAdvertsRequired', 10, 3);
function get_data() {
    $response=[];
    // nonce check for an extra layer of security, the function will exit if it fails
       if ( !wp_verify_nonce( $_REQUEST['nonce'], "my_user_like_nonce")) {
      exit("Woof Woof Woof");
   }   
    $post_id = $_REQUEST["post_id"];
    if($post_id){
    // Get the current value.
    $count = (int) get_field('Advert_count',$post_id);
    // Increase it.
    $count++;
    // Update with new value.
    update_field('Advert_count', $count, $post_id);
     $response['message']='done';
    }else {
        $response['message']='something is going wrong';
    }
    //echo  $response['message'] ; //returning this value but still shows 0
    // Check if action was fired via Ajax call. If yes, JS code will be triggered, else the user is redirected to the post page
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($response);
      echo $result;
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
    wp_die();
}
add_action( 'wp_ajax_nopriv_get_data', 'get_data' );
add_action( 'wp_ajax_get_data', 'get_data' );
add_action('admin_menu', 'adminPanelsAndMetaBoxes');
add_action( 'wp_enqueue_scripts', 'ads_enqueue_scripts', 100 );
// Add the custom columns to the ads post type:
add_filter( 'manage_ads_posts_columns', 'set_custom_edit_ads_columns' );
function set_custom_edit_ads_columns($columns) {
    unset( $columns['author'] );
    $columns['ads_author'] = __( 'Advert click count', 'Advert' );

    return $columns;
}

// Add the data to the custom columns for the ads post type:
add_action( 'manage_ads_posts_custom_column' , 'custom_ads_column', 10, 2 );
function custom_ads_column( $column, $post_id ) {
    switch ( $column ) {
        case 'ads_author' :
            $Advert_count = get_field('Advert_count',$post_id );
            if ($Advert_count )
                echo $Advert_count;
            else
                echo '0';
            break;
    }
}
function displayMetaBox($post)
{
    // Get meta
    $adPosition = get_post_meta($post->ID, '_ads_position', true);
    $paragraphNumber = get_post_meta($post->ID, '_paragraph_number_ads', true);

    // Nonce field
    wp_nonce_field('Advert', 'Advert' . '_nonce');
?>
    <div style="clear:both"></div>
    <p>
        <label for="ads_position"><?php _e('Display the advert:', 'Advert'); ?></label>
        <select onChange="javascript:handleIpaAdOptionChange( this.value )" name="ads_position" size="1">
            <option value="top" <?php echo (($adPosition == 'top') ? ' selected' : ''); ?>><?php _e('Before Content', 'Advert'); ?></option>
            <option value="" <?php echo (($adPosition == '') ? ' selected' : ''); ?>><?php _e('After Paragraph Number', 'Advert'); ?></option>
            <option value="bottom" <?php echo (($adPosition == 'bottom') ? ' selected' : ''); ?>><?php _e('After Content', 'Advert'); ?></option>
        </select>
        <input type="number" name="paragraph_number_ads" value="<?php echo $paragraphNumber; ?>" min="1" max="999" step="1" id="paragraph_number_ads" <?php if ($adPosition != '') {
                                                                                                                                                            echo ' style="display: none;"';
                                                                                                                                                        } ?> />
        <script type="text/javascript">
            var ipaParaNumberElem = document.getElementById('paragraph_number_ads');
            var handleIpaAdOptionChange = function(value) {
                if (value != "") {
                    ipaParaNumberElem.style.display = 'none';
                } else {
                    ipaParaNumberElem.style.display = 'inline-block';
                }
            }
        </script>
    </p>

<?php
}
function adminPanelsAndMetaBoxes()
{
    add_meta_box('ads_meta', __('Advert position', 'Advert'), 'displayMetaBox', 'ads', 'normal', 'high');
    $postTypes = get_post_types( array(
        'public' => true,
    ), 'objects' );
    if ( $postTypes ) {
        foreach ( $postTypes as $postType ) {
            // Skip attachments
            if ( $postType->name == 'attachment' ) {
                continue;
            }
            // Skip our CPT
            if ( $postType->name == 'ads') {
                continue;
            }
            add_meta_box( 'ads_meta', __( 'Advert_display', 'Advert' ),  'displayOptionsMetaBox' , $postType->name, 'normal', 'high' );
        }
    }
}
/**
 * Saves the meta box field data
 *
 * @param int $post_id Post ID
 */
function save($post_id)
{
    // Check if our nonce is set.
    if (!isset($_REQUEST['Advert' . '_nonce'])) {
        return $post_id;
    }
    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_REQUEST['Advert' . '_nonce'], 'Advert')) {
        return $post_id;
    }
    // Check the logged in user has permission to edit this post
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    // OK to save meta data
    if (isset($_REQUEST['checkbox_disable_ads'])) {
        update_post_meta($post_id, '_checkbox_disable_ads', sanitize_text_field($_REQUEST['checkbox_disable_ads']));
    } else {
        delete_post_meta($post_id, '_checkbox_disable_ads');
    }
    if (isset($_REQUEST['ads_position'])) {
        update_post_meta($post_id, '_ads_position', sanitize_text_field($_REQUEST['ads_position']));
    }
    if (isset($_REQUEST['paragraph_number_ads'])) {
        update_post_meta($post_id, '_paragraph_number_ads', sanitize_text_field($_REQUEST['paragraph_number_ads']));
    }
}
/**
 * Checks if the current screen on the frontend needs advert(s) adding to it
 */
function checkAdvertsRequired($content)
{
    /**
     * Filter insert_post_ads_enabled Whether ads should be printed.
     * This filter can be used to temporarily stop ads from printing,
     * say, for AMP pages
     *
     * @param bool $enabled Whether ads should be printed
     */
    if (!apply_filters('insert_post_ads_enabled', true)) {
        return $content;
    }

    global $post;
    $postType = 'post';
    // // Check if we are on a singular post type that's enabled
    // foreach ( $this->settings as $postType=>$enabled ) {
    if (is_singular($postType)) {
        // Check the post hasn't disabled adverts
        $disable = get_post_meta($post->ID, '_checkbox_disable_ads', true);
        if (!$disable) {
            return insertAds('Check the post hasnt disabled adverts'.$content);
        }
    }
    // }

    return  $content ;
}

/**
 * Inserts advert(s) into content
 *
 * @param string $content Content
 * @return string Content
 */
function insertAds($content)
{
    $ads = new WP_Query(array(
        'post_type' => 'ads',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'rand',
    ));
    if ($ads->have_posts()) {
        while ($ads->have_posts()) {
            $ads->the_post();
            $adID = get_the_ID();
            $ad_image = get_field('Advert_image',$adID)['url'];
            $ad_link = get_field('Advert_link',$adID)['url'];
            $ad_count = get_field('Advert_count',$adID);
            $nonce = wp_create_nonce("my_user_like_nonce");
	        $link = admin_url('admin-ajax.php?action=my_user_like&post_id='.$adID.'&nonce='.$nonce);
            $adCode = '<a href="'.$ad_link.'" data-ref="'.$ad_count.'" data-nonce="' . $nonce . '" data-post_id="' . $adID . '" class="Advert_count">';
            $adCode .= '<img width="800" height="215" 
            src="'.$ad_image.'"';
            $adCode .=' class="attachment-large size-large" alt="" loading="lazy"'; 
            $adCode .=' srcset="'.$ad_image.' 1300w,'.$ad_image.' 300w,'.$ad_image.' 600w" 
            sizes="(max-width: 800px) 100vw, 800px"></a>';
            
            $adPosition = get_post_meta($adID, '_ads_position', true);
            $paragraphNumber = get_post_meta($adID, '_paragraph_number_ads', true);

            switch ($adPosition) {
                case 'top':
                    $content = $adCode . $content;
                    break;
                case 'bottom':
                    $content = $content . $adCode;
                    break;
                default:
                    $content = insertAdAfterParagraph($adCode, $paragraphNumber, $content);
                    break;
            }
        }
    }

    wp_reset_postdata();
    return $content . $paragraphNumber;
}
/**
 * Insert something after a specific paragraph in some content.
 *
 * @param  string $insertion    Likely HTML markup, ad script code etc.
 * @param  int    $paragraph_id After which paragraph should the insertion be added. Starts at 1.
 * @param  string $content      Likely HTML markup.
 *
 * @return string               Likely HTML markup.
 */
function insertAdAfterParagraph($insertion, $paragraph_id, $content)
{
    $closing_p = '</p>';
    $paragraphs = explode($closing_p, $content);
    foreach ($paragraphs as $index => $paragraph) {
        // Only add closing tag to non-empty paragraphs
        if (trim($paragraph)) {
            // Adding closing markup now, rather than at implode, means insertion
            // is outside of the paragraph markup, and not just inside of it.
            $paragraphs[$index] .= $closing_p;
        }

        // + 1 allows for considering the first paragraph as #1, not #0.
        if ($paragraph_id == $index + 1) {
            $paragraphs[$index] .= '<div class="' . generateRandomString(8) . '"' . ' style="clear:both;float:left;width:100%;margin:0 0 20px 0;"' . '>' . $insertion . '</div>';
        }
    }
    return implode('', $paragraphs);
}
/**
 * Generate a random string of length N
 */
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/**
 * Displays the meta box on Pages, Posts and CPTs
 *
 * @param object $post Post
 */
function displayOptionsMetaBox($post)
{
    // Get meta
    $disable = get_post_meta($post->ID, '_checkbox_disable_ads', true);
    
    // Nonce field
    wp_nonce_field('Advert', 'Advert' . '_nonce');
?>
    <p>
        <label for="checkbox_disable_ads"><?php _e('Disable Adverts', 'Advert'); ?></label>
        <input type="checkbox" name="checkbox_disable_ads" id="checkbox_disable_ads" value="1" 
        <?php echo ($disable ? ' checked' : ''); ?> />
    </p>
    <p class="description">
        <?php _e('Check this option if you wish to disable all Post Ads from displaying on this content.', 'Advert'); ?>
    </p>
<?php
}