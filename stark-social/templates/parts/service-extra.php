<?php
/**
 * Extra card sections (v27.3.0) — e.g. "First 30 days", "What we need from you".
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( empty( $args['extra_sections'] ) ) return;
foreach ( $args['extra_sections'] as $i => $x ) :
    $id = 'stark-extra-' . ( $i + 1 ); ?>
    <section class="stark-band stark-extra" aria-labelledby="<?php echo esc_attr( $id ); ?>">
        <div class="stark-home__inner">
            <header class="stark-section-head">
                <?php if ( ! empty( $x['eyebrow'] ) ) : ?><p class="stark-eyebrow"><?php echo esc_html( $x['eyebrow'] ); ?></p><?php endif; ?>
                <h2 id="<?php echo esc_attr( $id ); ?>" class="stark-section-head__title"><?php echo esc_html( $x['heading'] ); ?></h2>
                <?php if ( ! empty( $x['intro'] ) ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $x['intro'] ); ?></p><?php endif; ?>
            </header>
            <?php if ( $x['items'] ) : ?>
                <ul class="stark-cards stark-cards--<?php echo esc_attr( min( 4, count( $x['items'] ) ) ); ?>">
                    <?php foreach ( $x['items'] as $item ) : ?>
                        <li class="stark-cards__item">
                            <h3 class="stark-cards__title"><?php echo esc_html( $item['title'] ); ?></h3>
                            <?php if ( ! empty( $item['description'] ) ) : ?><p class="stark-cards__desc"><?php echo esc_html( $item['description'] ); ?></p><?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ( ! empty( $x['note'] ) ) : ?><p class="stark-section-note"><?php echo esc_html( $x['note'] ); ?></p><?php endif; ?>
        </div>
    </section>
<?php endforeach;
