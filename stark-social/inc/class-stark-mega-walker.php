<?php
/**
 * Stark Mega Menu Walker
 *
 * Renders the primary nav with:
 *   - Mega menu panel for items whose menu ACF/CSS class contains "mega-services"
 *   - Chevron indicator on items with children
 *   - Clean markup for CSS targeting
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Stark_Mega_Walker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            // Children of top-level: wrapped in mega panel
            $output .= '<div class="stark-mega__panel"><div class="stark-mega__inner"><ul class="stark-mega__grid">';
        } else {
            $output .= '<ul class="sub-menu">';
        }
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            // Close the mega panel + add the CTA card as 8th slot
            $output .= '
                <li class="stark-mega__cta">
                    <a href="/services/" class="stark-mega__cta-link">
                        <span class="stark-mega__cta-eyebrow">Not sure where to start?</span>
                        <span class="stark-mega__cta-title">See all services</span>
                        <span class="stark-mega__cta-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </li>
            </ul></div></div>';
        } else {
            $output .= '</ul>';
        }
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes, true );

        $li_classes = array( 'stark-nav__item' );
        if ( $depth === 0 && $has_children ) {
            $li_classes[] = 'stark-nav__item--has-mega';
        }
        if ( $depth === 1 ) {
            $li_classes[] = 'stark-mega__item';
        }

        $class_attr = implode( ' ', array_map( 'esc_attr', $li_classes ) );

        $output .= '<li class="' . $class_attr . '">';

        $title = apply_filters( 'the_title', $item->title, $item->ID );
        $url   = ! empty( $item->url ) ? esc_url( $item->url ) : '#';

        if ( $depth === 0 ) {
            $a_class = 'stark-nav__link';
            $extra   = '';
            if ( $has_children ) {
                $a_class .= ' stark-nav__link--has-mega';
                $extra   = ' aria-expanded="false" aria-haspopup="true"';
            }
            $output .= '<a href="' . $url . '" class="' . $a_class . '"' . $extra . '>';
            $output .= '<span>' . esc_html( $title ) . '</span>';
            if ( $has_children ) {
                $output .= '<svg class="stark-nav__chevron" width="10" height="6" viewBox="0 0 10 6" aria-hidden="true"><path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }
            $output .= '</a>';
        } else {
            // Mega menu item: title + optional description
            $description = ! empty( $item->description ) ? $item->description : '';
            $output .= '<a href="' . $url . '" class="stark-mega__link">';
            $output .= '<span class="stark-mega__title">' . esc_html( $title ) . '</span>';
            if ( $description ) {
                $output .= '<span class="stark-mega__desc">' . esc_html( $description ) . '</span>';
            }
            $output .= '</a>';
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}
