<?php
/**
 * Navigation Menu
 *
 * @package Casino-forms
 * @subpackage Nav_Menus
 * @since 0.1.1
 */

class Walker_Playpage_Menu extends Walker {

    // Tell Walker where to inherit it's parent and id values
    var $db_fields = array(
        'parent' => 'menu_item_parent', 
        'id'     => 'db_id' 
    );

    /**
     * At the start of each element, output a <li> and <a> tag structure.
     * 
     * Note: Menu objects include url and title properties, so we will use those.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $post;
		
		$hasActiveLink = get_post_meta($post->ID, '_leadya_with_link', true);
		
		if( $hasActiveLink == "" ){
			$output .= sprintf( "\n<li><span data-href='%s'%s>%s</span></li>\n",
				$item->url,
				( $item->object_id === get_the_ID() ) ? ' class="current"' : '',
				$item->title
			);
		} else {
			$output .= sprintf( "\n<li><a href='%s'%s>%s</a></li>\n",
				$item->url,
				( $item->object_id === get_the_ID() ) ? ' class="current"' : '',
				$item->title
			);
		}
    }

}