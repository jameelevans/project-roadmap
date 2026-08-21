<?php
/**
 * Template for the Resources page.
 *
 * The cards are rendered from published WordPress posts that have a
 * `download_pdf` ACF value. JavaScript only filters and animates the markup
 * already printed by PHP, so the page stays useful without Ajax.
 *
 * @package project-roadmap
 */

get_header();

$resource_query = new WP_Query(
  array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => array(
      array(
        'key'     => 'download_pdf',
        'compare' => 'EXISTS',
      ),
    ),
  )
);

$resources = array();
$type_totals = array();
$topic_totals = array();
$resources_per_page = 9;
$featured_total = 0;
$resource_type_slugs = projectroadmap_resource_type_slugs();

if ( $resource_query->have_posts() ) {
  while ( $resource_query->have_posts() ) {
    $resource_query->the_post();

    $pdf = function_exists( 'get_field' ) ? get_field( 'download_pdf' ) : '';
    $pdf_url = '';

    if ( is_array( $pdf ) && ! empty( $pdf['url'] ) ) {
      $pdf_url = $pdf['url'];
    } elseif ( is_numeric( $pdf ) ) {
      $pdf_url = wp_get_attachment_url( $pdf );
    } elseif ( is_string( $pdf ) ) {
      $pdf_url = $pdf;
    }

    if ( empty( $pdf_url ) ) {
      continue;
    }

    $categories = get_the_category();
    $resource_categories = array_values(
      array_filter(
        $categories,
        function( $term ) {
          return 'uncategorized' !== $term->slug;
        }
      )
    );
    $type_terms = array_values(
      array_filter(
        $resource_categories,
        function( $term ) use ( $resource_type_slugs ) {
          return in_array( $term->slug, $resource_type_slugs, true );
        }
      )
    );

    // Preserve older resources whose type category predates the known type list.
    if ( empty( $type_terms ) ) {
      $type_terms = $resource_categories;
    }

    $primary_type = ! empty( $type_terms ) ? $type_terms[0] : null;
    $type_slug = $primary_type ? $primary_type->slug : 'resource';
    $type_name = projectroadmap_resource_type_label( $type_slug, $primary_type ? $primary_type->name : __( 'Resource', 'projectroadmaptta.com' ) );
    $tags = get_the_tags();
    $topic_pills = $tags ? $tags : array();
    $topics_by_slug = array();
    $is_featured = (bool) get_post_meta( get_the_ID(), '_projectroadmap_featured_resource', true );
    $search_parts = array( get_the_title(), get_the_excerpt(), $type_name );

    // Categories and tags are both searchable in the Topic dropdown.
    foreach ( array_merge( $resource_categories, $topic_pills ) as $topic ) {
      $topics_by_slug[ $topic->slug ] = $topic;
    }

    $topics = array_values( $topics_by_slug );

    if ( $is_featured ) {
      $featured_total++;
    }

    foreach ( $type_terms as $term ) {
      $type_totals[ $term->slug ] = array(
        'name'  => projectroadmap_resource_type_label( $term->slug, $term->name, true ),
        'count' => isset( $type_totals[ $term->slug ] ) ? $type_totals[ $term->slug ]['count'] + 1 : 1,
      );
      $search_parts[] = $term->name;
      $search_parts[] = projectroadmap_resource_type_label( $term->slug, $term->name );
    }

    foreach ( $topics as $topic ) {
      $topic_totals[ $topic->slug ] = array(
        'name'  => $topic->name,
        'count' => isset( $topic_totals[ $topic->slug ] ) ? $topic_totals[ $topic->slug ]['count'] + 1 : 1,
      );
      $search_parts[] = $topic->name;
    }

    $resources[] = array(
      'id'        => get_the_ID(),
      'title'     => get_the_title(),
      'excerpt'   => get_the_excerpt(),
      'url'       => $pdf_url,
      'date'      => get_the_date( 'Y-m-d' ),
      'dateLabel' => get_the_date( 'M Y' ),
      'type'      => $type_name,
      'typeSlug'  => $type_slug,
      'types'     => wp_list_pluck( $type_terms, 'slug' ),
      'topics'    => $topics,
      'topicPills'=> $topic_pills,
      'featured'  => $is_featured,
      'search'    => strtolower( implode( ' ', $search_parts ) ),
    );
  }

  wp_reset_postdata();
}

// Featured resources lead every initial result set; titles break ties.
usort(
  $resources,
  function( $resource_a, $resource_b ) {
    if ( $resource_a['featured'] !== $resource_b['featured'] ) {
      return $resource_a['featured'] ? -1 : 1;
    }

    return strcasecmp( $resource_a['title'], $resource_b['title'] );
  }
);

uasort(
  $type_totals,
  function( $a, $b ) {
    return strcasecmp( $a['name'], $b['name'] );
  }
);

uasort(
  $topic_totals,
  function( $a, $b ) {
    return strcasecmp( $a['name'], $b['name'] );
  }
);

