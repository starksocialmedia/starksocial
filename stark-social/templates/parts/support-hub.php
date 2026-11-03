<?php
/**
 * Support hub body (v27.5.2) — /support/ only; /support/knowledgebase/ keeps the full listing.
 * Order is by what people come here to do: quick actions → knowledgebase by category →
 * most-read articles (BetterDocs view counts) → still need help (chat / email / call) with
 * office hours. Contact details and hours come from templates/stark-support.php (Contact page).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$a = $args;

// Category → icon (stark_service_icon names).
$cat_icons = array(
    'getting-started'         => 'flag',
    'cybersecurity'           => 'shield',
    'paid-ads'                => 'target',
    'seo'                     => 'search',
    'social-media-management' => 'message',
    'web-design'              => 'layout',
);

$quick = array(
    array( 'label' => 'Open a ticket', 'desc' => 'Clients: goes straight to our team through the portal.', 'url' => $a['ticket_url'], 'icon' => 'ticket', 'external' => true ),
    array( 'label' => 'Browse the knowledgebase', 'desc' => sprintf( '%d how-to articles, from logins to reports.', $a['doc_total'] ), 'url' => $a['kb_url'], 'icon' => 'book' ),
    array( 'label' => 'Password generator', 'desc' => 'Strong, random passwords, made in your browser.', 'url' => home_url( '/support/password-generator/' ), 'icon' => 'key' ),
    array( 'label' => 'Contact the team', 'desc' => 'New project or question? Start here.', 'url' => home_url( '/contact/' ), 'icon' => 'mail' ),
);
?>
<nav class="stark-band stark-help-quick" aria-label="Quick actions">
    <div class="stark-home__inner">
        <ul class="stark-help-quick__list">
            <?php foreach ( $quick as $q ) : ?>
                <li>
                    <a class="stark-help-quick__card" href="<?php echo esc_url( $q['url'] ); ?>"<?php echo ! empty( $q['external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
                        <span class="stark-help-quick__icon" aria-hidden="true"><?php echo stark_service_icon( $q['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                        <strong><?php echo esc_html( $q['label'] ); ?><?php if ( ! empty( $q['external'] ) ) : ?><span class="screen-reader-text"> (opens the client portal in a new tab)</span><?php endif; ?></strong>
                        <span><?php echo esc_html( $q['desc'] ); ?></span>
                        <span class="stark-help-quick__arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<?php if ( $a['groups'] ) : ?>
<section class="stark-band stark-help-kb" aria-labelledby="stark-help-kb-title">
    <div class="stark-home__inner">
        <div class="stark-help__head">
            <p class="stark-eyebrow">Knowledgebase</p>
            <h2 id="stark-help-kb-title" class="stark-section-head__title">Find the answer yourself.</h2>
        </div>
        <div class="stark-support__search stark-help-kb__search">
            <?php if ( shortcode_exists( 'betterdocs_search_form' ) ) : ?>
                <?php echo do_shortcode( '[betterdocs_search_form placeholder="Search the knowledgebase…"]' ); // phpcs:ignore WordPress.Security.EscapeOutput -- plugin markup ?>
            <?php else : ?>
                <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="stark-support__form">
                    <label for="stark-help-q" class="screen-reader-text">Search the knowledgebase</label>
                    <input id="stark-help-q" type="search" name="s" placeholder="Search the knowledgebase…">
                    <input type="hidden" name="type" value="docs">
                    <button type="submit" class="stark-btn stark-btn--primary">Search</button>
                </form>
            <?php endif; ?>
        </div>
        <ul class="stark-help-cats">
            <?php foreach ( $a['groups'] as $g ) : $t = $g['term']; ?>
                <li class="stark-help-cat">
                    <span class="stark-help-cat__icon" aria-hidden="true"><?php echo stark_service_icon( $cat_icons[ $t->slug ] ?? 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                    <h3 class="stark-help-cat__title"><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></h3>
                    <p class="stark-help-cat__count"><?php echo esc_html( sprintf( _n( '%d article', '%d articles', $t->count, 'stark-social' ), $t->count ) ); ?></p>
                    <ul class="stark-help-cat__docs">
                        <?php foreach ( array_slice( $g['docs'], 0, 3 ) as $doc ) : ?>
                            <li><a href="<?php echo esc_url( get_permalink( $doc ) ); ?>"><?php echo esc_html( get_the_title( $doc ) ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ( $t->count > 3 ) : ?>
                        <a class="stark-help-cat__all" href="<?php echo esc_url( get_term_link( $t ) ); ?>">All <?php echo (int) $t->count; ?><span class="screen-reader-text"> <?php echo esc_html( $t->name ); ?> articles</span> <span aria-hidden="true">&rarr;</span></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<?php endif; ?>

<?php if ( $a['popular'] ) : ?>
<section class="stark-band stark-help-popular" aria-labelledby="stark-help-popular-title">
    <div class="stark-home__inner stark-help-popular__inner">
        <div class="stark-help__head">
            <p class="stark-eyebrow">Most read</p>
            <h2 id="stark-help-popular-title" class="stark-section-head__title">What clients look up most.</h2>
        </div>
        <ol class="stark-help-popular__list">
            <?php foreach ( $a['popular'] as $doc ) :
                $cats = get_the_terms( $doc, 'doc_category' ); ?>
                <li>
                    <a href="<?php echo esc_url( get_permalink( $doc ) ); ?>">
                        <span class="stark-help-popular__title"><?php echo esc_html( get_the_title( $doc ) ); ?></span>
                        <?php if ( $cats && ! is_wp_error( $cats ) ) : ?><span class="stark-help-popular__cat"><?php echo esc_html( $cats[0]->name ); ?></span><?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>
<?php endif; ?>

<section class="stark-band stark-help-contact" aria-labelledby="stark-help-contact-title">
    <div class="stark-home__inner">
        <div class="stark-help__head stark-help-contact__head">
            <div>
                <p class="stark-eyebrow stark-eyebrow--light">Still need help?</p>
                <h2 id="stark-help-contact-title" class="stark-section-head__title">Talk to a person.</h2>
            </div>
            <div class="stark-help-hours">
                <p class="stark-help-hours__label"><?php echo stark_service_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?> Office hours <span>(Pacific)</span></p>
                <ul>
                    <?php foreach ( $a['hours'] as $row ) : ?>
                        <li><span><?php echo esc_html( $row['days'] ); ?></span> <strong><?php echo esc_html( $row['time'] ?? '' ); ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <ul class="stark-help-ways">
            <li class="stark-help-way stark-help-way--chat" data-stark-chat="<?php echo $a['chat'] ? 'on' : 'off'; ?>">
                <span class="stark-help-way__icon" aria-hidden="true"><?php echo stark_service_icon( 'message' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                <h3 class="stark-help-way__title">Chat</h3>
                <p class="stark-help-way__on">Use the chat button in the corner of the page. We answer during office hours.</p>
                <p class="stark-help-way__off">Live chat isn't available right now. Email us instead and we'll get back to you within one business day.</p>
                <a class="stark-help-way__link stark-help-way__off" href="mailto:<?php echo esc_attr( antispambot( $a['email'] ) ); ?>">Email us <span aria-hidden="true">&rarr;</span></a>
            </li>
            <li class="stark-help-way">
                <span class="stark-help-way__icon" aria-hidden="true"><?php echo stark_service_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                <h3 class="stark-help-way__title">Email</h3>
                <p>Best for anything with details, screenshots or files. Replies within one business day.</p>
                <a class="stark-help-way__link" href="mailto:<?php echo esc_attr( antispambot( $a['email'] ) ); ?>"><?php echo esc_html( antispambot( $a['email'] ) ); ?></a>
            </li>
            <li class="stark-help-way">
                <span class="stark-help-way__icon" aria-hidden="true"><?php echo stark_service_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static markup ?></span>
                <h3 class="stark-help-way__title">Call</h3>
                <p>For anything urgent during office hours.</p>
                <a class="stark-help-way__link" href="tel:<?php echo esc_attr( $a['tel'] ); ?>"><?php echo esc_html( $a['phone'] ); ?></a>
            </li>
        </ul>
        <p class="stark-help-contact__more">
            <a href="<?php echo esc_url( $a['portal_url'] ); ?>" target="_blank" rel="noopener">Client portal<span class="screen-reader-text"> (opens in a new tab)</span></a>
            <a href="https://client.starksocial.com/login" target="_blank" rel="noopener">Reporting dashboard<span class="screen-reader-text"> (opens in a new tab)</span></a>
            <a href="<?php echo esc_url( home_url( '/support/accessibility-statement/' ) ); ?>">Accessibility statement</a>
        </p>
    </div>
</section>
