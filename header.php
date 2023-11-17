<?php
/**
 * * The template for displaying the header
 *
 * @package your-wp-project
 */
?>
	<!doctype html>
	<html <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<?php wp_head(); ?>
	</head>

	<style>
		.general-header {
			background-image: linear-gradient(264deg, rgba(92, 134, 76, 0.75) -3.81%, rgba(1, 124, 138, 0.67) 21.3%, rgba(0, 34, 57, 0.75) 50.36%),
			url("<?php if (has_post_thumbnail($post->ID)){ $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail'); echo  $image[0];}else{ echo get_stylesheet_directory_uri() .  '/assets/img/backgrounds/header-bg.jpg';	}?>");	
		}



	@media screen and (max-width: 800px) {
				.general-header{
				background-image: linear-gradient(to bottom,
				rgba(var(--color-dark-blue-a), .95), rgba(var(--color-blue-a), 0.95)),
				url("<?php if (has_post_thumbnail($post->ID)){ $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail'); echo  $image[0];}else{ echo get_stylesheet_directory_uri() .  '/assets/img/backgrounds/header-bg.jpg';	}?>");
			}
		}


		
	</style>

	<body class="container front-page" id="top">
		<a class="screen-reader-shortcut" href="#main-content" tabindex="1">Skip to main content</a>
		
		<!-- Header -->
		<header id="top" class="header general-header"  role="banner">
			<div class="header__top">
				<!-- Logo image-->
				<a class="header__logo" href="#top" title="Click here to go to the Home page">
					<?php
					// If logo uploaded to customizer display it, if not show nothing
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
					if ( has_custom_logo() ) {
						echo '<img class="header__icon" src="' . esc_url( $logo[0] ) . '"' . 'alt="' . get_bloginfo( 'name' ) . '" draggable="false">';
					} else {
						echo '';
					} ?>
				</a> <!-- .Logo image -->

				<?php
				// Display site navigation
				echo site_navigation();
				echo mobile_navigation();	?>
			</div>

			<div class="header__content">
				<h1 class="header__heading"><?php
							if(is_front_page()){
								echo get_bloginfo( 'description' );
							}else if (is_404()) {
								echo '404 Error';
							} else {
							echo the_title();
							}
						?>
				</h1>

				<div class="header__description">
					<?php
						if (is_front_page()) {
							echo the_content();
						} else if (is_404()) {
							echo 'Sorry You may be lost!';
						} 
					?>
				</div>

					<?php 
					$link = get_field('header_link_1');
					if( $link && is_front_page() ): 
							$link_url = $link['url'];
							$link_title = $link['title'];
							$link_target = $link['target'] ? $link['target'] : '_self';
							?>
							<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" title="Learn more about <?php $link_title?> now" ><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
							</div>
					<?php endif;
				
			
				$link = get_field('header_link_2');
				if( $link && is_front_page()): 
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" title="Learn more about <?php $link_title?> now" ><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
						</div>
				<?php endif;

				$link = get_field('header_link_3');
				if( $link && is_front_page()): 
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" title="Learn more about <?php $link_title?> now" ><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
						</div>
				<?php endif;
				?>
			</div>
		</header><!-- .Header -->