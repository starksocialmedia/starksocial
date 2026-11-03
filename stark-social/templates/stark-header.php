<?php
/**
 * Stark header template partial
 * Rendered via wp_body_open action in functions.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$logo_url = get_stylesheet_directory_uri() . '/img/stark-logo.svg';
?>
<div class="stark-scroll-progress" aria-hidden="true"><div class="stark-scroll-progress__bar"></div></div>

<header class="stark-nav" role="banner" aria-label="Primary">
<?php
// Stark templates have <main id="main">; BetterDocs pages go through templates/stark-docs-wrap.php (v27.4.0).
global $template;
if ( $template && false !== strpos( $template, '/stark-social/templates/' ) ) : ?>
    <a class="stark-skip-link" href="<?php echo esc_attr( apply_filters( 'stark_skip_link_target', '#main' ) ); ?>">Skip to content</a>
<?php endif; ?>
    <div class="stark-nav__inner">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="stark-nav__logo" aria-label="Stark Social Media Agency Home">
            <img src="<?php echo esc_url( $logo_url ); ?>" alt="Stark Social Media Agency" width="220" height="48">
        </a>

        <nav class="stark-nav__primary" aria-label="Main navigation">
            <?php
            if ( has_nav_menu( 'stark-primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'stark-primary',
                    'container'      => false,
                    'menu_class'     => 'stark-nav__menu',
                    'walker'         => new Stark_Mega_Walker(),
                    'depth'          => 2,
                    'fallback_cb'    => false,
                ) );
            } else {
                // Fallback hard-coded nav if no menu assigned yet
                echo stark_fallback_primary_menu();
            }
            ?>
        </nav>

        <div class="stark-nav__utility">
            <a href="/support/" class="stark-nav__util-link">Support</a>
            <a href="https://hub.starksocial.com/clients/" class="stark-nav__util-link stark-nav__util-link--cta" target="_blank" rel="noopener">Client Portal</a>
        </div>

        <button type="button" class="stark-nav__burger" aria-label="Open menu" aria-expanded="false" aria-controls="stark-mobile-nav">
            <span class="stark-nav__burger-bars" aria-hidden="true">
                <span></span><span></span><span></span>
            </span>
        </button>
    </div>
</header>

<div class="stark-nav__scrim" aria-hidden="true"></div>

<div id="stark-mobile-nav" class="stark-mobile-nav" aria-hidden="true">
    <div class="stark-mobile-nav__inner">
        <?php
        if ( has_nav_menu( 'stark-primary' ) ) {
            wp_nav_menu( array(
                'theme_location' => 'stark-primary',
                'container'      => false,
                'menu_class'     => 'stark-mobile-nav__menu',
                'depth'          => 2,
                'fallback_cb'    => false,
            ) );
        } else {
            echo stark_fallback_mobile_menu();
        }
        ?>
        <div class="stark-mobile-nav__utility">
            <a href="/support/" class="stark-mobile-nav__util-link">Support</a>
            <a href="https://hub.starksocial.com/clients/" class="stark-mobile-nav__util-link" target="_blank" rel="noopener">Client Portal</a>
        </div>
    </div>
</div>
