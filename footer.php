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
            <a class="footer__email">projectroadmap@icf.com</a>
            <div class="footer__social">
                <?php
                $facebook_link = get_theme_mod('facebook_link', '');
                $twitter_link = get_theme_mod('twitter_link', '');
                $website_link = get_theme_mod('website_link', '');

                if (!empty($facebook_link)) {
                    echo '<a href="' . esc_url($facebook_link) . '" class="footer__social-link" title="Go to our Facebook page">';
                    echo svg_icon('footer__icon', 'facebook');
                    echo '</a>';
                }

                if (!empty($twitter_link)) {
                    echo '<a href="' . esc_url($twitter_link) . '" class="footer__social-link" title="Go to our Twitter page">';
                    echo svg_icon('footer__icon', 'twitter');
                    echo '</a>';
                }

                if (!empty($website_link)) {
                    echo '<a href="' . esc_url($website_link) . '" class="footer__social-link" title="Go to our website">';
                    echo svg_icon('footer__icon', 'web');
                    echo '</a>';
                }
                ?>
            </div>
        </div>
        <div class="footer__section">
            <ul class="footer__list">
                <li class="footer__item"><a class="footer__link" href="#about" title="Learn more about us">About</a></li>
                <li class="footer__item"><a class="footer__link" href="#task-force" title="Learn more about our Task Force">Task Force</a></li>
                <li class="footer__item"><a class="footer__link" href="#discipline" title="Learn more about desciplines">Discipline</a></li>
                <li class="footer__item"><a class="footer__link" href="#field-at-large" title="Learn more about the field at large">Field At-Large</a></li>
                <li class="footer__item"><a class="footer__link" href="#team" title="Learn more about our staff">Staff</a></li>
                <li class="footer__item"><a class="footer__link" href="#resources" title="Go to our Resources section">Resources</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( site_url( '/admin' ) ); ?>" title="Go to the Admin page">Admin</a></li>
            </ul>
        </div>
        <div class="footer__section">
            <p class="footer__award-info"><?php echo get_theme_mod('footer_disclaimer_text', ''); ?></p>
        </div>  
    </footer>
    <a class="backtop" href="#top"><span class="sr-only">Back Top</span> <?php echo svg_icon('backtop__icon', 'angle-up');?></a>
    <?php wp_footer(); ?>
</body>
</html>
