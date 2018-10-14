<?php

/**
 * Class WP_Statistics_TinyMCE
 */
class WP_Statistics_TinyMCE {

	/**
	 * Setup an TinyMCE action to close the notice on the overview page.
	 */
	static function init() {

	    //Add Filter TinyMce Editor
        add_action('admin_head', array(self::class, 'wp_statistic_add_my_tc_button'));

        //Load Text Widget Button
        add_action( 'admin_enqueue_scripts', array(self::class, 'load_tinymcejs_widget_wp_admin_style') );

        //Add TextLang
        add_action( 'admin_footer-widgets.php', array(self::class, 'my_post_edit_page_footer'), 999 );

	}

	/*
	 * Language List Text Domain
	 */
	static public function lang()
    {
        if ( ! class_exists( '_WP_Editors' ) )
            require( ABSPATH . WPINC . '/class-wp-editor.php' );

        $strings = array(
            'insert' => __('WP Statistics Shortcodes', 'wp-statistics'),
            'stat' => __('Stat', 'wp-statistics'),
            'usersonline' => __('User Online', 'wp-statistics'),
            'visits' => __('Visits', 'wp-statistics'),
            'visitors' => __('Visitors', 'wp-statistics'),
            'pagevisits' => __('Page Visits', 'wp-statistics'),
            'searches' => __('Searches', 'wp-statistics'),
            'postcount' => __('Post Count', 'wp-statistics'),
            'pagecount' => __('Page Count', 'wp-statistics'),
            'commentcount' => __('Comment Count', 'wp-statistics'),
            'spamcount' => __('Spam Count', 'wp-statistics'),
            'usercount' => __('User Count', 'wp-statistics'),
            'postaverage' => __('Post Average', 'wp-statistics'),
            'commentaverage' => __('Comment Average', 'wp-statistics'),
            'useraverage' => __('User Average', 'wp-statistics'),
            'lpd' => __('lpd', 'wp-statistics'),
            'help_stat' => __('The statistics you want, see the next table for available options.', 'wp-statistics'),
            'time' => __('Time', 'wp-statistics'),
            'se' => __('Select item ...', 'wp-statistics'),
            'today' => __('Today', 'wp-statistics'),
            'yesterday' => __('Yesterday', 'wp-statistics'),
            'week' => __('Week', 'wp-statistics'),
            'month' => __('Month', 'wp-statistics'),
            'year' => __('Year', 'wp-statistics'),
            'total' => __('Total', 'wp-statistics'),
            'help_time' => __('Is the time frame (time periods) for the statistic', 'wp-statistics'),
            'provider' => __('Provider', 'wp-statistics'),
            'help_provider' => __('ask / bing / clearch / duckduckgo / google / yahoo / yandex', 'wp-statistics'),
            'format' => __('Format', 'wp-statistics'),
            'help_format' => __('The format  for numbers, the options are i18n', 'wp-statistics'),
            'id' => __('Id', 'wp-statistics'),
            'help_id' => __('The page/post ID to get statistics on.  Only used on page visits.', 'wp-statistics'),
        );

        $locale = _WP_Editors::$mce_locale;
        $translated = 'tinyMCE.addI18n("' . $locale . '.wp_statistic_tinymce_plugin", ' . json_encode( $strings ) . ");\n";

        return array('locale' => $locale, 'translate' => $translated);
	}

	/*
	 * Add Filter TinyMCE
	 */
    static public function wp_statistic_add_my_tc_button()
    {
        global $typenow;

        // check user permissions
        if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            return;
        }

        // verify the post type
        if( ! in_array( $typenow, array( 'post', 'page' ) ) )
            return;

        // check if WYSIWYG is enabled
        if ( get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins",  array(self::class, "wp_statistic_add_tinymce_plugin"));
            add_filter('mce_buttons',  array(self::class, 'wp_statistic_register_my_tc_button'));
            add_filter('mce_external_languages',  array(self::class, 'wp_statistic_tinymce_plugin_add_locale'));
        }
    }

    /*
     * Add Js Bottun to Editor
     */
    static function wp_statistic_add_tinymce_plugin($plugin_array) {
        $plugin_array['wp_statistic_tc_button'] =  WP_Statistics::$reg['plugin-url'].'assets/js/tinymce.js';
        return $plugin_array;
    }

    /*
     * Push Button to TinyMCE Advance
     */
    static function wp_statistic_register_my_tc_button($buttons) {
        array_push($buttons, "wp_statistic_tc_button");
        return $buttons;
    }

    /*
     * Add Lang Text Domain
     */
    static function wp_statistic_tinymce_plugin_add_locale($locales) {
        $locales ['wp-statistic-tinymce-plugin'] =  WP_Statistics::$reg['plugin-dir'].'includes/functions/tinymce.php';
        return $locales;
    }

    /*
     * Add Button For Text Widget
     */
    static function load_tinymcejs_widget_wp_admin_style(){
        global $pagenow;
        if($pagenow =="widgets.php") {
            wp_enqueue_script( 'add_wp_statistic_button_for_widget_text', WP_Statistics::$reg['plugin-url'].'assets/js/tinymce.js' );
        }
    }

    /*
     * Add Lang for Text Widget
     */
    static function my_post_edit_page_footer(){
        echo '
        <script type="text/javascript">
        jQuery( document ).on( \'tinymce-editor-setup\', function( event, editor ) {
                editor.settings.toolbar1 += \',wp_statistic_tc_button\';
        });
        ';
        echo self::lang()['translate'];
        echo '
        tinyMCEPreInit.load_ext("'.rtrim( WP_Statistics::$reg['plugin-url'], "/").'", "'.self::lang()['locale'].'");
        </script>
    ';
    }
}
