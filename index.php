<?php
/**
 * Fallback template for posts, archives, and other WordPress content.
 *
 * @package project-roadmap
 */

get_header();
?>
<main id="main-content" class="content-page">
  <div class="content-page__inner">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : ?>
        <?php the_post(); ?>
        <article <?php post_class( 'content-page__article' ); ?>>
          <?php if ( ! is_singular() ) : ?>
            <h2 class="content-page__title">
              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
          <?php endif; ?>

          <div class="content-page__content">
            <?php if ( is_singular() ) : ?>
              <?php the_content(); ?>
            <?php else : ?>
              <?php the_excerpt(); ?>
            <?php endif; ?>
          </div>

          <?php if ( is_singular() && 'post' === get_post_type() ) : ?>
            <?php $resource_destination = projectroadmap_resource_destination( get_the_ID() ); ?>
            <?php if ( ! empty( $resource_destination['url'] ) ) : ?>
              <a class="content-page__action" href="<?php echo esc_url( $resource_destination['url'] ); ?>" target="_blank" rel="noopener">
                <?php echo $resource_destination['isVideo'] ? esc_html__( 'Watch this resource', 'projectroadmaptta.com' ) : esc_html__( 'View this resource', 'projectroadmaptta.com' ); ?>
              </a>
            <?php endif; ?>
          <?php endif; ?>

          <?php if ( 'post' === get_post_type() ) : ?>
            <footer class="content-page__meta">
              <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
              <?php the_tags( '<span class="content-page__topics">Topics: ', ', ', '</span>' ); ?>
            </footer>
          <?php endif; ?>
        </article>
      <?php endwhile; ?>

      <?php the_posts_pagination(); ?>
    <?php else : ?>
      <section class="content-page__empty" aria-labelledby="content-empty-title">
        <h2 id="content-empty-title"><?php esc_html_e( 'Nothing found', 'projectroadmaptta.com' ); ?></h2>
        <p><?php esc_html_e( 'The requested content is not available.', 'projectroadmaptta.com' ); ?></p>
      </section>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
