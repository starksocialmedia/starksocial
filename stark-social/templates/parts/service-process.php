<?php
/**
 * Our process — numbered 4-step flow.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! $args['process'] ) return;
?>
<section class="stark-band stark-process" aria-labelledby="stark-process-title">
    <div class="stark-home__inner">
        <header class="stark-section-head">
            <p class="stark-eyebrow"><?php echo esc_html( $args['process_eyebrow'] ?? 'Our process' ); ?></p>
            <h2 id="stark-process-title" class="stark-section-head__title"><?php echo esc_html( $args['process_heading'] ); ?></h2>
            <?php if ( ! empty( $args['process_intro'] ) ) : ?><p class="stark-section-head__intro"><?php echo esc_html( $args['process_intro'] ); ?></p><?php endif; ?>
        </header>
        <ol class="stark-process__steps">
            <?php foreach ( $args['process'] as $i => $step ) : ?>
                <li class="stark-process__step">
                    <span class="stark-process__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
                    <h3 class="stark-process__title"><?php echo esc_html( $step['title'] ); ?></h3>
                    <?php if ( ! empty( $step['description'] ) ) : ?>
                        <p class="stark-process__desc"><?php echo esc_html( $step['description'] ); ?></p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php if ( ! empty( $args['process_note'] ) ) : ?><p class="stark-section-note"><?php echo esc_html( $args['process_note'] ); ?></p><?php endif; ?>
    </div>
</section>
