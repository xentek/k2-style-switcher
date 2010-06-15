=== K2 Style Switcher ===
Contributors: xenlab, thecuriousfrog
Donate link: http://j.mp/donate-to-xentek
Tags: k2, style, style switcher, theme, theme switcher
Requires at least: 2.8
Tested up to: 2.9.2
Stable tag: trunk

This WordPress plugin allows site visitors to change the active style in the K2 theme.

== Description ==

This plugin is the equivalent of a theme switcher for styles applied to the K2 theme. It allows your visitors to re-skin your site from a list of K2 styles that you select.

The default K2 style to be applied to a given post or page can be specified using a parameter added to the URL, post or page meta data, or an overall site default.

Style selections made by the user are persistent for the duration of their visit and override default values, however they can be overridden using a URL parameter.

== Installation ==

**For this plugin to work correctly the latest version of K2 must be installed and selected as the current theme**

1. Unzip the plugin archive
1. Upload the entire `k2-style-switcher` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Goto the K2 admin panel ('Appearance' > 'K2 Options') and select the K2 styles that you ALWAYS want to be applied to the site (these are called the 'Base Styles' in our terminology)
1. Goto the K2 Style Switcher admin panel ('Appearance' > 'K2 Style Switcher') and select the K2 styles that you want visitor to be able to choose from. You may also want to customize the other settings at this point
1. To display the Style Picker that allows visitors to change the active K2 Style, either use the included widget or place the following code where you want it to appear: `<?php k2ss_style_picker(); ?>`. If the widget is used then the format of the Style Picker is set via the widget options, otherwise the default format set in the K2 Style Switcher admin panel is used.

== Frequently Asked Questions ==

= Do I Need To Make Any Changes to K2? =

No, the K2 Style Switcher is completely self contained, no changes are required to the K2 core files

= What Is The Difference Between A Base Style And A Selectable Style? =

Base styles are activated on the K2 admin panel, these styles are applied to the site in addition to either the default selectable style or one that the site visitor has chosen. Base styles are intended to be used to add any snippets of CSS that should be loaded in all cases, thus saving the administrator from having to add these CSS snippets to each of the selectable styles.

Selectable styles are activated on the K2 Style Switcher admin panel, these are the styles that the site visitors can choose from. The default selectable style chosen on the K2 Style Switcher admin panel is applied to the site before the site visitor chooses a different selectable style. Note that a K2 style cannot be both a base style and a selectable style at once. If it is chosen as a base style on the K2 admin panel then it will disappear from the K2 Style Switcher admin panel.

= Why Can't I See the K2 Style Switcher Menu Under 'Appearance' In The Admin Area? =

If K2 is not the active theme then the plugin does not instantiate itself and will not appear in the administration area or on the site. Make sure that K2 is the active theme.

= When a Style Is Specified Using a URL Parameter Is It Set As The Current Style For The Whole Site? =

Yes. When the page loads the selected style is stored in the PHP session variables as if the user had selected it using the style picker.

= When a Style Is Specified Using a Custom Field Is It Set As The Current Style For The Whole Site? =

No. The style specified in the custom field will only be used for the post or page that the custom field is set for. The style for the rest of the site will be selected using the normal logic.

= Why Isn't XYZ Working? =

The K2 Style Switcher relies on various options set by K2. For it to function correctly please make sure that the latest versions of K2 and WordPress are installed. If you are still having problems then you can ask questions, leave comments or find more information at the plugin's homepage: [K2 Style Switcher](http://xentek.net/code/wordpress/plugins/k2-style-switcher/ "K2 Style Switcher")

= I want to help with development of this Plugin! =

