<?php
/** 
 * Custom Functions
 *
 * ! What the custom functions do:
 * *    1. Enqueues all styles and scripts
 * *    2. Asynchronously load scripts for speed optimization
 * *    3. Activates the ability to add custom logo in customizer    
 * *    4. Enable support for custom sized Post Thumbnails on posts and pages
 * *    5. Add site link to logo on login screen
 * *    6. Make css styles available to login screen
 * *    7. Replace WP logo with site title name on login screen
 * *    8. Add theme title to login screen
 * *    9.  Display inline svg icon from sprite sheet with custom class
 * *    10. Display Home BG image
 * *    11. Display site navigation
 * *    12. Display mobile navigation
 * *    13.  Enable custom post types
 * *    14. Change dashboard Posts to Resources
 */

// * * --------| Actions and filters in order |-------- *

  // Action to enque styles and scripts
  add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
  add_action( 'after_setup_theme', 'prm_custom_logo_setup' );
  add_action('init', 'custom_post_types');
  add_action('login_enqueue_scripts', 'projectroadmap_login_css');
  add_action( 'init', 'cp_change_post_object' );

  // Asynchronously load scripts
  add_filter( 'clean_url', 'async_scripts', 11, 1 ); 
  add_filter('login_headerurl', 'ourHeaderUrl');
  add_filter('login_headertitle', 'projectroadmap_login_title');
  

// * * --------| Functions in order |-------- *

  //* 1. Enqueuing styles and scripts
  function theme_enqueue_scripts() {
  wp_enqueue_script( 'Bundled_js', get_template_directory_uri() . '/assets/js/scripts-bundled.js#asyncload', array(), '1.0.0', true );
  wp_enqueue_style('projectroadmap_main_styles', get_stylesheet_uri());
  }

  //* 2. Asynchronously load scripts
  function async_scripts($url){
  if ( strpos( $url, '#asyncload') === false )
    return $url;
  else if ( is_admin() )
    return str_replace( '#asyncload', '', $url );
  else
    return str_replace( '#asyncload', '', $url )."' async='async";
  }

   //* 3. Activates the ability to add custom logo in customizer
