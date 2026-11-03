<?php
// Portfolio "Market" field (v27.4.9): only where the case study itself names the place.
// Valencia Jazz & Blues: "…Santa Clarita every summer". Pathway Escrow and Schlickart
// state no location; Nathan sets theirs in the editor if they're local.
$p = get_page_by_path( 'valencia-jazz-blues', OBJECT, 'portfolio' );
if ( $p ) update_field( 'field_stark_work_location', 'santa-clarita', $p->ID );
WP_CLI::success( 'Markets set' );
