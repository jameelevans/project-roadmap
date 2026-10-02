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
  add_action( 'init', 'projectroadmap_remove_frontend_bloat' );
  add_action( 'template_redirect', 'projectroadmap_render_crawler_files', -100 );
  // Analytics is paused; keep plugin-generated tracking out of frontend HTML.
  add_action( 'wp', 'projectroadmap_disable_monsterinsights_frontend' );


  // Asynchronously load scripts
  add_filter( 'script_loader_tag', 'async_scripts', 10, 3 );
  add_filter('login_headerurl', 'ourHeaderUrl');
  add_filter('login_headertitle', 'projectroadmap_login_title');
  add_filter( 'manage_post_posts_columns', 'projectroadmap_resource_admin_columns' );
  add_filter( 'acf/validate_value/name=youtube_video_url', 'projectroadmap_validate_youtube_url', 10, 4 );
  add_filter( 'acf/update_value/name=youtube_video_url', 'projectroadmap_update_youtube_url', 10, 3 );
  add_filter( 'acf/update_value/name=resource_display_topic_1', 'projectroadmap_assign_card_topic', 10, 3 );
  add_filter( 'acf/update_value/name=resource_display_topic_2', 'projectroadmap_assign_card_topic', 10, 3 );
  add_filter( 'acf/update_value/name=resource_display_topic_3', 'projectroadmap_assign_card_topic', 10, 3 );
  add_filter( 'image_editor_output_format', 'projectroadmap_webp_upload_formats' );
  add_filter( 'wp_editor_set_quality', 'projectroadmap_webp_quality', 10, 2 );
  add_filter( 'wp_resource_hints', 'projectroadmap_filter_resource_hints', PHP_INT_MAX, 2 );
  add_filter( 'wp_robots', 'projectroadmap_environment_robots' );
  add_filter( 'wpseo_robots', 'projectroadmap_yoast_environment_robots' );
  add_filter( 'wpseo_canonical', 'projectroadmap_yoast_environment_canonical' );
  add_filter( 'wpseo_metadesc', 'projectroadmap_yoast_meta_description' );
  add_filter( 'wpseo_schema_webpage_type', 'projectroadmap_yoast_webpage_type' );
  add_filter( 'wpseo_schema_organization', 'projectroadmap_yoast_organization_schema' );
  add_filter( 'wpseo_add_opengraph_images', 'projectroadmap_add_social_share_image' );
  add_filter( 'wpseo_opengraph_image', 'projectroadmap_social_share_image' );
  add_filter( 'wpseo_twitter_image', 'projectroadmap_social_share_image' );
  add_filter( 'monsterinsights_track_user', '__return_false', PHP_INT_MAX );
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

  //* Detect local and staging installs so search engines never index duplicates.
  function projectroadmap_is_nonproduction_environment() {
    $home_parts = wp_parse_url( home_url( '/' ) );
    $host = isset( $home_parts['host'] ) ? strtolower( $home_parts['host'] ) : '';
    $path = isset( $home_parts['path'] ) ? strtolower( $home_parts['path'] ) : '';

    return 'production' !== wp_get_environment_type()
      || '.local' === substr( $host, -6 )
      || false !== strpos( $path, '/staging/' );
  }

  //* Return the URL WordPress is actually serving, bypassing stale cloned SEO data.
  function projectroadmap_current_canonical_url() {
    if ( is_404() ) {
      return false;
    }

    if ( is_singular() ) {
      return get_permalink( get_queried_object_id() );
    }

    if ( is_front_page() ) {
      return home_url( '/' );
    }

    global $wp;
    $request_path = isset( $wp->request ) ? trailingslashit( $wp->request ) : '';

    return home_url( '/' . ltrim( $request_path, '/' ) );
  }

  //* Keep staging/local pages out of indexes while preserving normal production rules.
  function projectroadmap_environment_robots( $robots ) {
    if ( ! projectroadmap_is_nonproduction_environment() ) {
      return $robots;
    }

    unset( $robots['index'], $robots['follow'] );
    $robots['noindex'] = true;
    $robots['nofollow'] = true;

    return $robots;
  }

  //* Apply the same environment protection when Yoast owns the robots tag.
  function projectroadmap_yoast_environment_robots( $robots ) {
    return projectroadmap_is_nonproduction_environment() ? 'noindex, nofollow' : $robots;
  }

  //* Use a self-referencing staging canonical instead of a cloned production URL.
  function projectroadmap_yoast_environment_canonical( $canonical ) {
    if ( ! projectroadmap_is_nonproduction_environment() ) {
      return $canonical;
    }

    return projectroadmap_current_canonical_url();
  }

  //* Describe the Resources landing page accurately in Yoast's schema graph.
  function projectroadmap_yoast_webpage_type( $type ) {
    return is_page( 'resources' ) ? 'CollectionPage' : $type;
  }

  //* Add stable organization details that match the visible footer content.
  function projectroadmap_yoast_organization_schema( $data ) {
    $data['email'] = 'projectroadmap@icf.com';
    $data['contactPoint'] = array(
      '@type'       => 'ContactPoint',
      'email'       => 'projectroadmap@icf.com',
      'contactType' => 'technical assistance',
    );

    $social_profiles = array_filter(
      array(
        get_theme_mod( 'facebook_link', '' ),
        get_theme_mod( 'twitter_link', '' ),
        get_theme_mod( 'website_link', '' ),
      )
    );

    if ( ! empty( $social_profiles ) ) {
      $data['sameAs'] = array_values( array_map( 'esc_url_raw', $social_profiles ) );
    }

    return $data;
  }

  //* Supply a share image when editors have not chosen one in Yoast.
  function projectroadmap_social_share_image( $image_url ) {
    if ( ! empty( $image_url ) ) {
      return $image_url;
    }

    $file_name = is_page( 'resources' ) ? 'resources-hero.webp' : 'header-bg.webp';

    return get_stylesheet_directory_uri() . '/assets/img/backgrounds/' . $file_name;
  }

  //* Add the theme fallback when Yoast has no editor-selected Open Graph image.
  function projectroadmap_add_social_share_image( $image_container ) {
    $image_container->add_image_by_url( projectroadmap_social_share_image( '' ) );

    return $image_container;
  }

  //* Prefer Yoast's sitemap index when available, with WordPress core as fallback.
  function projectroadmap_sitemap_url() {
    return defined( 'WPSEO_VERSION' ) ? home_url( '/sitemap_index.xml' ) : home_url( '/wp-sitemap.xml' );
  }

  //* Serve crawl directives and an AI-readable site summary from subdirectory installs.
  function projectroadmap_render_crawler_files() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $request_path = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
    $home_path = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
    $robots_path = $home_path . '/robots.txt';
    $llms_path = $home_path . '/llms.txt';

    if ( $request_path !== $robots_path && $request_path !== $llms_path ) {
      return;
    }

    status_header( 200 );
    nocache_headers();
    header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );

    if ( $request_path === $robots_path ) {
      if ( projectroadmap_is_nonproduction_environment() ) {
        echo "User-agent: *\nDisallow: /\n";
        exit;
      }

      echo "User-agent: *\n";
      echo "Disallow: /wp-admin/\n";
      echo "Allow: /wp-admin/admin-ajax.php\n\n";
      echo "User-agent: OAI-SearchBot\nAllow: /\n\n";
      echo "User-agent: ChatGPT-User\nAllow: /\n\n";
      echo "User-agent: GPTBot\nAllow: /\n\n";
      echo 'Sitemap: ' . esc_url_raw( projectroadmap_sitemap_url() ) . "\n";
      exit;
    }

    echo '# ' . wp_strip_all_tags( get_bloginfo( 'name' ) ) . "\n\n";
    echo '> Project Roadmap provides training, technical assistance, tools, and resources for OVC Enhanced Collaborative Model task forces.' . "\n\n";
    echo "## Primary pages\n\n";
    echo '- [Home](' . esc_url_raw( home_url( '/' ) ) . '): Program overview, strategy, team, and contact information.' . "\n";
    echo '- [Resources](' . esc_url_raw( projectroadmap_resources_url() ) . '): Searchable tools, guides, checklists, case spotlights, and Fireside Chat videos.' . "\n";
    echo '- [XML sitemap](' . esc_url_raw( projectroadmap_sitemap_url() ) . '): Complete index of public content.' . "\n\n";
    echo "## Contact\n\nprojectroadmap@icf.com\n";
    exit;
  }

  //* Generate WebP derivatives for uploaded JPEGs while retaining original files.
  function projectroadmap_webp_upload_formats( $formats ) {
    $formats['image/jpeg'] = 'image/webp';

    return $formats;
  }

  //* Balance WebP detail and transfer size for photos generated by WordPress.
  function projectroadmap_webp_quality( $quality, $mime_type ) {
    return 'image/webp' === $mime_type ? 82 : $quality;
  }

  //* Render a responsive theme image with WebP sources and a JPEG fallback.
  function projectroadmap_theme_picture( $image_name, $alt_text ) {
    $image_name = sanitize_file_name( $image_name );
    $base_uri = get_stylesheet_directory_uri() . '/assets/img/backgrounds/';
    $webp_srcset = sprintf(
      '%1$s 480w, %2$s 768w, %3$s 1000w',
      $base_uri . $image_name . '-480.webp',
      $base_uri . $image_name . '-768.webp',
      $base_uri . $image_name . '.webp'
    );
    ?>
    <picture class="about__picture">
      <source type="image/webp" srcset="<?php echo esc_attr( $webp_srcset ); ?>" sizes="(max-width: 800px) 100vw, 50vw">
      <img src="<?php echo esc_url( $base_uri . $image_name . '.jpg' ); ?>" alt="<?php echo esc_attr( $alt_text ); ?>" width="1000" height="667" loading="lazy" decoding="async">
    </picture>
    <?php
  }

  //* Retain the configured GA4 ID helper for a future consent reactivation.
  function projectroadmap_analytics_measurement_id() {
    if ( ! function_exists( 'monsterinsights_get_v4_id' ) ) {
      return '';
    }

    $measurement_id = strtoupper( (string) monsterinsights_get_v4_id() );

    return preg_match( '/^G-[A-Z0-9]+$/', $measurement_id ) ? $measurement_id : '';
  }

  //* Keep analytics out of cacheable HTML while tracking is paused.
  function projectroadmap_disable_monsterinsights_frontend() {
    remove_action( 'wp_head', 'monsterinsights_tracking_script', 6 );
  }

  //* Prevent paused analytics hosts from being contacted by resource hints.
  function projectroadmap_filter_resource_hints( $urls, $relation_type ) {
    if ( ! in_array( $relation_type, array( 'dns-prefetch', 'preconnect' ), true ) ) {
      return $urls;
    }

    return array_values(
      array_filter(
        $urls,
        function ( $url ) {
          $href = is_array( $url ) && isset( $url['href'] ) ? $url['href'] : $url;

          return false === strpos( (string) $href, 'googletagmanager.com' )
            && false === strpos( (string) $href, 'google-analytics.com' );
        }
      )
    );
  }

  //* Remove small legacy frontend payloads that this theme does not use.
  function projectroadmap_remove_frontend_bloat() {
    if ( is_admin() ) {
      return;
    }

    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
    add_filter( 'emoji_svg_url', '__return_false' );
  }

  //* Accept YouTube and YouTube No-Cookie URLs for Fireside Chat resources.
  function projectroadmap_sanitize_youtube_url( $url ) {
    $url = esc_url_raw( trim( (string) $url ) );

    if ( empty( $url ) ) {
      return '';
    }

    $host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
    $scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
    $is_web_url = in_array( $scheme, array( 'http', 'https' ), true );
    $is_youtube_url = 'youtu.be' === $host || (bool) preg_match( '/(^|\.)youtube(?:-nocookie)?\.com$/', $host );

    return $is_web_url && $is_youtube_url ? $url : '';
  }

  //* Give editors a useful ACF error instead of silently saving another host.
  function projectroadmap_validate_youtube_url( $valid, $value, $field, $input ) {
    if ( true !== $valid || empty( $value ) ) {
      return $valid;
    }

    return projectroadmap_sanitize_youtube_url( $value )
      ? $valid
      : __( 'Enter a valid YouTube or YouTube No-Cookie URL.', 'projectroadmaptta.com' );
  }

  //* Store a normalized URL after ACF validation succeeds.
  function projectroadmap_update_youtube_url( $value, $post_id, $field ) {
    return projectroadmap_sanitize_youtube_url( $value );
  }

  //* Resource type category slugs used by cards and filters.
  function projectroadmap_resource_type_slugs() {
    return array( 'tool', 'tools-checklists', 'guide', 'quick-guide', 'window', 'fireside-chat', 'fireside-chats' );
  }

  //* Resolve a Resource post's public file or video destination.
  function projectroadmap_resource_destination( $post_id ) {
    $category_slugs = wp_list_pluck( get_the_category( $post_id ), 'slug' );
    $youtube_value = function_exists( 'get_field' )
      ? get_field( 'youtube_video_url', $post_id )
      : get_post_meta( $post_id, 'youtube_video_url', true );
    $youtube_url = projectroadmap_sanitize_youtube_url( $youtube_value );
    $is_fireside_chat = ! empty( array_intersect( array( 'fireside-chat', 'fireside-chats' ), $category_slugs ) );

    if ( $is_fireside_chat && $youtube_url ) {
      return array( 'url' => $youtube_url, 'isVideo' => true );
    }

    $pdf = function_exists( 'get_field' ) ? get_field( 'download_pdf', $post_id ) : '';
    $pdf_url = '';

    if ( is_array( $pdf ) && ! empty( $pdf['url'] ) ) {
      $pdf_url = $pdf['url'];
    } elseif ( is_numeric( $pdf ) ) {
      $pdf_url = wp_get_attachment_url( $pdf );
    } elseif ( is_string( $pdf ) ) {
      $pdf_url = $pdf;
    }

    return array( 'url' => esc_url_raw( $pdf_url ), 'isVideo' => false );
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
      'fireside-chats'   => 'resource-fireside-chat.svg',
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
      'fireside-chats'   => array( 'singular' => __( 'Fireside Chat', 'projectroadmaptta.com' ), 'plural' => __( 'Fireside Chats', 'projectroadmaptta.com' ) ),
    );

    if ( isset( $labels[ $type_slug ] ) ) {
      return $plural ? $labels[ $type_slug ]['plural'] : $labels[ $type_slug ]['singular'];
    }

    return $fallback;
  }

  //* Build a concise page-specific description for SEO and social metadata.
  function projectroadmap_default_meta_description() {
    if ( is_front_page() ) {
      $description = __( 'Project Roadmap provides training, technical assistance, tools, and resources for OVC Enhanced Collaborative Model task forces.', 'projectroadmaptta.com' );
    } elseif ( is_page( 'resources' ) ) {
      $description = __( 'Search and download Project Roadmap tools, guides, templates, and resources for OVC Enhanced Collaborative Model task forces.', 'projectroadmaptta.com' );
    } elseif ( is_singular() && has_excerpt() ) {
      $description = get_the_excerpt();
    } else {
      $description = get_bloginfo( 'description' );
    }

    return wp_strip_all_tags( $description );
  }

  //* Fill blank Yoast descriptions without replacing editor-written metadata.
  function projectroadmap_yoast_meta_description( $description ) {
    return ! empty( $description ) ? $description : projectroadmap_default_meta_description();
  }

  //* Add metadata directly only when no SEO plugin owns the document head.
  function projectroadmap_meta_description() {
    if (
      is_admin()
      || defined( 'WPSEO_VERSION' )
      || defined( 'RANK_MATH_VERSION' )
      || defined( 'AIOSEO_VERSION' )
    ) {
      return;
    }

    $description = projectroadmap_default_meta_description();

    if ( $description ) {
      printf( "\n<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
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
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'responsive-embeds' );
  add_theme_support(
    'html5',
    array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
  );

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
    add_image_size( 'staff-headshot-small', 200, 200, true );
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
          <a href="<?php echo esc_url( projectroadmap_resources_url() ); ?>" class="navigation__link" id="resources-nav-link" title="Go to the Resources page">Resources</a>
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
            <a href="<?php echo esc_url( projectroadmap_resources_url() ); ?>" class="mobile-navigation__link" title="Go to the Resources page">Resources</a>
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

//* A card-topic choice also assigns the Topic without replacing other tags.
function projectroadmap_assign_card_topic( $value, $post_id, $field ) {
  if ( is_string( $post_id ) && preg_match( '/^post_(\d+)$/', $post_id, $matches ) ) {
    $post_id = $matches[1];
  }
  $post_id = absint( $post_id );
  $term_id = absint( $value );

  if ( $post_id && 'post' === get_post_type( $post_id ) && $term_id && term_exists( $term_id, 'post_tag' ) ) {
    wp_set_post_terms( $post_id, array( $term_id ), 'post_tag', true );
  }

  return $value;
}

//* Honor editor-selected topic order; fill unused slots with other assigned Topics.
function projectroadmap_resource_display_topics( $post_id, $assigned_topics ) {
  $by_id = array();
  foreach ( $assigned_topics as $topic ) {
    $by_id[ $topic->term_id ] = $topic;
  }

  $display_topics = array();
  for ( $slot = 1; $slot <= 3; $slot++ ) {
    $term_id = absint( get_post_meta( $post_id, 'resource_display_topic_' . $slot, true ) );
    if ( $term_id && isset( $by_id[ $term_id ] ) ) {
      $display_topics[ $term_id ] = $by_id[ $term_id ];
    }
  }

  // Older Resources need no migration: their first three assigned Topics show.
  foreach ( $assigned_topics as $topic ) {
    if ( count( $display_topics ) >= 3 ) {
      break;
    }
    $display_topics[ $topic->term_id ] = $topic;
  }

  return array_values( $display_topics );
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
