<?php
/**
 * * The template for displaying the header
 *
 * @package your-wp-project
 */
?>
	<?php
	$header_image = get_stylesheet_directory_uri() . '/assets/img/backgrounds/header-bg.webp';
	$header_image_mobile = get_stylesheet_directory_uri() . '/assets/img/backgrounds/header-bg-mobile.webp';

	if ( is_page( 'resources' ) ) {
		$header_image = get_stylesheet_directory_uri() . '/assets/img/backgrounds/resources-hero.webp';
		$header_image_mobile = get_stylesheet_directory_uri() . '/assets/img/backgrounds/resources-hero-mobile.webp';
	}

	if ( isset( $post->ID ) && has_post_thumbnail( $post->ID ) ) {
		// Use a generated page-banner derivative instead of downloading the full upload.
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'pageBanner' );

		if ( ! empty( $image[0] ) ) {
			$header_image = $image[0];
		}

		$mobile_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium_large' );
		if ( ! empty( $mobile_image[0] ) ) {
			$header_image_mobile = $mobile_image[0];
		}
	}
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="theme-color" content="#002239">
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<link rel="preload" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/fonts/open-sans-latin-variable.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
		<link rel="preload" href="<?php echo esc_url( $header_image ); ?>" as="image"<?php echo 'webp' === pathinfo( (string) wp_parse_url( $header_image, PHP_URL_PATH ), PATHINFO_EXTENSION ) ? ' type="image/webp"' : ''; ?> media="(min-width: 801px)" fetchpriority="high">
		<link rel="preload" href="<?php echo esc_url( $header_image_mobile ); ?>" as="image"<?php echo 'webp' === pathinfo( (string) wp_parse_url( $header_image_mobile, PHP_URL_PATH ), PATHINFO_EXTENSION ) ? ' type="image/webp"' : ''; ?> media="(max-width: 800px)" fetchpriority="high">
		<?php wp_head(); ?>

	<style>
		.general-header {
			background-image: linear-gradient(264deg, rgba(92, 134, 76, 0.75) -3.81%, rgba(1, 124, 138, 0.67) 21.3%, rgba(0, 34, 57, 0.75) 50.36%),
			url("<?php echo esc_url( $header_image ); ?>");
		}



	@media screen and (max-width: 800px) {
				.general-header{
				background-image: linear-gradient(to bottom,
				rgba(var(--color-dark-blue-a), .95), rgba(var(--color-blue-a), 0.95)),
				url("<?php echo esc_url( $header_image_mobile ); ?>");
			}
		}


		
	</style>
	</head>

	<body <?php body_class( 'container' ); ?> id="top">
		<?php wp_body_open(); ?>
		<a class="screen-reader-shortcut" href="#main-content">Skip to main content</a>
		
		<!-- Header -->
		<header class="header general-header">
			<div class="header__top">
				<!-- Logo image-->
				<a class="header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' home' ); ?>">
					<?php
					// If logo uploaded to customizer display it, if not show nothing
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					if ( has_custom_logo() ) {
						echo wp_get_attachment_image(
							$custom_logo_id,
							'full',
							false,
							array(
								'class'     => 'header__icon',
								'alt'       => get_bloginfo( 'name' ),
								'draggable' => 'false',
								'loading'   => 'eager',
								'decoding'  => 'async',
							)
						);
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
				<h1 class="header__heading">
					<?php
					if ( is_front_page() ) {
						$header_title = get_bloginfo( 'description' );
					} elseif ( is_404() ) {
						$header_title = __( '404 Error', 'projectroadmaptta.com' );
					} elseif ( is_search() ) {
						$header_title = sprintf( __( 'Search results for %s', 'projectroadmaptta.com' ), get_search_query() );
					} elseif ( is_archive() ) {
						$header_title = get_the_archive_title();
					} elseif ( is_home() ) {
						$header_title = single_post_title( '', false );
					} else {
						$header_title = get_the_title();
					}

					echo esc_html( wp_strip_all_tags( $header_title ) );
					?>
				</h1>

				<div class="header__description">
					<?php
						if (is_front_page()) {
							the_content();
						} else if (is_page( 'resources' )) {
							esc_html_e( 'Tools, guides, and downloadable resources for ECM task forces.', 'projectroadmaptta.com' );
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
							<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
							</div>
					<?php endif;
				
			
				$link = get_field('header_link_2');
				if( $link && is_front_page()): 
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
						</div>
				<?php endif;

				$link = get_field('header_link_3');
				if( $link && is_front_page()): 
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
						</div>
				<?php endif;


				$link = get_field('header_link_4');
				if( $link && is_front_page()): 
						$link_url = $link['url'];
						$link_title = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<div class="header__cta--wrapper"><a class="header__cta underline" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a><?php echo svg_icon('header__arrow', 'angle-right');?>
						</div>
				<?php endif;
				?>
			</div>
		</header><!-- .Header -->
