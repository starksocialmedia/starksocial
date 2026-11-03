# Knowledgebase import (BetterDocs)

Copies live's knowledgebase (27 docs, 6 categories, BetterDocs settings) into a
Phase 2 site. URLs match live: `/support/knowledgebase/`, `/docs/{slug}/`,
`/docs-category/{slug}/`. Docs are plain HTML on live (no Cornerstone), so no
cleanup is needed.

1. Export from live (read-only):

```
ssh stark-live 'cd ~/public_html && wp --skip-themes eval '"'"'
$out = array( "settings" => get_option( "betterdocs_settings" ), "terms" => array(), "docs" => array() );
foreach ( get_terms( array( "taxonomy" => "doc_category", "hide_empty" => false ) ) as $t ) $out["terms"][] = array( "id" => $t->term_id, "name" => $t->name, "slug" => $t->slug, "description" => $t->description, "parent" => $t->parent, "order" => get_term_meta( $t->term_id, "doc_category_order", true ) );
foreach ( get_posts( array( "post_type" => "docs", "post_status" => "publish", "numberposts" => -1 ) ) as $p ) $out["docs"][] = array( "id" => $p->ID, "title" => $p->post_title, "slug" => $p->post_name, "content" => $p->post_content, "excerpt" => $p->post_excerpt, "date" => $p->post_date, "modified" => $p->post_modified, "menu_order" => $p->menu_order, "author" => $p->post_author, "cats" => wp_get_post_terms( $p->ID, "doc_category", array( "fields" => "slugs" ) ), "tags" => wp_get_post_terms( $p->ID, "doc_tag", array( "fields" => "names" ) ), "thumb" => get_post_thumbnail_id( $p->ID ) );
echo json_encode( $out );
'"'"' 2>/dev/null' > kb.json
```

2. Import (needs the `support` page): `STARK_JSON=kb.json wp eval-file tools/kb-import/seed-kb.php`.
   Creates `/support/knowledgebase/` if missing, copies the settings with
   `docs_page` pointed at it, and creates or updates docs by slug.

Run on Local November 2 2026 (v27.1.2). Re-run at cutover if docs change on live.
