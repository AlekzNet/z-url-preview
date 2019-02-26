<?php
/*
  Plugin Name: Z-URL Preview
  Plugin URI: http://www.z-add.co.uk/
  Description: A plugin to embed a preview of a link, similar to facebook
  Version: 2.0.0
  Author: Stuart Millington
  Author URI: http://www.z-add.co.uk
  License: GPL
 */

add_action( 'wp_enqueue_scripts', 'zurlpreview_styles_method' );

function zurlpreview_styles_method() {
	wp_enqueue_style(
		'zurlcustom-style',
		plugins_url() . '/z-url-preview/zurlplugin.css'
	);
	wp_add_inline_style( 'zurlcustom-style', get_option('zurlpreview_css') );
}

// Media Button
function add_zurlpreview_media_button() {
    global $typenow;
    if (get_option('zurlpreview_buttonrow') == '5') {
        if ($typenow == "post") {
            echo '<a href="#" id="insert-zurlpreview-media" class="button"><img src="' . plugins_url( '/button.png', __FILE__ ) . '" width="22" height="22"> Add Preview </a>';
        }
    } elseif (get_option('zurlpreview_buttonrow') == '6') {
        if (($typenow == "post") || ($typenow == "page")) {
            echo '<a href="#" id="insert-zurlpreview-media" class="button"><img src="' . plugins_url( '/button.png', __FILE__ ) . '" width="22" height="22"> Add Preview </a>';
        }
    } else {
        echo '<a href="#" id="insert-zurlpreview-media" class="button"><img src="' . plugins_url( '/button.png', __FILE__ ) . '" width="22" height="22"> Add Preview </a>';
    }
}
function include_zurlpreview_media_button_js_file() {
    wp_enqueue_script('media_button', plugins_url( '/zurlplugin_media.js', __FILE__ ), array('jquery'), '1.0', true);
}

if (get_option('zurlpreview_buttonrow') == '4') {   // Media Button Row unconditional
	add_action('media_buttons', 'add_zurlpreview_media_button', 50);
	add_action('wp_enqueue_media', 'include_zurlpreview_media_button_js_file');
} elseif (get_option('zurlpreview_buttonrow') == '5') {   // Media Button Row only for post type
	add_action('media_buttons', 'add_zurlpreview_media_button', 50);
	add_action('wp_enqueue_media', 'include_zurlpreview_media_button_js_file');
} elseif (get_option('zurlpreview_buttonrow') == '6') {   // Media Button Row only for post and page types
	add_action('media_buttons', 'add_zurlpreview_media_button', 50);
	add_action('wp_enqueue_media', 'include_zurlpreview_media_button_js_file');
}


// TinyMCE
//if ((get_option('zurlpreview_buttonrow') > 0) && (get_option('zurlpreview_buttonrow') < 4)) {
if ((get_option('zurlpreview_buttonrow') > 6) || (get_option('zurlpreview_buttonrow') < 4)) {
	add_action( 'admin_head', 'at_zurlpreview_add_tinymce' );

	function at_zurlpreview_add_tinymce() {
		global $typenow;

		// only on Post Type: post and page
		if( ! in_array( $typenow, array( 'post', 'page' ) ) )
			return ;

		add_filter( 'mce_external_plugins', 'at_zurlpreview_add_tinymce_plugin' );
		if (get_option('zurlpreview_buttonrow') == '3') {
			// Add to line 1 form WP TinyMCE
			add_filter( 'mce_buttons_3', 'at_zurlpreview_add_tinymce_button' );
		} elseif (get_option('zurlpreview_buttonrow') == '2') {
			// Add to line 1 form WP TinyMCE
			add_filter( 'mce_buttons_2', 'at_zurlpreview_add_tinymce_button' );
		} else {
			// Add to line 1 form WP TinyMCE
			add_filter( 'mce_buttons', 'at_zurlpreview_add_tinymce_button' );
		}
	}
}
// inlcude the js for tinymce
function at_zurlpreview_add_tinymce_plugin( $plugin_array ) {

    $plugin_array['at_zurlpreview'] = plugins_url( '/zurlplugin.js', __FILE__ );
    return $plugin_array;
}

// Add the button key for address via JS
function at_zurlpreview_add_tinymce_button( $buttons ) {

    array_push( $buttons, 'at_zurlpreview_button_key' );
    return $buttons;
}

/* Runs on plugin activation */
register_activation_hook(__FILE__, 'zurlpreview_install');

