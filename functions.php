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
 * *    13. Enable custom post types
 * *    14. Change dashboard Posts to Resources
 * *    15. Add a section to the Customizer
 * *    16. Add Social Media Links to Customizer settings
 */ 

// * * --------| Actions and filters in order |-------- *

  // Action to enque styles and scripts
  add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
  add_action( 'after_setup_theme', 'prm_custom_logo_setup' );
  add_action('init', 'custom_post_types');
  add_action('login_enqueue_scripts', 'projectroadmap_login_css');
  add_action( 'init', 'cp_change_post_object' );
  add_action( 'init', 'projectroadmap_register_resource_meta' );
  add_action( 'add_meta_boxes_post', 'projectroadmap_add_resource_meta_box' );
  add_action( 'save_post_post', 'projectroadmap_save_resource_meta_box' );
  add_action('customize_register', 'custom_footer_disclaimer_customize_register');
  add_action('customize_register', 'customizer_social_settings');


  // Asynchronously load scripts
  add_filter( 'script_loader_tag', 'async_scripts', 10, 3 );
  add_filter('login_headerurl', 'ourHeaderUrl');
  add_filter('login_headertitle', 'projectroadmap_login_title');
  add_filter( 'manage_post_posts_columns', 'projectroadmap_resource_admin_columns' );
  add_action( 'manage_post_posts_custom_column', 'projectroadmap_resource_admin_column_content', 10, 2 );
  add_action( 'wp_head', 'projectroadmap_meta_description', 1 );
  

