<?php
/**
 * Online Market functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Online Market
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function market_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Online Market, use a find and replace
		* to change 'market' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'market', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'header-menu' => esc_html__( 'Header Menu', 'market'),
			'footer-menu' => esc_html__( 'Footer Menu', 'market' ),
			'language-menu' => esc_html__( 'Language Menu', 'market' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'market_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'market_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function market_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'market_content_width', 640 );
}
add_action( 'after_setup_theme', 'market_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function market_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'market' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'market' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'market_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function market_scripts() {
	wp_enqueue_style( 'market-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_enqueue_style( 'market-main-style', get_stylesheet_directory_uri() . '/assets/css/main.min.css', array(), _S_VERSION );
	wp_style_add_data( 'market-style', 'rtl', 'replace' );

	wp_enqueue_script( 'market-main-scripts', get_template_directory_uri() . '/assets/js/main.min.js', array('jquery'), _S_VERSION, true );

	wp_localize_script( 
		'market-main-scripts', 
		'my_ajax_object', 
		array( 
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('ajax-nonce') 
		) 
	);
}
add_action( 'wp_enqueue_scripts', 'market_scripts' );

if (is_admin()) {
	wp_enqueue_style( 'market-acf-style', get_stylesheet_directory_uri() . '/assets/css/editor-acf.css', array(), _S_VERSION );
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' => 'Global Content',
		'menu_slug' => 'global-content',
		'position' => '21',
		'icon_url' => 'dashicons-welcome-widgets-menus',
	));
  }

// Removes from admin menu
add_action( 'admin_menu', 'my_remove_admin_menus' );
function my_remove_admin_menus() {
    remove_menu_page( 'edit-comments.php' );
}
// Removes from post and pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
// Removes from admin bar
function mytheme_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

function load_custom_wp_admin_style(){
    wp_register_style( 'custom_wp_admin_css', get_bloginfo('stylesheet_directory') . '/admin-style.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');

//exclide node_modules and git folders from ain1wm
 
add_filter( 'ai1wm_exclude_themes_from_export', function ( $exclude_filters ) {
	$exclude_filters[] = 'online-market/node_modules';
	$exclude_filters[] = 'online-market/.git'; 
	return $exclude_filters;
} );

