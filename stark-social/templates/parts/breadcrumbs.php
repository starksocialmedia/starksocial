<?php
/**
 * Visible breadcrumbs (v27.3.6) — Rank Math's trail (Home / … / Page), placed
 * directly under the hero. Rank Math also outputs the BreadcrumbList schema.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! function_exists( 'rank_math_get_breadcrumbs' ) || is_front_page() ) return;
$trail = rank_math_get_breadcrumbs();
if ( ! $trail ) return;
?>
<div class="stark-breadcrumbs">
    <div class="stark-home__inner"><?php echo $trail; // phpcs:ignore WordPress.Security.EscapeOutput -- Rank Math markup ?></div>
</div>
