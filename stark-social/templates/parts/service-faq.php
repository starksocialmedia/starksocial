<?php
/**
 * FAQ accordion — native <details>, no JS. Emits FAQPage JSON-LD
 * (SEO-STRATEGY schema plan); disable with the stark_faq_schema filter.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! $args['faq'] ) return;
?>
<section class="stark-band stark-faq" aria-labelledby="stark-faq-title">
    <div class="stark-home__inner stark-faq__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow">FAQ</p>
            <h2 id="stark-faq-title" class="stark-section-head__title"><?php echo esc_html( $args['faq_heading'] ); ?></h2>
            <?php if ( ! empty( $args['faq_intro'] ) ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $args['faq_intro'] ); ?></p><?php endif; ?>
        </header>
        <div class="stark-faq__list">
            <?php foreach ( $args['faq'] as $item ) : ?>
                <details class="stark-faq__item">
                    <summary class="stark-faq__q"><?php echo esc_html( $item['question'] ); ?></summary>
                    <div class="stark-faq__a"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
if ( apply_filters( 'stark_faq_schema', true ) ) {
    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map( function ( $item ) {
            return array(
                '@type'          => 'Question',
                'name'           => wp_strip_all_tags( $item['question'] ),
                'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $item['answer'] ) ),
            );
        }, $args['faq'] ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
