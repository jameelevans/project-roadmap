<?php
/**
 * * The template for displaying the footer
 *
 * @package your-wp-project
 */

?>
    <!--Footer-->
    <footer class="footer">
        <div class="footer__section">
            <h2 class="footer__header">Contact us</h2>
            <a class="footer__email" href="mailto:projectroadmap@icf.com">projectroadmap@icf.com</a>
            <div class="footer__social">
                <?php
                $facebook_link = get_theme_mod('facebook_link', '');
                $twitter_link = get_theme_mod('twitter_link', '');
                $website_link = get_theme_mod('website_link', '');

                if (!empty($facebook_link)) {
                    echo '<a href="' . esc_url($facebook_link) . '" class="footer__social-link" aria-label="Visit our Facebook page">';
                    echo svg_icon('footer__icon', 'facebook');
                    echo '</a>';
                }

                if (!empty($twitter_link)) {
                    echo '<a href="' . esc_url($twitter_link) . '" class="footer__social-link" aria-label="Visit our Twitter page">';
                    echo svg_icon('footer__icon', 'twitter');
                    echo '</a>';
                }

                if (!empty($website_link)) {
                    echo '<a href="' . esc_url($website_link) . '" class="footer__social-link" aria-label="Visit the ICF website">';
                    echo svg_icon('footer__icon', 'web');
                    echo '</a>';
                }
                ?>
            </div>
        </div>
        <div class="footer__section">
          <nav aria-label="Footer navigation">
            <ul class="footer__list">
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( projectroadmap_section_url( 'about' ) ); ?>" title="Learn more about us">About</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( projectroadmap_resources_url() ); ?>" title="Go to the Resources page">Resources</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( projectroadmap_section_url( 'strategy' ) ); ?>" title="Learn about our strategy">Our Strategy</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( projectroadmap_section_url( 'team' ) ); ?>" title="Learn more about our team">Team</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( projectroadmap_section_url( 'contact' ) ); ?>" title="Contact Project Roadmap">Contact</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( site_url( '/admin' ) ); ?>" title="Go to the Admin page">Admin</a></li>
            </ul>
          </nav>
        </div>
        <div class="footer__section">
            <p class="footer__award-info">This website was produced by ICF under 2020-VT-BX-K003, awarded by the Office for Victims of Crime, Office of Justice Programs, U.S. Department of Justice. The opinions, findings, and conclusions or recommendations expressed in this website are those of the contributors and do not necessarily represent the official position or policies of the U.S. Department of Justice.</p>
        </div>  
    </footer>
    <a class="backtop" href="#top"><span class="sr-only">Back to top</span> <?php echo svg_icon('backtop__icon', 'angle-up');?></a>
    <?php wp_footer(); ?>
</body>
</html>
