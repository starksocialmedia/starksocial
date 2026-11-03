<?php
/**
 * BetterDocs page wrapper (v27.4.0): renders BetterDocs' own template and adds
 * the landmarks it lacks. See inc/stark-docs.php.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

ob_start();
include $GLOBALS['stark_docs_inner_template'];
echo stark_docs_landmarks( ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput -- BetterDocs' own escaped output.