/* Runs on plugin deactivation */
register_deactivation_hook(__FILE__, 'zurlpreview_remove');

function zurlpreview_install() {
    /* Creates new database field */
    add_option("zurlpreview_css", get_zurlpreview_css(), '', 'yes');
    add_option("zurlpreview_linktxt", get_zurlpreview_linktxt(), '', 'yes');
    add_option("zurlpreview_linkmode", get_zurlpreview_linkmode(), '', 'yes');
    add_option("zurlpreview_buttonrow", get_zurlpreview_buttonrow(), '', 'yes');
    add_option("zurlpreview_parsemode", get_zurlpreview_parsemode(), '', 'yes');
    add_option("zurlpreview_noheadtag", get_zurlpreview_noheadtag(), '', 'yes');
    add_option("zurlpreview_noheadtag", get_zurlpreview_noimage(), '', 'yes');
    add_option("zurlpreview_noheadtag", get_zurlpreview_nointro(), '', 'yes');
    add_option("zurlpreview_noheadtag", get_zurlpreview_titlelink(), '', 'yes');
    add_option("zurlpreview_linkheader", get_zurlpreview_linkheader(), '', 'yes');
    add_option("zurlpreview_linkimage", get_zurlpreview_linkimage(), '', 'yes');
}

function zurlpreview_remove() {
    /* Deletes the database field */
    delete_option('zurlpreview_css');
    delete_option('zurlpreview_linktxt');
    delete_option('zurlpreview_linkmode');
    delete_option('zurlpreview_buttonrow');
    delete_option('zurlpreview_parsemode');
    delete_option('zurlpreview_noheadtag');
    delete_option('zurlpreview_noimage');
    delete_option('zurlpreview_nointro');
    delete_option('zurlpreview_titlelink');
    delete_option('zurlpreview_linkheader');
    delete_option('zurlpreview_linkimage');
}

function get_zurlpreview_css() {
    return '.at_zurlpreview img {
				width: 100%;
				max-width:100%;
 			}';
}

function get_zurlpreview_linktxt() {
    return 'Source:';
}
function get_zurlpreview_linkmode() {
    return 'target-blank';
}
function get_zurlpreview_buttonrow() {
    return '1';
}
function get_zurlpreview_parsemode() {
    return 'd';
}
function get_zurlpreview_noheadtag() {
    return 'No';
}
function get_zurlpreview_noimage() {
    return 'No';
}
function get_zurlpreview_nointro() {
    return 'No';
}
function get_zurlpreview_titlelink() {
    return 'No';
}
function get_zurlpreview_linkheader() {
    return 'No';
}
function get_zurlpreview_linkimage() {
    return 'No';
}

if (is_admin()) {

    /* Call the html code */
    add_action('admin_menu', 'zurlpreview_admin_menu');

    function zurlpreview_admin_menu() {
        add_options_page('Z-URL Preview', 'Z-URL Preview', 'administrator', 'hello-world', 'z_url_preview_option_page');
    }
}

add_filter('tiny_mce_before_init', 'zurl_tiny_mce_before_init');

function zurl_tiny_mce_before_init($initArray) {
    $initArray['setup'] = <<<JS
[function(ed) {
    ed.onKeyDown.add(function(ed, e) {
        console.debug('Key down event: ' + e.keyCode);
    });
}][0]
JS;
    return $initArray;
}

