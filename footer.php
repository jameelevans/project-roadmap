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
            <a href="#" class="footer__social-link" title="Go to our Fackbook page"><?php echo svg_icon('footer__icon', 'facebook');?></a>
            <a href="#" class="footer__social-link"  title="Go to our Twitter page"><?php echo svg_icon('footer__icon', 'twitter');?></a>
            <a href="#" class="footer__social-link" title="Go to our other web page"><?php echo svg_icon('footer__icon', 'web');?></a>
        </div>
            
            
        </div>
        <div class="footer__section">
            <ul class="footer__list">
                <li class="footer__item"><a class="footer__link" href="#about" title="Learn more about us">About</a></li>
                <li class="footer__item"><a class="footer__link" href="#task-force" title="Learn more about our Task Force">Task Force</a></li>
                <li class="footer__item"><a class="footer__link" href="#discipline" title="Learn more about desciplines">Decipline</a></li>
                <li class="footer__item"><a class="footer__link" href="#field-at-large" title="Learn more about the field at large">Field At-Large</a></li>
                <li class="footer__item"><a class="footer__link" href="#team" title="Learn more about our staff">Staff</a></li>
                <li class="footer__item"><a class="footer__link" href="#resources" title="Go to our Resources section">Resources</a></li>
                <li class="footer__item"><a class="footer__link" href="<?php echo esc_url( site_url( '/admin' ) ); ?>" title="Go to the Admin page">Admin</a></li>
            </ul>
        </div>

        

        <div class="footer__section">
            <p class="footer__award-info">This website is supported by ICF under 2020-VT-BX-K003, awarded by the Office for Victims of Crime, Office of Justice Programs, U.S. Department of Justice. The opinions, findings, and conclusions or recommendations expressed in this website are those of the contributors and do not necessarily represent the official position or policies of the U.S. Department of Justice.</p>
            
        </div>
       
        

                

               
    
       
      
        
    </footer>
    <a class="backtop" href="#top"><span class="sr-only">Back Top</span> <?php echo svg_icon('backtop__icon', 'angle-up');?></a>
  


    <?php wp_footer(); ?>
</body>
</html>
