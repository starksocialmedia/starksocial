<?php
/**
 * Stark support hub (v27.1.2) — /support/ and /support/knowledgebase/
 * Hero → knowledgebase search (BetterDocs live search) + articles grouped by
 * category | contact card + quick actions. Contact details mirror the Contact
 * page defaults; portal links match live. The knowledgebase page is the same
 * layout listing every article, with live's intro copy.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$is_kb = 'knowledgebase' === get_queried_object()->post_name;

$hero = array(
    'eyebrow'       => $is_kb ? 'Support' : 'Client support',
    'title'         => $is_kb ? 'Knowledgebase' : 'Support',
    // Knowledgebase intro is live's copy.
    'thesis'        => $is_kb ? 'Guides on how to work with us, share account access, understand your reports, and get the most out of your services. Can’t find what you’re looking for? Send us a message and we’ll get back to you within one business day.' : 'We’re here when you need us.',
    'cta_primary'   => null,
    'cta_secondary' => null,
);

$kb_page   = get_page_by_path( 'support/knowledgebase' );
$kb_url    = $kb_page ? get_permalink( $kb_page ) : home_url( '/support/knowledgebase/' );
$per_group = $is_kb ? -1 : 5;

$groups = array();
if ( taxonomy_exists( 'doc_category' ) ) {
    $terms = get_terms( array( 'taxonomy' => 'doc_category', 'hide_empty' => true, 'parent' => 0 ) );
    if ( ! is_wp_error( $terms ) ) {
        // BetterDocs order, with Getting Started pinned first.
        $rank = function ( $t ) {
            return 'getting-started' === $t->slug ? -1 : (int) get_term_meta( $t->term_id, 'doc_category_order', true );
        };
        usort( $terms, function ( $a, $b ) use ( $rank ) {
            return $rank( $a ) <=> $rank( $b );
        } );
        foreach ( $terms as $term ) {
            $groups[] = array(
                'term' => $term,
                'docs' => get_posts( array(
                    'post_type'   => 'docs',
                    'numberposts' => $per_group,
                    'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
                    'tax_query'   => array( array( 'taxonomy' => 'doc_category', 'terms' => $term->term_id ) ),
                ) ),
            );
        }
    }
}

// Same defaults as templates/stark-contact.php (from the live Contact page).
$contact_id = ( $contact = get_page_by_path( 'contact' ) ) ? $contact->ID : 0;
$phone = ( $contact_id ? stark_field( 'phone', $contact_id ) : '' ) ?: '(661) 513-3996';
$tel   = preg_replace( '/[^0-9+]/', '', $phone );
$tel   = 10 === strlen( $tel ) ? '+1' . $tel : $tel;
$email = ( $contact_id ? stark_field( 'email', $contact_id ) : '' ) ?: 'hello@starksocial.com';
$hours = array_values( array_filter( (array) ( $contact_id ? stark_field( 'hours', $contact_id ) : array() ), function ( $row ) {
    return ! empty( $row['days'] );
} ) ) ?: array(
    array( 'days' => 'Mon–Thu', 'time' => '9:00 AM – 5:00 PM' ),
    array( 'days' => 'Friday', 'time' => '9:00 AM – 4:00 PM' ),
);

$portal_url = apply_filters( 'stark_client_portal_url', 'https://hub.starksocial.com/clients/' );
$ticket_url = apply_filters( 'stark_open_ticket_url', 'https://hub.starksocial.com/clients/open_ticket' );

$actions = array(
    array( 'label' => 'Password Generator', 'desc' => 'Strong, random passwords, generated in your browser.', 'url' => home_url( '/support/password-generator/' ), 'icon' => 'shield' ),
    array( 'label' => 'Accessibility Statement', 'desc' => 'Our commitment and how to report a barrier.', 'url' => home_url( '/support/accessibility-statement/' ), 'icon' => 'users' ),
    array( 'label' => 'Client Portal', 'desc' => 'Projects, files, invoices and tickets.', 'url' => $portal_url, 'icon' => 'layout', 'external' => true ),
    array( 'label' => 'Client Dashboard', 'desc' => 'Reporting and performance insights.', 'url' => 'https://client.starksocial.com/login', 'icon' => 'chart', 'external' => true ),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<main id="main" class="stark-page stark-support">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>
    <?php get_template_part( 'templates/parts/breadcrumbs' ); ?>

    <section class="stark-band stark-support__band" aria-label="Knowledgebase and contact">
        <div class="stark-home__inner stark-support__grid">

            <div class="stark-support__main">
                <h2 class="stark-support__heading">Search the knowledgebase</h2>
                <div class="stark-support__search">
                    <?php if ( shortcode_exists( 'betterdocs_search_form' ) ) : ?>
                        <?php echo do_shortcode( '[betterdocs_search_form placeholder="Search articles…"]' ); // phpcs:ignore WordPress.Security.EscapeOutput -- plugin markup ?>
                    <?php else : ?>
                        <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="stark-support__form">
                            <label for="stark-support-q" class="screen-reader-text">Search articles</label>
                            <input id="stark-support-q" type="search" name="s" placeholder="Search articles…">
                            <input type="hidden" name="post_type" value="docs">
                            <button type="submit" class="stark-btn stark-btn--primary">Search</button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if ( $groups ) : ?>
                    <div class="stark-support__groups">
                        <?php foreach ( $groups as $group ) :
                            $term = $group['term'];
                            $more = $term->count > count( $group['docs'] ); ?>
                            <section class="stark-support__group" aria-labelledby="stark-kb-<?php echo esc_attr( $term->slug ); ?>">
                                <h3 id="stark-kb-<?php echo esc_attr( $term->slug ); ?>" class="stark-support__group-title">
                                    <a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
                                    <span class="stark-support__count"><?php echo esc_html( sprintf( _n( '%d article', '%d articles', $term->count, 'stark-social' ), $term->count ) ); ?></span>
                                </h3>
                                <ul class="stark-support__list">
                                    <?php foreach ( $group['docs'] as $doc ) : ?>
                                        <li><a href="<?php echo esc_url( get_permalink( $doc ) ); ?>"><?php echo esc_html( get_the_title( $doc ) ); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if ( $more ) : ?>
                                    <a class="stark-support__all" href="<?php echo esc_url( get_term_link( $term ) ); ?>">All <?php echo esc_html( $term->name ); ?> articles &rarr;</a>
                                <?php endif; ?>
                            </section>
                        <?php endforeach; ?>
                    </div>
                    <?php if ( ! $is_kb ) : ?>
                        <p class="stark-support__kb-link"><a class="stark-link-button" href="<?php echo esc_url( $kb_url ); ?>">Browse the full knowledgebase &rarr;</a></p>
                    <?php endif; ?>
                <?php else : ?>
                    <p class="stark-support__empty">Knowledgebase articles are on the way. In the meantime, <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">get in touch</a>.</p>
                <?php endif; ?>
            </div>

            <aside class="stark-support__side" aria-label="Contact and quick actions">
                <div class="stark-support__card">
                    <h2 class="stark-support__card-title">Talk to us</h2>
                    <dl class="stark-support__details">
                        <div>
                            <dt>Phone</dt>
                            <dd><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $phone ); ?></a></dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></dd>
                        </div>
                        <div>
                            <dt>Office hours</dt>
                            <dd>
                                <ul class="stark-contact__hours">
                                    <?php foreach ( $hours as $row ) : ?>
                                        <li><span><?php echo esc_html( $row['days'] ); ?></span> <span><?php echo esc_html( $row['time'] ?? '' ); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            </dd>
                        </div>
                    </dl>
                    <a class="stark-btn stark-btn--primary stark-support__ticket" href="<?php echo esc_url( $ticket_url ); ?>" target="_blank" rel="noopener">Open a ticket<span class="screen-reader-text"> (opens the client portal in a new tab)</span></a>
                    <p class="stark-support__note">Existing clients: tickets go straight to our team through the client portal.</p>
                </div>

                <nav class="stark-support__actions" aria-label="Quick actions">
                    <h2 class="stark-support__card-title">Quick actions</h2>
                    <ul>
                        <?php foreach ( $actions as $action ) : ?>
                            <li>
                                <a href="<?php echo esc_url( $action['url'] ); ?>"<?php echo ! empty( $action['external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
                                    <span class="stark-support__action-icon" aria-hidden="true"><?php echo function_exists( 'stark_service_icon' ) ? stark_service_icon( $action['icon'] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                                    <span class="stark-support__action-text">
                                        <strong><?php echo esc_html( $action['label'] ); ?></strong>
                                        <span><?php echo esc_html( $action['desc'] ); ?></span>
                                    </span>
                                    <?php if ( ! empty( $action['external'] ) ) : ?><span class="screen-reader-text"> (opens in a new tab)</span><?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </aside>

        </div>
    </section>
</main>

<?php wp_footer(); ?>
</body>
</html>