// * * --------| Functions in order |-------- *

  //* Build a URL for home page section links.
  function projectroadmap_section_url( $section ) {
    return is_front_page() ? '#' . $section : home_url( '/#' . $section );
  }

  //* Get the Resources page URL with a graceful fallback.
  function projectroadmap_resources_url() {
    $page = get_page_by_path( 'resources' );

    return $page ? get_permalink( $page ) : home_url( '/resources/' );
  }

  //* Resource type category slugs used by cards and filters.
  function projectroadmap_resource_type_slugs() {
    return array( 'tool', 'tools-checklists', 'guide', 'quick-guide', 'window', 'fireside-chat' );
  }

  //* Render the icon that matches a resource type.
  function projectroadmap_resource_type_icon( $type_slug ) {
    $icon_map = array(
      'tool'             => 'resource-tool.svg',
      'tools-checklists' => 'resource-tool.svg',
      'guide'            => 'resource-guide.svg',
      'quick-guide'      => 'resource-quick-guide.svg',
      'window'           => 'resource-window.svg',
      'fireside-chat'    => 'resource-fireside-chat.svg',
    );

    $file_name = isset( $icon_map[ $type_slug ] ) ? $icon_map[ $type_slug ] : 'resource-guide.svg';
    $file_path = get_stylesheet_directory() . '/assets/img/icons/' . $file_name;

    if ( file_exists( $file_path ) ) {
      echo file_get_contents( $file_path );
    }
  }

  //* Get the display label for a resource type in card or filter context.
  function projectroadmap_resource_type_label( $type_slug, $fallback = 'Resource', $plural = false ) {
    $labels = array(
      'tool'             => array( 'singular' => __( 'Tool/Checklist', 'projectroadmaptta.com' ), 'plural' => __( 'Tools/Checklists', 'projectroadmaptta.com' ) ),
      'tools-checklists' => array( 'singular' => __( 'Tool/Checklist', 'projectroadmaptta.com' ), 'plural' => __( 'Tools/Checklists', 'projectroadmaptta.com' ) ),
      'guide'            => array( 'singular' => __( 'Guide', 'projectroadmaptta.com' ), 'plural' => __( 'Guides', 'projectroadmaptta.com' ) ),
      'quick-guide'      => array( 'singular' => __( 'Quick Guide', 'projectroadmaptta.com' ), 'plural' => __( 'Quick Guides', 'projectroadmaptta.com' ) ),
      'window'           => array( 'singular' => __( 'Window', 'projectroadmaptta.com' ), 'plural' => __( 'Windows', 'projectroadmaptta.com' ) ),
      'fireside-chat'    => array( 'singular' => __( 'Fireside Chat', 'projectroadmaptta.com' ), 'plural' => __( 'Fireside Chats', 'projectroadmaptta.com' ) ),
    );

    if ( isset( $labels[ $type_slug ] ) ) {
      return $plural ? $labels[ $type_slug ]['plural'] : $labels[ $type_slug ]['singular'];
    }

    return $fallback;
  }

  //* Add a concise, page-specific description when no SEO plugin supplies one.
  function projectroadmap_meta_description() {
    if ( is_admin() ) {
      return;
    }

    if ( is_front_page() ) {
      $description = __( 'Project Roadmap provides training, technical assistance, tools, and resources for OVC Enhanced Collaborative Model task forces.', 'projectroadmaptta.com' );
    } elseif ( is_page( 'resources' ) ) {
      $description = __( 'Search and download Project Roadmap tools, guides, templates, and resources for OVC Enhanced Collaborative Model task forces.', 'projectroadmaptta.com' );
    } elseif ( is_singular() && has_excerpt() ) {
      $description = get_the_excerpt();
    } else {
      $description = get_bloginfo( 'description' );
    }

    if ( $description ) {
      printf( "\n<meta name=\"description\" content=\"%s\">\n", esc_attr( wp_strip_all_tags( $description ) ) );
    }
  }

  //* 1. Enqueuing styles and scripts
  function theme_enqueue_scripts() {
  $script_path = get_template_directory() . '/assets/js/scripts-bundled.js';
  $script_version = file_exists( $script_path ) ? filemtime( $script_path ) : '1.0.0';
  $style_path = get_stylesheet_directory() . '/style.css';
  $style_version = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0.0';

  wp_enqueue_script( 'Bundled_js', get_template_directory_uri() . '/assets/js/scripts-bundled.js', array(), $script_version, true );
  wp_enqueue_style( 'projectroadmap_main_styles', get_stylesheet_uri(), array(), $style_version );
  }

  //* 2. Defer scripts that rely on rendered page markup.
  function async_scripts($tag, $handle, $src){
  if ( 'Bundled_js' !== $handle || is_admin() ) {
    return $tag;
  }

  return '<script src="' . esc_url( $src ) . '" id="' . esc_attr( $handle ) . '-js" defer></script>' . "\n";
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
  add_theme_support( 'title-tag' );

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
  <svg class="<?php echo esc_attr( $class ); ?>" aria-hidden="true" focusable="false">
    <use href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/sprite.svg#icon-' . sanitize_key( $icon ) ); ?>"></use>
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
        <nav class="navigation__nav" aria-label="Primary navigation">
      <ul class="navigation__list">
        <li class="navigation__item">
          <a href="<?php echo esc_url( projectroadmap_section_url( 'about' ) ); ?>" class="navigation__link" id="about-link" title="Go to the About section">About</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url( projectroadmap_section_url( 'resources-link' ) ); ?>" class="navigation__link" id="resources-nav-link" title="Go to the Resources section">Resources</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url( projectroadmap_section_url( 'strategy' ) ); ?>" class="navigation__link" id="strategy-link" title="Go to the Our Strategy section">Our Strategy</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url( projectroadmap_section_url( 'team' ) ); ?>" class="navigation__link" id="team-link" title="Go to the Team section">Team</a>
        </li>
        <li class="navigation__item">
          <a href="<?php echo esc_url( projectroadmap_section_url( 'contact' ) ); ?>" class="navigation__link" id="contact-link" title="Go to the Contact section">Contact</a>
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
    <button class="mobile-navigation__menu" type="button" aria-controls="mobile-navigation" aria-expanded="false" aria-label="Open main menu">
      <!-- navigation menu icon-->
      <span class="mobile-navigation__icon" aria-hidden="true">&nbsp;</span>
    </button>
      <nav class="mobile-navigation__nav" id="mobile-navigation" aria-label="Mobile navigation" aria-hidden="true" inert>
        <ul class="mobile-navigation__list">
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( projectroadmap_section_url( 'about' ) ); ?>" class="mobile-navigation__link" title="Go to the About section">About</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( projectroadmap_section_url( 'resources-link' ) ); ?>" class="mobile-navigation__link" title="Go to the Resources section">Resources</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( projectroadmap_section_url( 'strategy' ) ); ?>" class="mobile-navigation__link" title="Go to the Our Strategy section">Our Strategy</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( projectroadmap_section_url( 'team' ) ); ?>" class="mobile-navigation__link" title="Go to the Team section">Team</a>
          </li>
          <li class="mobile-navigation__item">
            <a href="<?php echo esc_url( projectroadmap_section_url( 'contact' ) ); ?>" class="mobile-navigation__link" title="Go to the Contact section">Contact</a>
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

  if ( ! $get_post_type ) {
    return;
  }

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

  // Clarify how the built-in taxonomies are used by the resource library.
  $category = get_taxonomy( 'category' );
  $post_tag = get_taxonomy( 'post_tag' );

  if ( $category ) {
    $category->labels->name = 'Resource Types';
    $category->labels->singular_name = 'Resource Type';
    $category->labels->menu_name = 'Resource Types';
  }

  if ( $post_tag ) {
    $post_tag->labels->name = 'Topics';
    $post_tag->labels->singular_name = 'Topic';
    $post_tag->labels->menu_name = 'Topics';
  }
}

