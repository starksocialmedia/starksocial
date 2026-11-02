<?php
/**
 * Stark password generator (v27.0.18) — /support/password-generator/
 * Hero → tool card ([password_generator], passgen/) with tips → FAQ.
 * Copy from the live page.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$hero = array(
    'eyebrow'       => 'Free tool',
    'title'         => 'Generate secure passwords. Instantly.',
    'thesis'        => 'Create strong, random passwords designed to protect your accounts, without storing, tracking or logging anything.',
    'cta_primary'   => null,
    'cta_secondary' => null,
);

$tips = array(
    'Use 16–24 characters for most accounts to balance strength and usability.',
    'Enable symbols and numbers to significantly increase entropy.',
    'Save your password in a trusted password manager (recommended).',
    'Runs locally in your browser. Nothing is stored or sent.',
);

$faq = array(
    'faq_heading' => 'Frequently asked questions',
    'faq'         => array(
        array( 'question' => 'What is a password generator?', 'answer' => 'A password generator is a web tool that creates unique and random passwords based on security recommendations. Our tool lets you customize settings to meet your specific needs.' ),
        array( 'question' => 'Is this generator safe to use?', 'answer' => 'Yes. Passwords are generated locally on your computer and are not stored or shared. Don\'t leave the page unattended after creating and copying your password.' ),
        array( 'question' => 'What are the requirements for a strong password?', 'answer' => 'A strong password should be as random as possible, longer than eight characters, and include a combination of upper and lower case letters, numbers and symbols.' ),
        array( 'question' => 'Can a strong password be hacked?', 'answer' => 'No password is entirely uncrackable, but you can improve security by avoiding common patterns and never reusing passwords.' ),
        array( 'question' => 'Do I need a unique password for every account?', 'answer' => 'Yes. Using the same password across multiple accounts is risky. If one account is compromised, all of them could be at risk.' ),
        array( 'question' => 'How long should my password be?', 'answer' => 'At least 12 characters. The longer the password, the better.' ),
        array( 'question' => 'What passwords should not be used?', 'answer' => 'Avoid previously used passwords, passwords identical to your username, personal information, dictionary words, common phrases, popular passwords like "password", "123456" or "qwerty", and anything shorter than 12 characters.' ),
    ),
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

<main id="main" class="stark-page stark-passgen-page">
    <?php get_template_part( 'templates/parts/service', 'hero', $hero ); ?>

    <section class="stark-passgen-page__tool" aria-labelledby="stark-passgen-title">
        <div class="stark-home__inner stark-passgen-page__grid">
            <div class="stark-passgen-page__card">
                <h2 id="stark-passgen-title" class="stark-passgen-page__heading">Generate a strong password in seconds</h2>
                <?php echo do_shortcode( '[password_generator]' ); ?>
            </div>
            <aside class="stark-passgen-page__tips" aria-label="Password tips">
                <p class="stark-episode__aside-label">Good habits</p>
                <ul>
                    <?php foreach ( $tips as $tip ) : ?><li><?php echo esc_html( $tip ); ?></li><?php endforeach; ?>
                </ul>
                <p class="stark-passgen-page__more">More on this: <a href="/2025/11/15/why-random-passwords-matter-and-how-to-make-them-easy/">Why random passwords matter</a></p>
            </aside>
        </div>
    </section>

    <?php get_template_part( 'templates/parts/service', 'faq', $faq ); ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
