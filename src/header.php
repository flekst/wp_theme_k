<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ktheme1
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<!--a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ktheme1' ); ?></a-->



	<header id="masthead" class="site-header">
		<div class="site-branding">

<div class="custom-header">

	<?php
		$header      = get_custom_header();
    	$header->url = get_header_image();
    	$custom_logo_id = get_theme_mod( 'custom_logo' );
    	$logo 		 = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    	@$logo = $logo[0];
    	if (!$header->url) {
    		$header->url=get_template_directory_uri().'/images/default-header.jpg';
    	}

		if (!$logo) {
    		$logo=get_template_directory_uri().'/images/default-logo.jpg';
    	}
    ?>
	<div class="custom-header-media" style="background-image: url(<?=$header->url;?>);">

		<div class="site-branding-container">
				 <img class="site-logo" src="<?= $logo;?>"</img>



			<div class="site-title-container">
			<?php
			if ( is_front_page() && is_home() ) :
				?>

				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$ktheme1_description = get_bloginfo( 'description', 'display' );
			if ( $ktheme1_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $ktheme1_description; /* WPCS: xss ok. */ ?></p>

			<?php endif; ?>
			</div>
		</div><!-- .site-branding -->
</div><!-- .layout-wrap -->
		<nav id="site-navigation" class="main-navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'ktheme1' ); ?></button>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'primary-menu',
			) );
			?>
		</nav><!-- #site-navigation -->
</div><!-- .custom-header -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">
