<?php
/**
 * Analytics consent banner.
 *
 * @package project-roadmap
 */

$privacy_policy_url = get_privacy_policy_url();
$consent_cookie_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
$analytics_measurement_id = projectroadmap_analytics_measurement_id();

if ( empty( $consent_cookie_path ) ) {
  $consent_cookie_path = '/';
}
?>

<section
  class="consent-banner"
  role="dialog"
  aria-labelledby="consent-banner-title"
  aria-describedby="consent-banner-description"
  aria-hidden="true"
  hidden
  data-consent-banner
  data-consent-cookie-path="<?php echo esc_attr( $consent_cookie_path ); ?>"
  data-analytics-measurement-id="<?php echo esc_attr( $analytics_measurement_id ); ?>"
>
  <div class="consent-banner__inner">
    <button
      class="consent-banner__dismiss"
      type="button"
      aria-label="<?php esc_attr_e( 'Close and decline analytics', 'projectroadmaptta.com' ); ?>"
      title="<?php esc_attr_e( 'Close and decline analytics', 'projectroadmaptta.com' ); ?>"
      data-consent-dismiss
    >
      <span aria-hidden="true">&#215;</span>
    </button>

    <div class="consent-banner__content">
      <p class="consent-banner__eyebrow"><?php esc_html_e( 'Privacy choices', 'projectroadmaptta.com' ); ?></p>
      <h2 class="consent-banner__title" id="consent-banner-title" tabindex="-1"><?php esc_html_e( 'Help us improve Project Roadmap', 'projectroadmaptta.com' ); ?></h2>
      <p class="consent-banner__description" id="consent-banner-description">
        <?php esc_html_e( 'We use optional analytics to understand how people use this site. Analytics remain off unless you select Accept.', 'projectroadmaptta.com' ); ?>
        <?php if ( $privacy_policy_url ) : ?>
          <a class="consent-banner__link" href="<?php echo esc_url( $privacy_policy_url ); ?>"><?php esc_html_e( 'Read our privacy policy', 'projectroadmaptta.com' ); ?></a>
        <?php endif; ?>
      </p>
    </div>

    <div class="consent-banner__actions" aria-label="<?php esc_attr_e( 'Analytics consent choices', 'projectroadmaptta.com' ); ?>">
      <button class="consent-banner__button consent-banner__button--accept" type="button" data-consent-accept>
        <?php esc_html_e( 'Accept', 'projectroadmaptta.com' ); ?>
      </button>
    </div>
  </div>
</section>