//* Register featured status for the REST-enabled Resource editor.
function projectroadmap_register_resource_meta() {
  register_post_meta(
    'post',
    '_projectroadmap_featured_resource',
    array(
      'type'              => 'boolean',
      'single'            => true,
      'default'           => false,
      'show_in_rest'      => true,
      'sanitize_callback' => 'rest_sanitize_boolean',
      'auth_callback'     => function() {
        return current_user_can( 'edit_posts' );
      },
    )
  );
}

//* Add the featured control to the Resource editor sidebar.
function projectroadmap_add_resource_meta_box() {
  add_meta_box(
    'projectroadmap-featured-resource',
    __( 'Resource Settings', 'projectroadmaptta.com' ),
    'projectroadmap_render_resource_meta_box',
    'post',
    'side',
    'high'
  );
}

function projectroadmap_render_resource_meta_box( $post ) {
  $is_featured = (bool) get_post_meta( $post->ID, '_projectroadmap_featured_resource', true );
  wp_nonce_field( 'projectroadmap_save_resource_settings', 'projectroadmap_resource_settings_nonce' );
  ?>
  <label for="projectroadmap-featured-resource-field">
    <input
      id="projectroadmap-featured-resource-field"
      name="projectroadmap_featured_resource"
      type="checkbox"
      value="1"
      <?php checked( $is_featured ); ?>
    >
    <?php esc_html_e( 'Feature this resource', 'projectroadmaptta.com' ); ?>
  </label>
  <p class="description"><?php esc_html_e( 'Featured resources appear first and receive a card badge.', 'projectroadmaptta.com' ); ?></p>
  <?php
}

//* Save the featured checkbox without affecting autosaves or revisions.
function projectroadmap_save_resource_meta_box( $post_id ) {
  if (
    ! isset( $_POST['projectroadmap_resource_settings_nonce'] ) ||
    ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['projectroadmap_resource_settings_nonce'] ) ), 'projectroadmap_save_resource_settings' ) ||
    ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
    ! current_user_can( 'edit_post', $post_id )
  ) {
    return;
  }

  update_post_meta( $post_id, '_projectroadmap_featured_resource', isset( $_POST['projectroadmap_featured_resource'] ) ? '1' : '0' );
}

//* Show featured status in the Resources admin list.
function projectroadmap_resource_admin_columns( $columns ) {
  $columns['projectroadmap_featured'] = __( 'Featured', 'projectroadmaptta.com' );
  return $columns;
}

function projectroadmap_resource_admin_column_content( $column, $post_id ) {
  if ( 'projectroadmap_featured' === $column && get_post_meta( $post_id, '_projectroadmap_featured_resource', true ) ) {
    esc_html_e( 'Yes', 'projectroadmaptta.com' );
  }
}

//* 15. Add a section to the Customizer
function custom_footer_disclaimer_customize_register($wp_customize) {
  $wp_customize->add_section('footer_disclaimer_section', array(
      'title' => 'Footer Disclaimer',
      'priority' => 200,
  ));

  // Add a setting for the footer disclaimer text
  $wp_customize->add_setting('footer_disclaimer_text', array(
      'default' => '',
      'type' => 'theme_mod',
  ));

  // Add a control to input the disclaimer text
  $wp_customize->add_control('footer_disclaimer_text', array(
      'label' => 'Footer Disclaimer Text',
      'section' => 'footer_disclaimer_section',
      'type' => 'textarea',
  ));
}
//* 16 Add Social Media Links to Customizer settings
function customizer_social_settings($wp_customize) {
  // Add a section for social media links
  $wp_customize->add_section('footer_social_section', array(
      'title' => 'Footer Social Links',
      'priority' => 201, // Adjust the priority as needed to control the display order
  ));

  // Add settings and controls for social media links
  $wp_customize->add_setting('facebook_link', array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw',
  ));

  $wp_customize->add_setting('twitter_link', array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw',
  ));

  $wp_customize->add_setting('website_link', array(
      'default' => '',
      'sanitize_callback' => 'esc_url_raw',
  ));

  $wp_customize->add_control('facebook_link', array(
      'label' => 'Facebook Link',
      'section' => 'footer_social_section',
      'type' => 'text',
  ));

  $wp_customize->add_control('twitter_link', array(
      'label' => 'Twitter Link',
      'section' => 'footer_social_section',
      'type' => 'text',
  ));

  $wp_customize->add_control('website_link', array(
      'label' => 'Custom Website Link',
      'section' => 'footer_social_section',
      'type' => 'text',
  ));
}