The project is now hosted on [github.com](http://github.com/xentek/mu-helpers). Just fork the project and send me a pull request.

[New to git?](http://delicious.com/ericmarden/git)

== Screenshots ==

1. The K2 Style Switcher options panel (partial)

== Changelog ==

= 1.1.3 =
* Cleaned up plugin header and readme as part of take over from original plugin author.

= 1.1.2 =
* Added `languages` folder for l10n files
* Added German translation (de_DE), many thanks to [Julian Manzel](http://www.zeitdieb.org/ "Julian Manzel")
* Confirmed compatibility against WordPress 2.8.5

= 1.1.1 =
* Added code to allow the current style to specified as a URL parameter, for example `http://example.com/?k2ss_target_style=style/style.css`
* Added code to allow the default style for a page or post to be specified using a custom field called `k2ss_default_style`

= 1.1.0 =
* Encapsulated plugin functionality in a class
* Consolidated plugin options into an array stored in a single option
* Cleaned up code to match [WordPress Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards/ "WordPress Coding Standards")
* Re-wrote admin panel code to use the new options mechanism introduced in WordPress 2.7
* Made all plugin messages translatable
* Added footer message to plugin admin panel stating the name of the plugin, the current version and the author's name
* Re-wrote plugin activation hook to attempt to pull in existing options from current and previous versions of the plugin as well as removing redundant options
* Added call to load the plugin l10n / i18n text domain
* Removed plugin deactivation code to stop configuration options being removed when plugin is deactivated
* Added uninstall script to ensure that configuration options are removed when plugin is deleted
* Added plugin widget in a class based on the WP_Widget class introduced in WordPress 2.8
* Added option to hide specific elements of WordPress & K2 standard footers
* Prevented styles that are always selected from being added to the list of switchable styles
* Took out custom WPMU checks, detection of K2 styles directory and URL is now based on K2 options
* Added a message to the K2 options page explaining how styles are layered
* Added code to make sure that the plugin is not loaded if K2 is not active

= 1.0.0 =
* Initial version of plugin originally written by Kimya Hasira
* Added WPMU compatibility

== Uninstallation ==

* If you wish to stop using the plugin but keep your configuration options then simply use the 'Deactivate' link on the WordPress 'Manage Plugins' screen
* If you wish to completely uninstall the plugin then use the 'Deactivate' link followed by the 'Delete' link on the WordPress 'Manage Plugins' screen. This removes the plugin files and your configuration options

== Usage ==

This plugin includes a widget that displays either a dropdown menu or an unordered list of the available styles. The title of the widget and the type of menu to use are set in the widget options.

The other plugin options are set using the K2 Style Switcher administration panel ('Appearance' > 'K2 Style Switcher').

The style to apply to a given post or page is chosen using one of the following methods:

1. A paramter can be appended to the page URL containing the relative path to the style, for example: `http://example.com/?k2ss_target_style=style/style.css`
1. If a style has already been set in the PHP sessions variables then it will be applied to the post or page
1. A custom field called `k2ss_default_style` can be added to the page or post. The field value should be set to the relative path to the style, for example: `style/style.css`
1. If none of the above methods have set the style then the default style specified in the K2 Style Switcher administration panel is applied

== Configuration ==

**Default Style**
This option sets the K2 style that visitors will see upon first landing on your page unless another style has been specified using one of the methods described in the Usage section. Once they select a style, they will see that style instead.

**Default Style Picker Format**
This option sets the type of style picker that will be displayed when the `<?php k2ss_style_picker(); ?>` function is called with no parameter set:

* Unordered List - Displays a list of links
* Dropdown Menu  - Displays a dropdown menu

If you opt to use the unordered list when displaying the style picker you can style the link to the currently selected style using the `.k2ss_selected` class. For example: `.k2ss_selected { border-left: 1px dashed #666; }`

**Footer Template**
This option sets the text that is displayed in your site footer. This is re-written each time the page is loaded, meaning that it always displays information relevant to the style that the visitor is currently seeing.

For a list of the available macros click the option name ('Footer Template') on the administration panel. This will display the list of macros and the information they will be replaced with at runtime.

**Display WordPress footer**
This option controls the visibility of the standard WordPress footer ('Powered by WordPress and K2'). You may wish to hide this footer if you are placing the same information in the custom footer template.

**Display feed links**
This option controls the visibility of the feed links footer ('Entries Feed and Comments Feed'). You may wish to hide this footer if you are placing the same information in the custom footer template.

**Display base styles footer**
This option controls the visibility of the active styles K2 footer ('Styled with <stylename> by <authorname>'). You probably wish to hide this footer as any base styles that are active will probably be tweaks that do not need crediting.

**Display page stats**
This option controls the visibility of the WordPress page loading stats footer ('<n> queries. <n> seconds.'). You may wish to show this information, it will appear just above the output of the custom footer template.

**Available Styles**
This option displays all the styles currently available in your K2 styles directory. Select the styles you want visitors to be able to use. Note that styles are only shown here if they are not currently in use by K2.

Any styles that are set as active on the K2 options screen will be used as base styles. This means they will be applied in addition to either the default K2 Style Switcher style or the style that the visitor has chosen. This allows any custom CSS snippets to be maintained in one place rather than being added to each style.

== Release Notes ==

* No known issues are present in this version