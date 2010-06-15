<?php
/*
Plugin Name: K2 Style Switcher
Plugin URI: http://thecuriousfrog.com/projects/k2-style-switcher/
Description: This plugin allows site visitors to change the active style when the excellent K2 theme is selected.
Version: 1.1.2
Author: Hugh Johnson
Author URI: http://thecuriousfrog.com
*/
/*
	Copyright (C) 2008-2009 Hugh Johnson  (email : hugh.johnson@thecuriousfrog.com)

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
/*
	This plugin is based on the K2 source files. It was originally written by Kimya Hasira (kimya.hasira@digitaldiversified.com)
*/

//Check plugin class has not already been defined
if ( !class_exists( "k2ss_plugin" ) ) {
	class k2ss_plugin {
		//Declare the variable containing the plugin options name
		var $plugin_options_name = 'k2_style_switcher_plugin_options';

		//Declare the variable containing the plugin text domain name
		var $plugin_text_domain_name = 'k2-style-switcher';

		//This function is the constructor for the plugin class
		function k2ss_plugin() {
			//Nothing to see here...
		}

		//This function returns an array of the plugin options
		function get_plugin_options() {
			//Declare an array containing the plugin options and default values
			$new_plugin_options = array(
				'display_format' => 'list',
				'styles_array' => array(),
				'default_style' => '',
				'footer_code' => 'Styled with <a title="%stylename%" href="%styleuri%">%stylename%</a> by <a title="%authorname%" href="%authoruri%">%authorname%</a>.',
				'display_powered_by' => 'show',
				'display_feed_links' => 'show',
				'display_styled_with' => 'hide',
				'display_stats' => 'hide'
			);

			//Attempt to retrieve the existing array of options from the database
			$existing_plugin_options = get_option( $this->plugin_options_name );

			//Overwrite the default options values with any retrieved from database
			if ( !empty( $existing_plugin_options ) ) {
				foreach( $existing_plugin_options as $key => $value ) {
					$new_plugin_options[$key] = $value;
				}
			}

			//Return the array of options
			return $new_plugin_options;
		}

		//This function returns text to be inserted into the WP contextual help menu for the plugin options page
		function output_help_menu() {
			$help_text = '<a href="http://thecuriousfrog.com/projects/k2-style-switcher/" target="_blank">' . __( 'Plugin Homepage' , 'k2-style-switcher' ) . '</a>';
			$help_text .= '<br /><a href="http://wordpress.org/extend/plugins/k2-style-switcher/faq/" target="_blank">' . __( 'Plugin FAQs' , 'k2-style-switcher' ) . '</a>';
			$help_text .= '<br /><a href="http://wordpress.org/tags/k2-style-switcher#postform" target="_blank">' . __( 'Plugin Support Forum' , 'k2-style-switcher' ) . '</a>';
			return $help_text;
		}

		//This function generates the plugin options menu
		function output_admin_menu() {
			//Get the directory that the k2 style files are stored in
			$styles_dir = get_option( 'k2stylesdir' );

			//If the directory is not valid then output an error message
			if ( !is_dir( $styles_dir ) ) {
				echo( '<div class="error">' );
				printf( __( 'The directory where k2 custom styles are stored is missing. The value set in the k2 options is: <strong>%s</strong>. For you to be able to use custom styles (and this plugin), you need to set the correct directory on the k2 options menu.', 'k2-style-switcher' ), $styles_dir );
				echo( '</div>' );
			}

			//Get an array of records containing information about inactive K2 styles in $styles_dir
			$available_styles = $this->get_available_styles( $styles_dir, 'inactive' );

			//Find out how many K2 styles are available in total (active and inactive)
			$styles_count = count( $this->get_available_styles( $styles_dir, 'both' ) );

			//WordPress does not automatically show the 'Options Updated' message, we will generate it ourselves
			if ( isset( $_GET['updated'] ) ) {
				echo ( '<div class="updated"><p><strong>' . __( 'K2 Style Switcher options have been updated.', 'k2-style-switcher' ) . '</strong></p></div>' );
			}
			?>

			<div class="wrap">

			<h2>K2 Style Switcher</h2>

			<h3><?php _e( 'Plugin Options' , 'k2-style-switcher' ) ?></h3>

			<p><em><?php _e( 'Click on the option names to display inline help' , 'k2-style-switcher' ) ?></em></p>

			<script type="text/javascript">
			<!--
				function toggleVisibility(id) {
				   var e = document.getElementById(id);
				   if(e.style.display == 'block' )
					  e.style.display = 'none';
				   else
					  e.style.display = 'block';
				}
			//-->
			</script>

			<form method="post" action="options.php">

			<?php settings_fields( 'k2_style_switcher_option_group' ); ?>

			<?php $existing_options = $this->get_plugin_options(); ?>

			<table class="form-table">

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher' )?>" onclick="toggleVisibility('default_style_picker_tip');"><?php _e( 'Default Style:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<select name="k2_style_switcher_plugin_options[default_style]" style="width: 300px; ">
			<?php
			foreach( $available_styles as $style ) {
				//Set the value of the option (the info stored in the options array) to the path of the style
				echo( '<option value="' . $style['path'] . '"' );

				//If this style is the current default highlight it
				if ( $style['path'] == $existing_options['default_style'] ) {
					echo( ' selected' );
				}

				//If the style has a name specified then display that on-screen, if not then use the path
				if ( $style['stylename'] == ''){
					echo( '>' . $style['path'] . '</option>' );
				} else {
					echo( '>' . $style['stylename'] . '</option>' );
				}
			}
			?>
			</select>
			<div style="text-align:left; display:none" id="default_style_picker_tip">
			<?php printf( __( 'The site will be displayed using this style unless the visitor chooses another one' , 'k2-style-switcher' ), '"k2ss_style_picker"' ) ?>
			</div>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher' )?>" onclick="toggleVisibility('style_picker_format_tip');"><?php _e( 'Default Style Picker Format:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<label for="display_format_list"><input type="radio" id="display_format_list" name="k2_style_switcher_plugin_options[display_format]" value="list" <?php if ( $existing_options['display_format'] == "list" ) { echo( 'checked="checked"' ); }?> />Unordered List</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="display_format_menu"><input type="radio" id="display_format_menu" name="k2_style_switcher_plugin_options[display_format]" value="menu" <?php if ( $existing_options['display_format'] == "menu" ) { echo( 'checked="checked"' ); }?>/>Dropdown Menu</label>
			<div style="text-align:left; display:none" id="style_picker_format_tip">
			<?php printf( __( 'This option controls the way that list of available styles is displayed at the point where the template calls the %s function.' , 'k2-style-switcher' ), '"k2ss_style_picker"' ) ?>
			</div>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher')?>" onclick="toggleVisibility('footer_template_tip');"><?php _e( 'Footer Template:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<textarea name="k2_style_switcher_plugin_options[footer_code]" rows="6" cols="88"><?php echo $existing_options['footer_code']; ?></textarea>
			<div style="text-align:left; display:none" id="footer_template_tip">
			<?php
			_e( 'The following macros are supported:' , 'k2-style-switcher' );
			echo( '<ul>' );
			echo( '<li>' ); _e( '%wporglink% - A link to the WordPress site (http://wordpress.org/)' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%k2comlink% - A link to the K2 site (http://getk2.com/)' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%bloginfoname% - The site title' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%bloginfoversion% - The version of WordPress your site uses' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%k2infoversion% - The version of K2 your site uses' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%rssentries% - The URL for the site\'s RSS 2.0 feed (/feed)', 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%rsscomments% - URL for the site\'s comments RSS 2.0 feed (/comments/feed)' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%authorname% - The author\'s name for the current K2 style' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%authoruri% - The author\'s website for the current K2 style' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%stylename% - The name of the current K2 style' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%styleuri% - The URL for the current K2 style\'s website' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%stylefooter% - The footer content taken from the current K2 style' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%styleversion% - The version of the current K2 style' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '<li>' ); _e( '%stylecomments% - Any comments attached to the current K2 style' , 'k2-style-switcher' ); echo( '</li>' );
			echo( '</ul>' );
			 ?>
			</div>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher' )?>" onclick="toggleVisibility('display_powered_by_tip');"><?php _e( 'Display WordPress footer:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<label for="display_powered_by_show"><input type="radio" id="display_powered_by_show" name="k2_style_switcher_plugin_options[display_powered_by]" value="show" <?php if ( $existing_options['display_powered_by'] == "show" ) { echo( 'checked="checked"' ); }?> />Show</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="display_powered_by_hide"><input type="radio" id="display_powered_by_hide" name="k2_style_switcher_plugin_options[display_powered_by]" value="hide" <?php if ( $existing_options['display_powered_by'] == "hide" ) { echo( 'checked="checked"' ); }?>/>Hide</label>
			<div style="text-align:left; display:none" id="display_powered_by_tip">
			<?php printf( __( 'This option determines if the default WordPress footer ( "%s" ) is displayed', 'k2-style-switcher' ), __( 'Powered by', 'k2-style-switcher' ) . ' <a href="http://wordpress.org/">' . __( 'WordPress', 'k2-style-switcher' ) . '</a> ' . __( 'and', 'k2-style-switcher' ) . ' <a href="http://getk2.com/" title="' . __( 'Loves you like a kitten.', 'k2-style-switcher' ) . '">K2</a>' ) ?>
			</div>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher')?>" onclick="toggleVisibility('display_feed_links_tip');"><?php _e( 'Display feed links:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<label for="display_feed_links_show"><input type="radio" id="display_feed_links_show" name="k2_style_switcher_plugin_options[display_feed_links]" value="show" <?php if ( $existing_options['display_feed_links'] == "show" ) { echo( 'checked="checked"' ); }?> />Show</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="display_feed_links_hide"><input type="radio" id="display_feed_links_hide" name="k2_style_switcher_plugin_options[display_feed_links]" value="hide" <?php if ( $existing_options['display_feed_links'] == "hide" ) { echo( 'checked="checked"' ); }?>/>Hide</label>
			<div style="text-align:left; display:none" id="display_feed_links_tip">
			<?php printf( __( 'This option determines if the feed links footer ( "%s" ) is displayed', 'k2-style-switcher' ), '<a href="' . get_bloginfo( 'rss2_url' ) . '">' . __( 'Entries Feed','k2-style-switcher' ) . '</a> ' . __( 'and', 'k2-style-switcher') . ' <a href="' . get_bloginfo( 'comments_rss2_url' ) . '">' . __( 'Comments Feed','k2-style-switcher' ) . '</a>' ) ?>
			</div>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher')?>" onclick="toggleVisibility('display_styled_with_tip');"><?php _e( 'Display base styles footer:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<label for="display_styled_with_show"><input type="radio" id="display_styled_with_show" name="k2_style_switcher_plugin_options[display_styled_with]" value="show" <?php if ( $existing_options['display_styled_with'] == "show" ) { echo( 'checked="checked"' ); }?> />Show</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="display_styled_with_hide"><input type="radio" id="display_styled_with_hide" name="k2_style_switcher_plugin_options[display_styled_with]" value="hide" <?php if ( $existing_options['display_styled_with'] == "hide" ) { echo( 'checked="checked"' ); }?>/>Hide</label>
			<div style="text-align:left; display:none" id="display_styled_with_tip">
			<?php _e( 'This option determines if the footer for any base K2 styles is displayed', 'k2-style-switcher' ) ?>
			</div>
			</td>
			</tr>

			<tr valign="top">
			<th scope="row" style="text-align:right; vertical-align:top;">
			<a style="cursor:pointer;" title="<?php _e( 'Click for Help!' , 'k2-style-switcher')?>" onclick="toggleVisibility('display_stats_tip');"><?php _e( 'Display page stats:' , 'k2-style-switcher' ) ?></a>
			</th>
			<td>
			<label for="display_stats_show"><input type="radio" id="display_stats_show" name="k2_style_switcher_plugin_options[display_stats]" value="show" <?php if ( $existing_options['display_stats'] == "show" ) { echo( 'checked="checked"' ); }?> />Show</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="display_stats_hide"><input type="radio" id="display_stats_hide" name="k2_style_switcher_plugin_options[display_stats]" value="hide" <?php if ( $existing_options['display_stats'] == "hide" ) { echo( 'checked="checked"' ); }?>/>Hide</label>
			<div style="text-align:left; display:none" id="display_stats_tip">
			<?php printf( __( 'This option determines if the page stats footer ( "%s" ) is displayed', 'k2-style-switcher' ), sprintf( __( '%d queries. %.4f seconds.','k2-style-switcher' ), $wpdb->num_queries , timer_stop() ) ) ?>
			</div>
			</td>
			</tr>

			</table>

			<br />

			<p><em><?php printf( __( 'Select the styles you want website visitors to be able to choose from. Styles will only appear on this list if they are not in use by K2, to make a style available for selection you should uncheck the box next to it\'s name (or path) on the %s screen' , 'k2-style-switcher' ), '"<a title="' . __( 'K2 Options', 'k2-style-switcher' ) . '" href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/themes.php?page=k2-options' . '">' . __( 'K2 Options', 'k2-style-switcher' ) . '</a>"' ) ?></em></p>

			<table id="k2ss-styles" class="widefat" cellspacing="0">
			<thead>
			<tr>
			<th class="manage-column column-cb check-column" scope="col">
			<input type="checkbox" />
			</th>
			<th class="manage-column column-title"><?php _e( 'Style', 'k2-style-switcher' ); ?></th>
			<th class="manage-column column-author"><?php _e( 'Author', 'k2-style-switcher' ); ?></th>
			<th class="manage-column column-version"><?php _e( 'Version', 'k2-style-switcher' ); ?></th>
			<th class="manage-column column-tags"><?php _e( 'Tags', 'k2-style-switcher' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php if ( empty( $available_styles ) ): ?>
			<tr>
			<td colspan="5">
			<?php
			if ( $styles_count > 0 ) {
				printf( __( 'There are %1s K2 styles in the current K2 styles directory ( %2s ) but they are all currently active', 'k2-style-switcher' ), $styles_count, $styles_dir );
			} else {
				printf( __( 'There are no K2 styles in the current K2 styles directory ( %s )', 'k2-style-switcher' ), $styles_dir );
			}
			?>
			</td>
			</tr>
			<?php else: foreach( $available_styles as $style ): ?>
			<tr>
			<th class="check-column" scope="row">
			<input type="checkbox" name="k2_style_switcher_plugin_options[styles_array][]" value="<?php echo attribute_escape( $style['path'] ); ?>" <?php if ( in_array( $style['path'], $existing_options['styles_array'] ) ) echo 'checked="checked"'; ?> />
			</th>
			<td class="column-title">
			<?php
			if ( $style['stylename'] == '' ) {
				echo ( $style['path'] );
			} else {
				echo ( '<strong>' . $style['stylename'] . '</strong>' . ' (' . $style['path'] . ')' );
			}
			?>
			</td>
			<td class="column-author">
			<a href="<?php echo $style['site']; ?>"><?php echo $style['author']; ?></a>
			</td>
			<td class="column-version">
			<?php echo $style['version']; ?>
			</td>
			<td class="column-tags">
			<?php echo $style['tags']; ?>
			</td>
			</tr>
			<?php endforeach; endif; ?>
			</tbody>
			</table>

			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' , 'k2-style-switcher' ) ?>" />
			</p>

			</form>

			<h3><?php _e( 'Footer Preview' , 'k2-style-switcher' ) ?></h3>
			<?php
			//Print the footer previews
			$this->output_footer( $existing_options['default_style'] );

			echo ( '<h3>' ); _e( 'Support and Further Info' , 'k2-style-switcher' ); echo( '</h3>' );
			echo ( '<p>' ); printf( __( 'Do you have a question or problem? Visit the %s homepage and leave a comment' , 'k2-style-switcher' ), '<a href="http://thecuriousfrog.com/projects/k2-style-switcher/" target="_blank">K2 Style Switcher</a>' ); echo( '</p>' );

			echo ( '</div>' );

			add_action( 'in_admin_footer', array( &$this, 'output_admin_menu_footer' ) );
		}

		//This function generates the plugin options menu footer
		function output_admin_menu_footer() {
			$plugin_data = get_plugin_data( __FILE__ );
			printf( '%1$s plugin | Version %2$s | by %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author'] );
		}

		//This function registers the plugin admin panel and contextual help menu
		function register_admin_menu() {
			if ( function_exists( 'add_submenu_page' ) ) {
				$options_page_hook = add_submenu_page( 'themes.php', 'K2 Style Switcher', 'K2 Style Switcher', 'switch_themes', __FILE__, array( &$this, 'output_admin_menu' ) );
				if ( function_exists( 'add_contextual_help' ) ) {
					$help_text = $this->output_help_menu();
					add_contextual_help( $options_page_hook, $help_text );
				}
			}
		}

		//This function generates the style picker
		function output_style_picker( $style_picker_format = '' ) {
			//Get the plugin options array
			$existing_options = $this->get_plugin_options();

			//If no value was specified for $style_picker_format then use the default
			if ( empty( $style_picker_format ) ) {
				$style_picker_format = $existing_options['display_format'];
			}

			//Get the directory that the k2 style files are stored in
			$styles_dir = get_option( 'k2stylesdir' );

			//Append a trailing forward slash to the dir path
			$styles_dir .= '/';

			//Get the relative path to the current style
			$current_style_path = $this->get_current_style_path();

			//Grab the array of available styles
			$available_styles = $existing_options['styles_array'];

			//Generate the style picker using the format specified by $style_picker_format
			if ( $style_picker_format == "list" ) {
				//Open the form
				echo( '<ul>' );
				echo( '
					<script language="JavaScript" type="text/javascript">
					<!--
					function k2ss_script ( selected_style )
					{
					document.k2ss_ss_picker.k2ss_selected_style.value = selected_style ;
					document.k2ss_ss_picker.submit() ;
					}
					-->
					</script>
				' );
				echo( '<form name="k2ss_ss_picker" action="' . $k2ss_set_active_style_action . '" method="post">' );
				echo( '<input type="hidden" name="k2ss_selected_style" />' );

				//Loop through the available styles
				foreach( $available_styles as $style ) {
					//Get the style metadata for the current style
					$style_info = $this->get_style_data( $style, $styles_dir );

					//If the style has a name specified in the CSS file
					if ( !empty( $style_info['stylename'] ) ) {
						//If this style is the default style
						if ( $current_style_path == $style_info['path'] ) {
							echo( '<li class="k2ss_selected"><a href="javascript:k2ss_script(\'' . $style_info['path'] . '\')">' . $style_info['stylename'] . '</a></li>' );
						} else {
							echo( '<li><a href="javascript:k2ss_script(\'' . $style_info['path'] . '\')">' . $style_info['stylename'] . '</a></li>' );
						}
					} else {
						//If this style is the default style
						if ( $current_style_path == $style_info['path'] ) {
							echo( '<li class="k2ss_selected"><a href="javascript:k2ss_script(\'' . $style_info['path'] . '\')">' . $style_info['path'] . '</a></li>' );
						} else {
							echo( '<li><a href="javascript:k2ss_script(\'' . $style_info['path'] . '\')">' . $style_info['path'] . '</a></li>' );
						}
					}
				}

				//Close the form
				echo( '</form>' );
				echo( '</ul>' );
			} else if ( $style_picker_format == "menu" ) {
				//Open the form
				echo( '<div>' );
				echo( '<form action=" " method="post">' );
				echo( '<input type="hidden" name="action" value="' . $k2ss_set_active_style_action . '" />' );
				echo( '<select name="k2ss_selected_style">' );

				//Loop through the available styles
				foreach( $available_styles as $style ) {
					//Get the style metadata for the current style
					$style_info = $this->get_style_data( $style, $styles_dir );

					//If the style has a name specified in the CSS file
					if ( !empty( $style_info['stylename'] ) ) {
						//If this style is the default style
						if ( $current_style_path == $style_info['path'] ) {
							echo( '<option value="' . $style_info['path'] . '" selected>' . $style_info['stylename'] . '</option>' );
						} else {
							echo( '<option value="' . $style_info['path'] . '">' . $style_info['stylename'] . '</option>' );
						}
					} else {
						//If this style is the default style
						if ( $current_style_path == $style_info['path'] ) {
							echo( '<option value="' . $style_info['path'] . '" selected>' . $style_info['path'] . '</option>' );
						} else {
							echo( '<option value="' . $style_info['path'] . '">' . $style_info['path'] . '</option>' );
						}
					}
				}

				//Close the form
				echo( '</select>' );
				echo( '<input type="submit" value="' . __( 'Go!', 'k2-style-switcher' ) . '" style="border: 1px solid Silver; margin: 0 0 0 2px;" />');
				echo( '</form>' );
				echo( '</div>' );
			}
		}

		//This function adds a note to the K2 administration panel informing the user how base and selectable styles work
		function output_k2_options_note() {
			echo ( '<div class="updated"><p><strong>' . __( 'K2 Style Switcher:', 'k2-style-switcher' ) . '</strong> ' . __( 'On this page you should select ONLY the styles that should applied in addition to the style chosen using the K2 Style Switcher.', 'k2-style-switcher' ) . '</p></div>' );
		}

		//This function is called when the plugin is activated
		function install_options() {
			//Add the plugin options to the database with default values
			$new_plugin_options = $this->get_plugin_options();

			//Grab any existing options from the old option system, then delete the old options
			foreach( $new_plugin_options as $key => $value ) {
				if( $old_option = get_option( 'k2ss_' . $key ) ) {
					$new_plugin_options[$key] = $old_option;
					delete_option( 'k2ss_' . $key );
				}
			}

			//Store the array of options in the database
			update_option( $this->plugin_options_name, $new_plugin_options );

			//Banish disused options from the database
			delete_option( 'k2ss_default_scheme' );
		}

		//This function whitelists the plugins options
		function register_settings() {
			register_setting( 'k2_style_switcher_option_group', 'k2_style_switcher_plugin_options', array( &$this, 'validate_options' ) );
		}

		//This function validates input from the admin panel
		function validate_options( $input ) {
			//Return the validated options array
			return $input;
		}

		//This function loads the plugin's text domain
		function load_l10n_domain() {
			$plugin_dir = basename( dirname( __FILE__ ) );

			load_plugin_textdomain( $this->plugin_text_domain_name, '', $plugin_dir . '/languages' );
		}

		//Displays footer on page. Plugin disables default k2 scheme so a replacement footer was needed.
		function output_footer( $style_path = '' ) {
			//Get the plugin options array
			$existing_options = $this->get_plugin_options();

			//Get the directory that the k2 style files are stored in
			$styles_dir = get_option( 'k2stylesdir' );

			//Get the relative path to the current style
			$current_style_path = $this->get_current_style_path();

			//The style to render the footer for has been passed in, override the current style
			if ( !empty( $style_path ) ) {
				$current_style_path = $style_path;
			}

			//Get the metadata associated with $current_style_path
			$current_style_data = $this->get_style_data( $current_style_path, $styles_dir );

			//Get the style meta data from the array
			$authorname = $current_style_data['author'];
			$authoruri = $current_style_data['site'];
			$stylename = $current_style_data['stylename'];
			$styleuri = $current_style_data['stylelink'];
			$stylefooter = $current_style_data['footer'];
			$styleversion = $current_style_data['version'];
			$stylecomments = $current_style_data['comments'];

			//Get the other field values
			$wporglink = sprintf( '<a title="%1s" href="http://wordpress.org">%2s</a>', __( 'WordPress', 'k2-style-switcher' ), __( 'WordPress', 'k2-style-switcher' ) );
			$k2comlink = sprintf( '<a title="%1s" href="http://getk2.com">%2s</a>', __( 'Loves you like a kitten.', 'k2-style-switcher' ), __( 'K2', 'k2-style-switcher' ) );
			$bloginfoname = get_bloginfo( 'name' );
			$bloginfoversion = get_bloginfo( 'version' );
			$k2infoversion = get_k2info( 'version' );
			$rssentries = sprintf( '<a href="%1s">%2s</a>', get_bloginfo( 'rss2_url' ), __( 'Entries Feed', 'k2-style-switcher' ) );
			$rsscomments = sprintf( '<a href="%1s">%2s</a>', get_bloginfo( 'comments_rss2_url' ), __( 'Comments Feed', 'k2-style-switcher' ) );

			//Get the footer template
			$footer_output = $existing_options['footer_code'];

			//Replace all the macros with the values
			$footer_output = str_replace( "%authorname%", $authorname, $footer_output );
			$footer_output = str_replace( "%authoruri%", $authoruri, $footer_output );
			$footer_output = str_replace( "%stylename%", $stylename, $footer_output );
			$footer_output = str_replace( "%styleuri%", $styleuri, $footer_output );
			$footer_output = str_replace( "%stylefooter%", $stylefooter, $footer_output );
			$footer_output = str_replace( "%styleversion%", $styleversion, $footer_output );
			$footer_output = str_replace( "%stylecomments%", $stylecomments, $footer_output );
			$footer_output = str_replace( "%wporglink%", $wporglink, $footer_output );
			$footer_output = str_replace( "%k2comlink%", $k2comlink, $footer_output );
			$footer_output = str_replace( "%bloginfoname%", $bloginfoname, $footer_output );
			$footer_output = str_replace( "%bloginfoversion%", $bloginfoversion, $footer_output );
			$footer_output = str_replace( "%k2infoversion%", $k2infoversion, $footer_output );
			$footer_output = str_replace( "%rssentries%", $rssentries, $footer_output );
			$footer_output = str_replace( "%rsscomments%", $rsscomments, $footer_output );

			//Output the footer
			echo( '<p class="footerk2ss">' . $footer_output . '</p>' );
		}

		//This function adds custom stylesheet to header
		function output_css_link() {
			//Get the plugin options array
			$existing_options = $this->get_plugin_options();

			//Get the URL of the directory that the k2 style files are stored in
			$styles_url = get_option( 'k2stylesurl' );

			//Get the relative path to the current style
			$current_style_path = $this->get_current_style_path();

			//Construct the full URL of the active style's CSS file
			$current_style_full_path = $styles_url . '/' . $current_style_path;

			//Output the link to the active style's CSS file
			echo( '<link rel="stylesheet" type="text/css" media="screen" href="' . $current_style_full_path . '" />' );
		}

		//This function returns the relative path to the style that should be used
		function get_current_style_path() {
			//Get the plugin options array
			$existing_options = $this->get_plugin_options();

			if ( isset( $_GET['k2ss_target_style'] ) ) {
				//Use the style specified in the 'k2ss_target_style' parameter of the URL e.g. http://example.com/?k2ss_target_style=style/style.css
				$current_style_path = $_GET['k2ss_target_style'];
			} elseif ( isset( $_SESSION['k2ss_current_style'] ) ) {
				//Use the style specified in the $_SESSION variable
				$current_style_path = $_SESSION['k2ss_current_style'];
			} else {
				//If we are loading a single post, page, attachement etc.
				if ( is_singular() ) {
					//The current post (or page)
					global $post;

					//Attempt to retrieve the value associated with the 'k2ss_default_style' custom field key
					$page_default_style_path = get_post_meta( $post->ID, 'k2ss_default_style', true );

					//If no value was set for the custom field key
					if ( empty( $page_default_style_path ) ) {
						//Use the site default style
						$current_style_path = $existing_options['default_style'];
					} else {
						//Use the style specified in the custom field
						$current_style_path = $page_default_style_path;
					}
				} else {
					//Use the site default style
					$current_style_path = $existing_options['default_style'];
				}
			}

			//Return the relative path to the style
			return $current_style_path;
		}

		//This function removes any unwanted footer elements
		function output_css_classes() {
			//Get the plugin options array
			$existing_options = $this->get_plugin_options();

			//Open the CSS block
			echo( '<!-- K2 Style Switcher -->' );
			echo( '<style type="text/css" media="screen">' );

			//Shown by default
			if ( $existing_options['display_powered_by'] == "hide" ) {
				echo( 'p.footerpoweredby { display: none; }' );
			}

			//Shown by default
			if ( $existing_options['display_feed_links'] == "hide" ) {
				echo( 'p.footerfeedlinks { display: none; }' );
			}

			//Hidden by default
			if ( $existing_options['display_styled_with'] == "hide" ) {
				echo( 'p.footerstyledwith { display: none; }' );
			}

			//Hidden by default
			if ( $existing_options['display_stats'] == "show" ) {
				echo( 'p.footerstats { display: block; }' );
			}

			//Close the CSS block
			echo( '</style>' );
			echo( '<!-- /K2 Style Switcher -->' );
		}

		//This function parses the style data out of a K2 CSS style sheet
		function get_style_data( $style_file = '', $styles_dir ) {
			//If no style selected, exit
			if ( '' == $style_file ) {
				return false;
			}

			//Construct the path to the style
			$style_path = $styles_dir . "/" . $style_file;

			//If the file cannot be read, exit
			if ( ! is_readable($style_path) ) {
				return false;
			}

			//Convert the file into a string
			$style_data = implode( '', file( $style_path ) );

			//Convert the line feed characters
			$style_data = str_replace( '\r', '\n', $style_data );

			//Pull the information out of the style
			if ( preg_match("|Author Name\s*:(.*)$|mi", $style_data, $author) )
				$author = trim( $author[1] );
			else
				$author = '';

			if ( preg_match("|Author Site\s*:(.*)$|mi", $style_data, $site) )
				$site = clean_url( trim( $site[1] ) );
			else
				$site = '';

			if ( preg_match("|Style Name\s*:(.*)$|mi", $style_data, $stylename) )
				$stylename = trim( $stylename[1] );
			else
				$stylename = '';

			if ( preg_match("|Style URI\s*:(.*)$|mi", $style_data, $stylelink) )
				$stylelink = clean_url( trim( $stylelink[1] ) );
			else
				$stylelink = '';

			if ( preg_match("|Style Footer\s*:(.*)$|mi", $style_data, $footer) )
				$footer = trim( $footer[1] );
			else
				$footer = '';

			if ( preg_match("|Version\s*:(.*)$|mi", $style_data, $version) )
				$version = trim( $version[1] );
			else
				$version = '';

			if ( preg_match("|Comments\s*:(.*)$|mi", $style_data, $comments) )
				$comments = trim( $comments[1] );
			else
				$comments = '';

			if ( preg_match("|Header Text Color\s*:\s*#*([\dABCDEF]+)|i", $style_data, $header_text_color) )
				 $header_text_color = $header_text_color[1];
			else
				 $header_text_color = '';

			if ( preg_match("|Header Width\s*:\s*(\d+)|i", $style_data, $header_width) )
				$header_width = (int) $header_width[1];
			else
				$header_width = 0;

			if ( preg_match("|Header Height\s*:\s*(\d+)|i", $style_data, $header_height) )
				$header_height = (int) $header_height[1];
			else
				$header_height = 0;

			$layout_widths = array();
			if ( preg_match("|Layout Widths\s*:\s*(\d+)\s*(px)?,\s*(\d+)\s*(px)?,\s*(\d+)|i", $style_data, $widths) ) {
				$layout_widths[1] = (int) $widths[1];
				$layout_widths[2] = (int) $widths[3];
				$layout_widths[3] = (int) $widths[5];
			}

			if ( preg_match("|Tags\s*:(.*)$|mi", $style_data, $tags) )
				$tags = trim($tags[1]);
			else
				$tags = '';

			//Return the gathered information as an array
			return array(
				'path' => $style_file,
				'modified' => filemtime($style_path),
				'author' => $author,
				'site' => $site,
				'stylename' => $stylename,
				'stylelink' => $stylelink,
				'footer' => $footer,
				'version' => $version,
				'comments' => $comments,
				'header_text_color' => $header_text_color,
				'header_width' => $header_width,
				'header_height' => $header_height,
				'layout_widths' => $layout_widths,
				'tags' => $tags
			);
		}

		//This function returns an array of records containing information about the k2 CSS style files in $styles_dir
		function get_available_styles( $styles_dir, $style_state = 'both' ) {
			//Append a trailing backslash to the path in case it is needed
			$styles_dir .= '/';

			//Declare the array to store the output in
			$style_files = array();

			//Open the directory
			if ( ( $dir = @dir( $styles_dir ) ) !== false ) {
				//Get all the files in the directory
				while ( ($file = $dir->read() ) !== false ) {
					//If this is a directory then scan it
					if ( is_dir( $styles_dir . $file ) && !preg_match( '/^\.+$/i', $file ) ) {
						$styles_subdir = $styles_dir . $file . '/';
						//Open the subdirectory
						if ( ( $sub_dir = @dir( $styles_subdir ) ) !== false ) {
							//Get all the files in the sub directory
							while ( ($sub_file = $sub_dir->read() ) !== false ) {
								if ( is_file( $styles_subdir . $sub_file ) && preg_match( '/\.css$/i', $sub_file ) ) {
									//If this is a CSS file then add it to the list
									$style_files[] = $file . '/' . $sub_file;
								}
							}
							//Close the subdirectory
							$sub_dir->close();
						}
					} elseif ( is_file( $styles_dir . $file ) && preg_match( '/\.css$/i', $file ) ) {
						//If this is a CSS file then add it to the list
						$style_files[] = $file;
					}
				}
				//Close the directory
				$dir->close();
			}

			//Sort the contents of the array
			sort( $style_files );

			//Get the array of currently active K2 styles
			$active_styles = get_option( 'k2styles' );

			//Make sure that $style_files contains only the type of styles specified in $style_state
			if ( empty( $active_styles ) && $style_state == "active" ) {
				//Assign the empty array of active K2 styles to $style_files
				$style_files = $active_styles;
			} else {
				//If there are any current active K2 styles
				if ( !empty( $active_styles ) ) {
					//Populate an array containing only the inactive K2 styles
					$inactive_styles = array_diff( $style_files, $active_styles );

					//Load $style_files based on the preference indicated by $style_state
					if ( $style_state == "inactive" ) {
						//We only want inactive styles in the array
						$style_files = $inactive_styles;
					} elseif ( $style_state == "active" ) {
						//We only want active styles in the array
						$style_files = $active_styles;
					} elseif ( $style_state == "both" ) {
						//We want both inactive and active styles in the array
						$style_files = array_merge( $active_styles, $inactive_styles );
					}
				}
			}

			//Declare an array containing the style data for each file
			$k2_styles = array();

			//Loop through $style_files populating the data on each k2 style into $k2_styles
			foreach ( (array) $style_files as $style_file ) {
				$style_data = $this->get_style_data( $style_file, $styles_dir );

				if ( ! empty( $style_data ) ) {
					$k2_styles[] = $style_data;
				}
			}

			//Populate the output variable
			$style_files = $k2_styles;

			//Return the output
			return $style_files;
		}
	}
}//End class k2ss_plugin

//Check widget class has not already been defined
if ( !class_exists( "k2ss_widget" ) ) {
	class k2ss_widget extends WP_Widget {
		//This function is the constructor for the widget class
		function k2ss_widget() {
			$widget_ops = array( 'classname' => 'widget_k2ss', 'description' => __( 'A style picker allowing site visitors to change the active style when the K2 theme is active' , 'k2-style-switcher' ) );
			$this->WP_Widget( 'k2ss', 'K2 Style Switcher', $widget_ops );
		}

		function widget( $args, $instance ) {
			//$args is an array of strings that help widgets to conform to the active theme: before_widget, before_title, after_widget, and after_title are the array keys.
			extract( $args );

			//Apply filters to the Widget Title
			$title = apply_filters( 'widget_title', empty( $instance[ 'title' ] ) ? '' : $instance[ 'title' ] );

			//Output the tags that come before the widget
			echo $before_widget;

			//Output the Widget Title (if non-blank)
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			//Generate the output for the widget in the appropriate format
			if( $instance[ 'display_format' ] == "list" ) {
				//Generate an un-ordered list version of the style picker
				k2ss_style_picker( 'list' );
			} else {
				//Generate a drop-down menu version of the style picker
				k2ss_style_picker( 'menu' );
			}

			//Output the tags that come after the widget
			echo $after_widget;
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
			$instance[ 'display_format' ] = strip_tags( $new_instance[ 'display_format' ] );
			return $instance;
		}

		function form( $instance ) {
			//Set the defaults
			$instance = wp_parse_args( ( array ) $instance, array( 'title' => __( 'K2 Style Switcher' , 'k2-style-switcher' ), 'display_format' => 'list' ) );

			//Clean up the options
			$title = strip_tags( $instance[ 'title' ] );
			$display_format = strip_tags( $instance[ 'display_format' ] );
			?>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , 'k2-style-switcher' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</label>

			<label for="<?php echo $this->get_field_id( 'display_format' ); ?>"><?php _e( 'Style Picker Format:' , 'k2-style-switcher' ); ?>
				<select name="<?php echo $this->get_field_name( 'display_format' ); ?>" id="<?php echo $this->get_field_id( 'display_format' ); ?>" class="widefat">
					<option value="list"<?php if ( $display_format == "list" ) echo( ' selected="selected" ' ); ?>><?php _e( 'Unordered List' , 'k2-style-switcher' ); ?></option>
					<option value="menu"<?php if ( $display_format == "menu" ) echo( ' selected="selected" ' ); ?>><?php _e( 'Dropdown Menu' , 'k2-style-switcher' ); ?></option>
				</select>
			</label>

			<input type="hidden" id="<?php echo $this->get_field_id( 'submit' ); ?>" name="<?php echo $this->get_field_name( 'submit' ); ?>" value="1" />
			<?php
		}
	}
}//End class k2ss_widget

//Declare an instance of the plugin class
if ( class_exists( "k2ss_plugin" ) ) {
	//Only create an instance of the plugin class if K2 is the current theme
	if ( get_current_theme() == 'K2' ) {
		$k2ss_instance = new k2ss_plugin();
	} else {
		return;
	}
}

//This function registers the widget
if ( !function_exists( "k2ss_register_widget" ) ) {
	function k2ss_register_widget() {
		register_widget( 'k2ss_widget' );
	}
}

//This function stores the relative path to the users chosen style in a PHP session variable
if ( !function_exists( "k2ss_set_active_style_action" ) ) {
	function k2ss_set_active_style_action() {
		//If there is any data in $_POST
		if ( !empty( $_POST ) ) {
			//If the 'k2ss_current_style' element is set in $_POST then copy it to $_SESSION
			if ( isset( $_POST['k2ss_selected_style'] ) ) {
				$_SESSION['k2ss_current_style'] = $_POST['k2ss_selected_style'];
			}
		} elseif ( isset( $_GET['k2ss_target_style'] ) ) {
			//Use the style specified in the 'k2ss_target_style' parameter of the URL
			$_SESSION['k2ss_current_style'] = $_GET['k2ss_target_style'];
		}

		//Initialize the session data
		session_start();
	}

	//Create a var that can be used as the action in HTML form elements
	$k2ss_set_active_style_action = k2ss_set_active_style_action();
}

//This function outputs the style picker menu, $style_picker_format can be 'list' or 'menu'
if ( !function_exists( "k2ss_style_picker" ) ) {
	function k2ss_style_picker( $style_picker_format ) {
		//Grab the instance of the plugin class
		global $k2ss_instance;

		//If for some reason no instance of the plugin class exists then bail out
		if( !isset( $k2ss_instance ) ) {
			return;
		}

		//Call the on-class function
		$k2ss_instance->output_style_picker( $style_picker_format );
	}
}

//Plugin actions and filters
if ( isset($k2ss_instance ) ) {
	//Add hook to place the generated text in the site footer
	add_action( 'template_footer', array( &$k2ss_instance, 'output_footer' ) );

	//Add hook to place CSS link for the selected style in the site header
	add_action( 'wp_head', array( &$k2ss_instance, 'output_css_link' ) );

	//Add hook to output CSS classes to remove unwanted footer elements
	add_action( 'wp_head', array( &$k2ss_instance, 'output_css_classes' ) );

	//Add hook to add plugin menu to admin screen
	add_action( 'admin_menu', array( &$k2ss_instance, 'register_admin_menu' ) );

	//Add hook to register the plugin settings
	add_action( 'admin_init', array( &$k2ss_instance, 'register_settings' ) );

	//Add hook to load the plugin's text domain
	add_action( 'init', array( &$k2ss_instance, 'load_l10n_domain' ) );

	//Add hook to set the session var
	add_action( 'init', 'k2ss_set_active_style_action' );

	//Add a hook to register the widget
	add_action( 'widgets_init' , 'k2ss_register_widget' );

	add_action( 'k2_options_top', array( &$k2ss_instance, 'output_k2_options_note' ) );
}

//Register the activation hook
if ( isset( $k2ss_instance ) ) {
	register_activation_hook( __FILE__, array( &$k2ss_instance, 'install_options' ) );
}
?>