$initial_visible_end = min( $resources_per_page, count( $resources ) );
$initial_count_label = count( $resources ) > 0
  ? sprintf(
    /* translators: 1: first visible resource number, 2: last visible resource number, 3: total resources, 4: resource/resource plural label */
    __( 'Showing %1$s&ndash;%2$s of %3$s %4$s', 'projectroadmaptta.com' ),
    number_format_i18n( 1 ),
    number_format_i18n( $initial_visible_end ),
    number_format_i18n( count( $resources ) ),
    esc_html( _n( 'resource', 'resources', count( $resources ), 'projectroadmaptta.com' ) )
  )
  : sprintf(
    esc_html( _n( '%s resource', '%s resources', count( $resources ), 'projectroadmaptta.com' ) ),
    esc_html( number_format_i18n( count( $resources ) ) )
  );
?>

<main id="main-content" class="resources-page" data-resources-page>
  <section class="resources-library" aria-labelledby="resources-library-title">
    <div class="resources-library__inner">
      <header class="resources-library__intro">
        <p class="resources-library__eyebrow"><?php esc_html_e( 'Resource Library', 'projectroadmaptta.com' ); ?></p>
        <h2 class="resources-library__title" id="resources-library-title"><?php esc_html_e( 'Tools for ECM task forces', 'projectroadmaptta.com' ); ?></h2>
        <p class="resources-library__summary"><?php esc_html_e( 'Search and filter Project Roadmap resources by title, resource type, and topic.', 'projectroadmaptta.com' ); ?></p>
      </header>

      <section class="resources-filter" aria-label="<?php esc_attr_e( 'Resource filters', 'projectroadmaptta.com' ); ?>">
        <div class="resources-filter__controls">
          <div class="resources-filter__control resources-filter__control--search">
            <label class="resources-filter__label" for="resources-search"><?php esc_html_e( 'Search resources', 'projectroadmaptta.com' ); ?></label>
            <div class="resources-filter__search-field">
              <svg class="resources-filter__search-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                <path d="M21 21l-6 -6"></path>
              </svg>
              <input class="resources-filter__input" id="resources-search" type="search" placeholder="<?php esc_attr_e( 'Search by title or keyword', 'projectroadmaptta.com' ); ?>" autocomplete="off" data-resource-search>
            </div>
          </div>

          <div class="resources-filter__control">
            <p class="resources-filter__label" id="resources-topic-label"><?php esc_html_e( 'Topic', 'projectroadmaptta.com' ); ?></p>
            <div class="resources-filter__dropdown" data-topic-dropdown>
              <button class="resources-filter__dropdown-button" type="button" aria-haspopup="true" aria-expanded="false" aria-labelledby="resources-topic-label resources-topic-button-label" data-topic-toggle>
                <span id="resources-topic-button-label" data-topic-label><?php esc_html_e( 'Filter by topic', 'projectroadmaptta.com' ); ?></span>
              </button>
              <div class="resources-filter__dropdown-panel" hidden role="dialog" aria-label="<?php esc_attr_e( 'Select topics', 'projectroadmaptta.com' ); ?>" data-topic-panel>
                <?php if ( ! empty( $topic_totals ) ) : ?>
                  <?php foreach ( $topic_totals as $slug => $topic ) : ?>
                    <label class="resources-filter__topic-option">
                      <input type="checkbox" value="<?php echo esc_attr( $slug ); ?>" data-topic-checkbox>
                      <span><?php echo esc_html( $topic['name'] ); ?></span>
                      <span class="resources-filter__count"><?php echo esc_html( $topic['count'] ); ?></span>
                    </label>
                  <?php endforeach; ?>
                <?php else : ?>
                  <p class="resources-filter__topic-empty"><?php esc_html_e( 'No topics available yet.', 'projectroadmaptta.com' ); ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="resources-filter__control">
            <label class="resources-filter__label" for="resources-sort"><?php esc_html_e( 'Sort by', 'projectroadmaptta.com' ); ?></label>
            <select class="resources-filter__select" id="resources-sort" data-resource-sort>
              <option value="az"><?php esc_html_e( 'A to Z', 'projectroadmaptta.com' ); ?></option>
              <option value="newest"><?php esc_html_e( 'Newest first', 'projectroadmaptta.com' ); ?></option>
              <option value="type"><?php esc_html_e( 'By type', 'projectroadmaptta.com' ); ?></option>
            </select>
          </div>
        </div>

        <div class="resources-filter__active-topics" data-topic-active></div>

        <div class="resources-filter__pills" role="group" aria-label="<?php esc_attr_e( 'Filter by resource type or featured status', 'projectroadmaptta.com' ); ?>" data-resource-type-filters>
          <button class="resources-filter__pill resources-filter__pill--active" type="button" data-filter-group="type" data-filter-value="all" aria-pressed="true">
            <?php esc_html_e( 'All', 'projectroadmaptta.com' ); ?>
            <span class="resources-filter__count"><?php echo esc_html( count( $resources ) ); ?></span>
          </button>

          <button class="resources-filter__pill" type="button" data-filter-group="type" data-filter-value="featured" aria-pressed="false">
            <?php esc_html_e( 'Featured Resources', 'projectroadmaptta.com' ); ?>
            <span class="resources-filter__count"><?php echo esc_html( $featured_total ); ?></span>
          </button>

          <?php if ( ! empty( $type_totals ) ) : ?>
            <?php foreach ( $type_totals as $slug => $type ) : ?>
              <button class="resources-filter__pill" type="button" data-filter-group="type" data-filter-value="<?php echo esc_attr( $slug ); ?>" aria-pressed="false">
                <?php echo esc_html( $type['name'] ); ?>
                <span class="resources-filter__count"><?php echo esc_html( $type['count'] ); ?></span>
              </button>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="resources-filter__meta">
          <p class="resources-filter__result-count" aria-live="polite" data-resource-count><?php echo wp_kses_post( $initial_count_label ); ?></p>
          <button class="resources-filter__clear" type="button" hidden data-resource-clear><?php esc_html_e( 'Clear filters', 'projectroadmaptta.com' ); ?></button>
        </div>
      </section>

      <div class="resources-library__grid" data-resource-grid>
        <?php foreach ( $resources as $resource ) : ?>
          <?php
          $topic_slugs = wp_list_pluck( $resource['topics'], 'slug' );
          $topic_names = wp_list_pluck( $resource['topicPills'], 'name' );
          ?>
          <article
            class="resource-card resource-card--<?php echo esc_attr( $resource['typeSlug'] ); ?><?php echo $resource['featured'] ? ' resource-card--featured' : ''; ?>"
            data-resource-card
            data-resource-title="<?php echo esc_attr( $resource['title'] ); ?>"
            data-resource-search="<?php echo esc_attr( $resource['search'] ); ?>"
            data-resource-types="<?php echo esc_attr( implode( ' ', $resource['types'] ) ); ?>"
            data-resource-topics="<?php echo esc_attr( implode( ' ', $topic_slugs ) ); ?>"
            data-resource-date="<?php echo esc_attr( $resource['date'] ); ?>"
            data-resource-featured="<?php echo $resource['featured'] ? 'true' : 'false'; ?>"
            data-resource-sort-title="<?php echo esc_attr( strtolower( $resource['title'] ) ); ?>"
            data-resource-sort-type="<?php echo esc_attr( $resource['typeSlug'] ); ?>"
          >
            <a class="resource-card__link" href="<?php echo esc_url( $resource['url'] ); ?>" target="_blank" rel="noopener" download>
              <?php if ( $resource['featured'] ) : ?>
                <span class="resource-card__featured"><?php esc_html_e( 'Featured Resource', 'projectroadmaptta.com' ); ?></span>
              <?php endif; ?>
              <span class="resource-card__icon" aria-hidden="true"><?php projectroadmap_resource_type_icon( $resource['typeSlug'] ); ?></span>
              <span class="resource-card__content">
                <span class="resource-card__type"><?php echo esc_html( $resource['type'] ); ?></span>
                <h3 class="resource-card__title"><?php echo esc_html( $resource['title'] ); ?></h3>
                <?php if ( ! empty( $topic_names ) ) : ?>
                  <span class="resource-card__topics" aria-label="<?php esc_attr_e( 'Resource topics', 'projectroadmaptta.com' ); ?>">
                    <?php foreach ( $topic_names as $topic_name ) : ?>
                      <span class="resource-card__topic-pill"><?php echo esc_html( $topic_name ); ?></span>
                    <?php endforeach; ?>
                  </span>
                <?php endif; ?>
              </span>
              <span class="resource-card__footer">
                <span class="resource-card__date"><?php echo esc_html( $resource['dateLabel'] ); ?></span>
                <span class="resource-card__action"><?php svg_icon( 'resource-card__download-icon', 'download' ); ?><?php esc_html_e( 'Download', 'projectroadmaptta.com' ); ?></span>
              </span>
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="resources-library__empty" hidden data-resource-empty>
        <h3 class="resources-library__empty-title"><?php esc_html_e( 'No matching resources', 'projectroadmaptta.com' ); ?></h3>
        <p class="resources-library__empty-text"><?php esc_html_e( 'Try another search term or remove a selected filter.', 'projectroadmaptta.com' ); ?></p>
        <button class="resources-library__empty-clear" type="button" data-resource-empty-clear><?php esc_html_e( 'Clear filters', 'projectroadmaptta.com' ); ?></button>
      </div>

      <nav class="resources-pagination" aria-label="<?php esc_attr_e( 'Resource pages', 'projectroadmaptta.com' ); ?>" hidden data-resource-pagination>
        <button class="resources-pagination__button" type="button" data-resource-prev>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6l6 6"></path></svg>
          <?php esc_html_e( 'Previous', 'projectroadmaptta.com' ); ?>
        </button>
        <span class="resources-pagination__status" data-resource-page-status><?php esc_html_e( 'Page 1 of 1', 'projectroadmaptta.com' ); ?></span>
        <button class="resources-pagination__button" type="button" data-resource-next>
          <?php esc_html_e( 'Next', 'projectroadmaptta.com' ); ?>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6l-6 6"></path></svg>
        </button>
      </nav>
    </div>
  </section>
</main>

<?php get_footer(); ?>
