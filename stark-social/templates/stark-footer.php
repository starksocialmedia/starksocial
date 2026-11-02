<?php
/**
 * Stark footer template partial
 * Rendered via wp_footer action in functions.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$year = date( 'Y' );
?>
<footer class="stark-footer" role="contentinfo">
    <div class="stark-footer__inner">

        <div class="stark-footer__top">

            <div class="stark-footer__brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="stark-footer__logo" aria-label="Stark Social Media Agency Home">
                    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/stark-logo-light.svg' ); ?>" alt="Stark Social Media Agency" width="220" height="48">
                </a>
                <p class="stark-footer__tagline">Digital marketing the right way, out of Santa Clarita, CA.</p>

                <div class="stark-footer__social" aria-label="Social media">
                    <a href="https://www.instagram.com/starksocial" aria-label="Instagram" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.248 2.228.413.56.217.96.477 1.382.896.419.42.679.819.896 1.38.164.422.36 1.057.413 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.248 1.805-.413 2.227-.217.56-.477.96-.896 1.382-.42.419-.819.679-1.38.896-.422.164-1.057.36-2.227.413-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.248-2.227-.413a3.72 3.72 0 01-1.382-.896 3.72 3.72 0 01-.896-1.38c-.164-.422-.36-1.057-.413-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.248-1.805.413-2.228.217-.56.477-.96.896-1.382.42-.419.819-.679 1.38-.896.422-.164 1.057-.36 2.227-.413 1.266-.057 1.646-.07 4.85-.07M12 0C8.741 0 8.332.014 7.052.072 5.775.13 4.902.333 4.14.63a5.58 5.58 0 00-2.021 1.317A5.58 5.58 0 00.63 3.966C.333 4.73.131 5.6.072 6.88.014 8.158 0 8.566 0 12s.014 3.842.072 5.122c.059 1.277.26 2.15.558 2.912.306.788.717 1.459 1.388 2.13a5.58 5.58 0 002.021 1.317c.76.3 1.633.5 2.912.56C8.332 23.986 8.741 24 12 24s3.668-.014 4.948-.072c1.277-.059 2.15-.26 2.912-.558a5.58 5.58 0 002.021-1.317 5.58 5.58 0 001.317-2.021c.3-.763.5-1.635.56-2.912.057-1.28.072-1.688.072-4.948s-.014-3.667-.072-4.947c-.06-1.278-.26-2.15-.558-2.912a5.58 5.58 0 00-1.317-2.021A5.58 5.58 0 0019.86.63c-.763-.297-1.635-.5-2.912-.558C15.667.014 15.258 0 12 0zm0 5.838a6.162 6.162 0 100 12.325 6.162 6.162 0 000-12.325zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" fill="currentColor"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/company/stark-social-media-agency" aria-label="LinkedIn" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="currentColor"/></svg>
                    </a>
                    <a href="https://www.facebook.com/starksocial" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="currentColor"/></svg>
                    </a>
                </div>
            </div>

            <div class="stark-footer__cols">

                <div class="stark-footer__col">
                    <h3 class="stark-footer__heading">Services</h3>
                    <ul class="stark-footer__list">
                        <li><a href="/social-media-management/">Social Media Management</a></li>
                        <li><a href="/paid-advertising/">Paid Advertising</a></li>
                        <li><a href="/web-design/">Web Design</a></li>
                        <li><a href="/seo/">SEO</a></li>
                        <li><a href="/content-creation/">Content Creation</a></li>
                        <li><a href="/brand-strategy-identity/">Brand Strategy</a></li>
                        <li><a href="/audit-consulting/">Audit &amp; Consulting</a></li>
                    </ul>
                </div>

                <div class="stark-footer__col">
                    <h3 class="stark-footer__heading">Company</h3>
                    <ul class="stark-footer__list">
                        <li><a href="/about/">About</a></li>
                        <li><a href="/work/">Work</a></li>
                        <li><a href="/service-areas/">Service Areas</a></li>
                        <li><a href="/blog/">Blog</a></li>
                        <li><a href="/podcast/">Podcast</a></li>
                        <li><a href="/contact/">Contact</a></li>
                    </ul>
                </div>

                <div class="stark-footer__col">
                    <h3 class="stark-footer__heading">Support</h3>
                    <ul class="stark-footer__list">
                        <li><a href="/support/">Support Hub</a></li>
                        <li><a href="/support/knowledgebase/">Knowledgebase</a></li>
                        <li><a href="/password-generator/">Password Generator</a></li>
                        <li><a href="/support/accessibility-statement/">Accessibility</a></li>
                        <li><a href="https://hub.starksocial.com/clients/" target="_blank" rel="noopener">Client Portal &uarr;</a></li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="stark-footer__bottom">
            <div class="stark-footer__meta">
                <p class="stark-footer__copyright">&copy; <?php echo esc_html( $year ); ?> Stark Social Media Agency LLC. All rights reserved.</p>
                <p class="stark-footer__location">Santa Clarita, CA  &middot;  Serving Southern California</p>
            </div>
            <ul class="stark-footer__legal">
                <li><a href="/privacy-policy/">Privacy Policy</a></li>
                <li><a href="/terms-of-service/">Terms of Service</a></li>
                <li><a href="/cookie-policy/">Cookie Policy</a></li>
            </ul>
        </div>

    </div>
</footer>
