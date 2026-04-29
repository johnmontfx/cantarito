<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/StarterSite.php';

Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

new StarterSite();



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

    // Get the theme data.
    $the_theme = wp_get_theme();

    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    // Grab asset urls.
    $theme_styles  = "/css/child-theme{$suffix}.css";
    $theme_scripts = "/js/child-theme{$suffix}.js";

    // Add font awesome
    wp_enqueue_style( 'margarita-theme-fontawesome-pro', get_stylesheet_directory_uri() . '/fonts/fontawesome-pro/css/all.css','', '6.4.2', 'all' );

    // Use file mod time for version instead of theme version
    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), filemtime( get_stylesheet_directory() . '/css/child-theme.css' ) );
    wp_enqueue_script( 'jquery' );

    // Use file mod time for version instead of theme version
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), filemtime( get_stylesheet_directory() . '/css/child-theme.js' ), true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Remove the powerpress styling for subscribe buttons
    wp_dequeue_style( 'powerpress_subscribe_widget_modern' );


}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 999 );



// Podcast support in articles
// Custom function to see if this is a podcast

function fx_is_podcast() {

    global $post;
    $type = get_post_type($post);
    if  (in_array($type, array('fxpodcasts', 'fxguidetv', 'fxguidetves', 'therc', 'thevfxshow') ) )  {
            return true;
    }
    return false;
}



// Add styling to the medialement player
// https://www.cssigniter.com/css-style-guide-for-the-default-wordpress-media-player/

add_action( 'wp_print_footer_scripts', 'fx_mejs_add_container_class' );

function fx_mejs_add_container_class() {

    if ( ! wp_script_is( 'mediaelement', 'done' ) ) {
        return;
    }
    ?>
    <script>
    (function() {
        var settings = window._wpmejsSettings || {};
        settings.features = settings.features || mejs.MepDefaults.features;
        settings.features.push( 'exampleclass' );
        MediaElementPlayer.prototype.buildexampleclass = function( player ) {
            player.container.addClass( 'fx-mejs-container' );
        };
    })();
    </script>
    <?php
}



// Put the PowerPress player above the content for podcast articles

add_filter('the_content', 'fx_add_powerpress');

function fx_add_powerpress( $content ) {

    // Check if powerpress plugin is installed
    if( !function_exists('the_powerpress_content') ) {
        return $content;
    }

    if ( fx_is_podcast() ) {
        $player = '<div id="fxplayer">' . get_the_powerpress_content() . '</div>';
        $content = $player . $content ;
    }

 return $content;

}

// Add our primary sidebar
add_action( 'widgets_init', 'fx_widgets_init' );

function fx_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Main Sidebar', 'mytheme' ),
        'id'            => 'main-sidebar',
        'description'   => esc_html__( 'Add widgets here to appear in your main sidebar.', 'mytheme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>',
    ) );
}

// Add the sidebar to the Timber context
add_filter( 'timber/context', 'add_sidebar_to_context' );

function add_sidebar_to_context( $context ) {
    $context['main_sidebar'] = Timber::get_widgets( 'main-sidebar' );
    return $context;
}



// Add the fxguide theme for Simple Lightbox
//

function fx_lightbox_theme_init($themes) {
    //Path to theme's directory
    $base_path = get_template_directory_uri();
    $base_url = site_url();
    if ( 0 === strpos($base_path, $base_url) ) {
        $base_path = substr($base_path, strlen($base_url));
    }

    //Theme properties
    $properties = array (
        'name'          => __('fxguide dark', 'slb-fxguide'),
        'parent'        => 'slb_baseline',
        'styles'        => array (
            array ( 'core', $base_path . '/lib/lightbox-fxguide/css/style.css' ),
        ),
    );

    $themes->add('slb_fxguide', $properties);
}

add_action('slb_themes_init', 'fx_lightbox_theme_init');