function z_url_preview_option_page() {
    ?>
    <div>
        <h2>Z-URL Preview Options</h2>
        <hr>
        <form method="post" action="options.php">
            <?php wp_nonce_field('update-options'); ?>

            <table width="510">
                <tr valign="top">
                    <td scope="row" colspan="2">Z-URL Preview</td>
                </tr>
                <tr valign="top">
                	<td width="130">CSS</td>
                    <td width="380">
                        <textarea name="zurlpreview_css" id="zurlpreview_css" rows="10" cols="60"><?php echo get_option('zurlpreview_css'); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                	<td width="130">Link Label</td>
                    <td width="380">
                        <input type="text" name="zurlpreview_linktxt" id="zurlpreview_linktxt" value="<?php echo get_option('zurlpreview_linktxt'); ?>"/>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Link Mode</td>
                    <td width="380">
                        <select name="zurlpreview_linkmode" id="zurlpreview_linkmode">
                        	<option value="same-window" <?php selected( get_option('zurlpreview_linkmode'), 'same-window'); ?>>Open in same window</option>
                        	<option value="target-blank" <?php selected( get_option('zurlpreview_linkmode'), 'target-blank'); ?>>New window (target=_blank)</option>
                        	<option value="target-newwindow" <?php selected( get_option('zurlpreview_linkmode'), 'target-newwindow'); ?>>New window (target=newwindow)</option>
                        	<option value="rel-external" <?php selected( get_option('zurlpreview_linkmode'), 'rel-external'); ?>>New window (rel=external)</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Supress Header Tag</td>
                    <td width="380">
                        <select name="zurlpreview_noheadtag" id="zurlpreview_noheadtag">
                        	<option value="No" <?php selected( get_option('zurlpreview_noheadtag'), 'No'); ?>>No</option>
                        	<option value="Yes" <?php selected( get_option('zurlpreview_noheadtag'), 'Yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Supress Image</td>
                    <td width="380">
                        <select name="zurlpreview_noimage" id="zurlpreview_noimage">
                        	<option value="No" <?php selected( get_option('zurlpreview_noimage'), 'No'); ?>>No</option>
                        	<option value="Yes" <?php selected( get_option('zurlpreview_noimage'), 'Yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Supress Intro</td>
                    <td width="380">
                        <select name="zurlpreview_nointro" id="zurlpreview_nointro">
                        	<option value="No" <?php selected( get_option('zurlpreview_nointro'), 'No'); ?>>No</option>
                        	<option value="Yes" <?php selected( get_option('zurlpreview_nointro'), 'Yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Title as Link Text</td>
                    <td width="380">
                        <select name="zurlpreview_titlelink" id="zurlpreview_titlelink">
                        	<option value="No" <?php selected( get_option('zurlpreview_titlelink'), 'No'); ?>>No</option>
                        	<option value="Yes" <?php selected( get_option('zurlpreview_titlelink'), 'Yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Main Heading as Link</td>
                    <td width="380">
                        <select name="zurlpreview_linkheader" id="zurlpreview_linkheader">
                        	<option value="No" <?php selected( get_option('zurlpreview_linkheader'), 'No'); ?>>No</option>
                        	<option value="Yes" <?php selected( get_option('zurlpreview_linkheader'), 'Yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Main Image as Link</td>
                    <td width="380">
                        <select name="zurlpreview_linkimage" id="zurlpreview_linkimage">
                        	<option value="No" <?php selected( get_option('zurlpreview_linkimage'), 'No'); ?>>No</option>
                        	<option value="Yes" <?php selected( get_option('zurlpreview_linkimage'), 'Yes'); ?>>Yes</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Button Location</td>
                    <td width="380">
                        <select name="zurlpreview_buttonrow" id="zurlpreview_buttonrow">
                        	<option value="1" <?php selected( get_option('zurlpreview_buttonrow'), '1'); ?>>TinyMCE Row 1</option>
                        	<option value="2" <?php selected( get_option('zurlpreview_buttonrow'), '2'); ?>>TinyMCE Row 2</option>
                        	<option value="3" <?php selected( get_option('zurlpreview_buttonrow'), '3'); ?>>TinyMCE Row 3</option>
                        	<option value="4" <?php selected( get_option('zurlpreview_buttonrow'), '4'); ?>>Media Button Row</option>
                                <option value="5" <?php selected( get_option('zurlpreview_buttonrow'), '5'); ?>>Media Button (Post Only)</option>
                                <option value="6" <?php selected( get_option('zurlpreview_buttonrow'), '6'); ?>>Media Button (Post/Page Only)</option>
                        </select>
                    </td>
                </tr>
				<tr valign="top">
                	<td width="130">Parse Mode</td>
                    <td width="380">
                        <select name="zurlpreview_parsemode" id="zurlpreview_parsemode">
                        	<option value="r" <?php selected( get_option('zurlpreview_parsemode'), 'r'); ?>>Regex (Orig)</option>
                        	<option value="d" <?php selected( get_option('zurlpreview_parsemode'), 'd'); ?>>HTML Dom</option>
                        </select>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="zurlpreview_css,zurlpreview_linktxt,zurlpreview_linkmode,zurlpreview_buttonrow,zurlpreview_parsemode,zurlpreview_noheadtag,zurlpreview_noimage,zurlpreview_nointro,zurlpreview_titlelink,zurlpreview_linkheader,zurlpreview_linkimage" />

            <p>
                <input type="submit" value="<?php _e('Save Changes') ?>" />
            </p>

        </form>
    </div>
    <?php
}
?>
