<?php
class Xcrud_config{
    // default connection
    public static $dbname = 'wiw2';  // Your database name
    public static $dbuser = 'root';  // Your database username
    public static $dbpass = '';  // // Your database password
    public static $dbhost = '127.0.0.1';  // Your database host, 'localhost' is default.
    public static $dbencoding = 'utf8';  // Your database encoding, default is 'utf8'. Do not change, if not sure.
    public static $mbencoding = 'utf-8';  // Your mb_string encoding, default is 'utf-8'. Do not change, if not sure.
    public static $db_time_zone = false; // database time zone, if you want use system default - leave empty
    public static $language = 'en'; // sets default localization
    // xcrud folder url
    public static $scripts_url = 'http://localhost/~joni/WorldSkills-Mobile-iOS/Aggregator/assets/xcrud'; // URL to the xCRUD folder, not real path, without a trailing slash, can be relative, e.g. 'some_folder/xcrud' or absolute, e.g. 'http://www.your_site.com/some_folder/xcrud'
    //public static $scripts_url = 'http://localhost/~joni/WorldSkills-Mobile-iOS/Aggregator/assets/xcrud'; // URL to the xCRUD folder, not real path, without a trailing slash, can be relative, e.g. 'some_folder/xcrud' or absolute, e.g. 'http://www.your_site.com/some_folder/xcrud'
    public static $theme = 'bootstrap'; // can be 'default', 'bootstrap', 'minimal' or your custom. Theme of xCRUD visual presentation. For using bootstrap you need to load it on your page.
    public static $load_bootstrap = false; // turn on, if you want to load bootstrap via xCRUD
    // advanced settings
    public static $sess_name = 'PHPSESSID'; // If your script is already using the session, specify the session name for it. By default, the name of the session in PHP equal 'PHPSESSID'.
    public static $sess_expire = 30; // Specifies the lifetime of the session, as well as the existence of a key safety (for example, the maximum edit-saving timeout).
    public static $dynamic_session = false; // this option is used for compatibility with with frameworks and cms that using dynamic session name. 
    
    public static $tinymce_folder_url = 'http://localhost/~joni/WorldSkills-Mobile-iOS/Aggregator/assets/tiny_mce'; // URL path to TinyMCE, if you want to use the visual editor. TinyMCE is not included in xCRUD.
    public static $tinymce_init_url = 'http://localhost/~joni/WorldSkills-Mobile-iOS/Aggregator/assets/tiny_mce/init.js'; //  URL path to your custom initialization file for TinyMCE.
    public static $force_tinymce = false; // Forced initialization of TinyMCE, even if the path is not specified. Check this if you're already using TinyMCE on your page.
    
    public static $disable_plugins = false; // Disables loading of all scripts and styles totally, use it when you want to include scripts and style manually
    public static $disable_jquery = true; // Disables loading of jQuery, turn it on if you already have jQuery on your page  by version at least 1.7
    public static $disable_jquery_ui = false; // Disables loading of jQueryUI, turn it on if you already have jQueryUI on your page (datepicker and slider widgets are mandatory).
    public static $jquery_no_conflict = false; // Includes jQuery.noConflict(). Use according to documentation jQuery.
    
    public static $show_primary_ai_field = false; // Show primary auto-increment field in create/edit view.
    public static $show_primary_ai_column = false; // Show primary auto-increment column in list view.
    public static $start_minimized = false; // Start all xCRUD instances minimized.
    public static $remove_confirm = true; // Show confirmation dialog on remove action.
    public static $column_cut = 50; // Sets the maximum number of characters in the column.
    public static $benchmark = false; // Displays information about the performance in the lower right corner.
    
    public static $csv_delimiter = ';'; // default delimiter in CSV file.
    public static $csv_enclosure = '"'; // default enclosure in CSV file.
    public static $csv_all_fields = false; // export all fields and rows of table or only visible.
    
    public static $limit = 20; // default limit of rows per page
    public static $limit_list = array('20','50','100','all'); // default limits list
    public static $top_pagination = false; // create pagination in top
    public static $search_opened = false; // make search always open
    
    public static $clickable_list_links = true; // make all links, emails clikable in list view
    public static $clickable_filenames = true; // makes filenames clikable in list view
    
    public static $make_checkbox = true; // display TINYINT(1),BIT(1),BOOL(1),BOOLEAN(1) fields like checkboxes
    public static $lists_null_opt = true; // display null(empty) option in all dropdowns and multiselects
    public static $fixed_action_buttons = true; // it allows to fix the action buttons on the right side of the table. Appears when you hover on row.
    
    public static $upload_folder_def = '../uploads'; // Default uploads folder on your site, relative to xCRUD folder or absolute path required. Folder is must exist.
    public static $images_in_grid = true; // shows images in list view
    public static $images_in_grid_height = 55; // maximal height of thumbnails in list view
    
    public static $enable_printout = true; // show print button
    public static $enable_search = true; // show searck block
    public static $enable_pagination = true; // show pagination
    public static $enable_csv_export = true; // show csv export button
    public static $enable_table_title = true; // show table title and toggle button
    public static $enable_numbers = true; // show row numbers in grid
    
    
    public static $email_from = 'webmaster@worldskills.org'; // email from address
    public static $email_from_name = 'WSI Mobile Aggregator'; // email from name
    public static $email_enable_html = true; // enables html in email letters
    
    // remote request options (call_page() methods)
    public static $use_browser_info = true; // allow use your browser cookie, referer, user agent for http request to some file or url. BE CAREFUL: DON'T USE IT FOR REQUESTS TO EXTERNAL SITES!!!
    
    
    // system integration options, inactive at this moment. Changing this has no effect.
    public static $integration_mode = false;
    public static $site_url = 'http://localhost/crud';
    public static $plugins_uri = 'assets/xcrud/plugins';
    public static $themes_uri = 'assets/xcrud/themes';
    public static $ajax_uri = 'ajax_loader';
    public static $csv_uri = 'csv_loader';
    public static $image_uri = 'image_loader';
    public static $views_path = '../views/xcrud';
    
    
}

