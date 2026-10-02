<?php
/**
 * * The template for displaying the about page
 *
 * @package your-wp-project
 */

 get_header('general');

?>
	<main id="main-content">
    <section class="error">
      <picture>
        <source type="image/webp" srcset="<?php echo esc_url( get_theme_file_uri( 'assets/img/404.webp' ) ); ?>">
        <img class="error__image" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/404.png' ) ); ?>" alt="Page not found" width="1412" height="1376" decoding="async">
      </picture>
      <h2 class="error__header">Page not found</h2>
      <p class="error__p">Sorry the page you requested can not be found. Please go back to the home page.</p> 
      <a class="error__home" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go Home</a>
    </section>
	</main>
<?php get_footer(); ?>
