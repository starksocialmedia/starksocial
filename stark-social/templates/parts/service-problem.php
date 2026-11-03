<?php
/**
 * The problem / the reality (v27.3.0, from the live pages). Copy left, symptoms right.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$pr = $args['problem'] ?? null;
if ( empty( $pr['heading'] ) ) return;
?>
<section class="stark-band stark-problem" aria-labelledby="stark-problem-title">
    <div class="stark-home__inner stark-problem__inner">
        <div class="stark-problem__copy">
            <p class="stark-eyebrow"><?php echo esc_html( $pr['eyebrow'] ); ?></p>
            <h2 id="stark-problem-title" class="stark-section-head__title"><?php echo esc_html( $pr['heading'] ); ?></h2>
            <?php foreach ( $pr['body'] as $i => $para ) : ?>
                <p class="<?php echo 0 === $i ? 'stark-problem__lead' : 'stark-problem__body'; ?>"><?php echo esc_html( $para ); ?></p>
            <?php endforeach; ?>
            <?php if ( $pr['note'] ) : ?><p class="stark-problem__note"><?php echo esc_html( $pr['note'] ); ?></p><?php endif; ?>
        </div>
        <?php if ( $pr['points'] ) : ?>
            <ul class="stark-problem__points">
                <?php foreach ( $pr['points'] as $point ) : ?><li><?php echo esc_html( $point ); ?></li><?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
