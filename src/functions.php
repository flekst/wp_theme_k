<?php
// include ('placeholder.php');
/**
 * ktheme1 functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ktheme1
 */

if ( ! function_exists( 'ktheme1_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function ktheme1_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on ktheme1, use a find and replace
		 * to change 'ktheme1' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'ktheme1', get_template_directory() . '/languages' );

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
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'ktheme1' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'ktheme1_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'ktheme1_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function ktheme1_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'ktheme1_content_width', 640 );
}
add_action( 'after_setup_theme', 'ktheme1_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function ktheme1_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'ktheme1' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'ktheme1' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'ktheme1_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function ktheme1_scripts() {
	wp_enqueue_style( 'ktheme1-style', get_stylesheet_uri() );

	wp_enqueue_script( 'ktheme1-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );
	wp_enqueue_script( 'ktheme1-masonry', get_template_directory_uri() . '/js/masonry.pkgd.js', array('jquery'), '20190306', false );

	wp_enqueue_script( 'ktheme1-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ktheme1_scripts' );

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





function getPostById($id){
	//выводит заголовок, миниатюру и ссылку на статью
	//краткое описание берется из цитаты
	$out='';
	$post=get_post($id);
	$out.='<div class="main-art"><h3>';
	$out.='<a href="'.$post->guid.'">';
	$out.=$post->post_title; //получаем заголовок записи
	$out.='</a>';
	$out.='</h3>';
	$out.='<div class="art-img">';
	$out.=get_the_post_thumbnail($id, 'ppthmb'); //получаем миниатюру, ppthmb - класс изображения
	$out.='</div>';
	$out.='<div class="min-content">';
	//получаем цитату из записи
	$out.=get_the_excerpt($post->ID);
	$out.='</div></div>';
	echo $out;
}




function ktheme1_excerpt_more( $more ) {
 $retval = sprintf( '<div class="excerpt_more_link"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) );
 $retval = $retval . __('excerpt_more', 'ktheme1').'</a></div>';

    return $retval;
}
 add_filter( 'excerpt_more', 'ktheme1_excerpt_more' ); 