function prm_custom_logo_setup() {
  $defaults = array(
      'height'      => 38,
      'width'       => 38,
      'flex-height' => true,
      'flex-width'  => true,
      'header-text' => array( 'Project Roadmap', 'Be curious, unlearn, evolve.' ),
  );
  add_theme_support( 'custom-logo', $defaults );

    //* 4. Enable support for custom sized Post Thumbnails on posts and pages
    add_image_size( 'my-thumbnail', 300, 169, false);
    add_image_size( 'x-small', 450, 253, false);
    add_image_size( 'small', 600, 338, false);
    add_image_size( 'medium', 768, 432, false);
    add_image_size( 'regular', 1024, 576, false);
    add_image_size( 'large', 1200, 675, false);
    add_image_size( 'med-large', 1600, 901, false);
    add_image_size( 'x-large', 2000, 1125, false);
    add_image_size( 'xx-large', 3000, 1688, false);
    add_image_size( 'full-size', 3200, 1801, false);
    add_image_size( 'staff-headshot', 350, 350, true);
    add_image_size('pageBanner', 1300, 700, true);
  }
  add_theme_support( 'post-thumbnails' );
  // .Activate the ability to add custom logo in customizer
  // .Enable support for Post Thumbnails on posts and pages

  //* 5. Add site link to logo on login screen
  function ourHeaderUrl() {
    return esc_url(site_url('/'));
  }

  // .Add site link to logo on login screen

  //* 6. Make css styles available to login screen
  function projectroadmap_login_css() {
    wp_enqueue_style('projectroadmap_main_styles', get_stylesheet_uri());
    }
  // .Make css styles available to login screen

  //* 7. Replace WP logo with site title name on login screen
  function projectroadmap_login_title() {
    return get_bloginfo('name');
  }
  // .Replace WP logo with site title name on login screen

  //* 8. Add theme title to login screen
  function ourLoginTitle() {
    return get_bloginfo('name');
  }
  add_filter('login_headertitle', 'ourLoginTitle');
  // .Add theme title to login screen

  //* 9.  Display inline svg icon from sprite sheet with custom class
  function svg_icon($class, $icon) { ?>
  <svg class="<?php echo $class ?>" aria-hidden="true">
    <use
      xlink:href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/sprite.svg' ); ?>#icon-<?php echo $icon ?>">
    </use>
  </svg>
  <?php } 
  // .Display inline svg icon from sprite sheet with custom class

  //* 10. Display Home BG image
  function bg_video() { ?>
    <div class="bg-video">
      <picture class="bg-video__content">
        <source srcset="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/backgrounds/header-bg.jpg"> 
      </picture>
    </div>
    <?php }
    //. Display Home bg image

   //* 11. Display site navigation
  function site_navigation() { ?>
  <!-- navigation -->
  <div class="navigation">
        <nav class="navigation__nav" aria-controls="primary-navigation">
      <ul class="navigation__list">
                <li class="navigation__item">
          <a href="<?php echo esc_url( '#about' ); ?>" class="navigation__link" id="about-link" title="Go to the About section">About</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url(  '#team' ); ?>" class="navigation__link" id="team-link" title="Go to the Team section">Team</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url( '#resources' ); ?>" class="navigation__link" id="resources-link" title="Go to the Resources section">Resources</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url( '#contact' ); ?>" class="navigation__link" id="contact-link" title="Go to the Contact section">Contact</a>
        </li>
      </ul>
    </nav>
  </div><!-- .navigation -->
  <?php }
  // .Display site navigation

   //* 12. Display mobile navigation
  function mobile_navigation() { ?>
  <!-- Mobile navigation -->
  <div class="mobile-navigation">
    <!-- Hidden menu label for accessibility-->
    <span hidden id="mobile-menu">Main menu</span>
    <button class="mobile-navigation__menu"  aria-controls="mobile-navigation" tabindex="0" aria-expanded="false" aria-labelledby="mobile-menu">
      <!-- navigation menu icon-->
      <i class="mobile-navigation__icon" alt="Menu icon" aria-hidden="true">&nbsp;</i>
    </button>
      <nav class="mobile-navigation__nav" aria-label="Mobile menu" aria-labelledby="mobile-menu" aria-hidden="true">
        <ul class="mobile-navigation__list">
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( site_url( '#about' ) ); ?>" class="mobile-navigation__link" title="Go to the About section">About</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( '#team' ); ?>" class="mobile-navigation__link" title="Go to the Team section">Team</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( '#resources'  ); ?>" class="mobile-navigation__link" title="Go to the Resources section">Resources</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url(  '#contact' ); ?>" class="mobile-navigation__link" title="Go to the Contact section">Contact</a>
          </li>
        </ul>
      </nav>
  </div><!-- .Moblie navigation -->
  <?php }
  // .Display mobile site navigation

   //* 13.  Enable custom post types
  function custom_post_types() {
  // Staff Post Type
  register_post_type('staff', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail'),
    'rewrite' => array('slug' => 'staff'),
    'taxonomies'  => array( 'category' ),
    'public' => true,
    'labels' => array(
      'name' => 'Staff',
      'add_new_item' => 'Add New Staff',
      'edit_item' => 'Edit Staff',
      'all_items' => 'All Staff',
      'singular_name' => 'Staff'
    ),
    'menu_icon' => 'dashicons-admin-users'
  ));
  }
    
//  .Enable custom post types

//* 14. Change dashboard Posts to Resources
function cp_change_post_object() {
  $get_post_type = get_post_type_object('post');
  $labels = $get_post_type->labels;
      $labels->name = 'Resources';
      $labels->singular_name = 'Resource';
      $labels->add_new = 'Add Resource';
      $labels->add_new_item = 'Add Resource';
      $labels->edit_item = 'Edit Resource';
      $labels->new_item = 'Resources';
      $labels->view_item = 'View Resources';
      $labels->search_items = 'Search Resources';
      $labels->not_found = 'No Resources found';
      $labels->not_found_in_trash = 'No Resources found in Trash';
      $labels->all_items = 'All Resources';
      $labels->menu_name = 'Resources';
      $labels->name_admin_bar = 'Resources';
}
