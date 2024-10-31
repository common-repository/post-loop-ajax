<?php
/*
see: http://stackoverflow.com/questions/29451220/easy-digital-downloads-and-pagination
*/

//function PostLoopAjaxGetPagination($widget_number, $paged, $max, $instance) {
function PostLoopAjaxGetPagination($paged, $max, $instance) {
    $paged = intval($paged);    // undefined
    //$instance = $instance['cookie_expires'];
    $pagination_type = $instance['pagination_type'];
    // $linkType: 0, 1, 2, 3;
    if ($pagination_type == 0) {
        return '';
    }
    $hoge = preg_split('/\s+/', $instance['class']);
    $target_class = array_pop($hoge).'-'.$instance['widget_number'];

    if ($max <= 1) {
        return '';
    }

    if (!$paged) {
        $paged = 1;
    }

    $tag = '<div class="post-loop-ajax-navigation post-loop-ajax-pagination">';

    if ($pagination_type == 4) {    // full
        for ($i = 1; $i <= $max; $i++) {
            $classes = $i == $paged ? ' class="current"' : '';
            $link = $i;
            $tag .= PostLoopAjaxGetPagination_getPageLink($classes, $link, $link, $target_class, $instance);
        }
        $tag .= '</div>';
        return $tag;
    }

    // Add current page to the array
    if ($paged >= 1) {
        $links[] = $paged;
    }

    // Add the pages around the current page to the array
    if ($pagination_type == 2) {
        if ($paged >= 3) {
            $links[] = $paged - 1;
            //$links[] = $paged - 2;
        }

        if (($paged + 2) <= $max) {
            //$links[] = $paged + 2;
            $links[] = $paged + 1;
        }
    } else if ($pagination_type == 3) {
    } else {    // 0
        if ($paged >= 3) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if (($paged + 2) <= $max) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }
    }

    $prev_page_str = __("&laquo; Previous Page");
    if ($instance['navi_string_prev']) {
        $prev_page_str = $instance['navi_string_prev'];
    }
    $next_page_str = __("Next Page &raquo;");
    if ($instance['navi_string_next']) {
        $next_page_str = $instance['navi_string_next'];
    }


    // Previous Post Link
    //if (get_previous_posts_link() || ($max > 1 && $paged > 1)) {
    if (!$instance['hide_prev_and_next_button'] && $max > 1 && $paged > 1) {

        $link = $paged - 1;
        $classes = '';

        $tag .= PostLoopAjaxGetPagination_getPageLink($classes, $link, $prev_page_str, $target_class, $instance);
    }

    if ($pagination_type != 3) {
        // Link to first page, plus ellipses if necessary
        if (!in_array(1, $links)) {
            $classes = 1 == $paged ? ' class="current"' : '';

            $link = 1;
            $tag .= PostLoopAjaxGetPagination_getPageLink($classes, $link, $link, $target_class, $instance);

            if (!in_array(2, $links)) {
                $tag .= ' … ';
            }
        }

        // Link to current page, plus 2 pages in either direction if necessary
        sort($links);
        foreach ((array) $links as $link) {
            $classes = $paged == $link ? ' class="current"' : '';
            $tag .= PostLoopAjaxGetPagination_getPageLink($classes, $link, $link, $target_class, $instance);
        }

        // Link to last page, plus ellipses if necessary
        if (!in_array($max, $links)) {
            if (!in_array($max - 1, $links)) {
                $tag .= ' … ';
            }

            $classes = $paged == $max ? ' class="current"' : '';
            $link = $max;
            $tag .= PostLoopAjaxGetPagination_getPageLink($classes, $link, $link, $target_class, $instance);
        }
    }

    // Next Post Link
    //if (!$classes && (get_next_posts_link() || ($max > 1 && $paged < $max))) {
    if (!$instance['hide_prev_and_next_button'] && $max > 1 && $paged < $max) {
        if (!$paged) {
            $paged = 1;
        }
        $link = $paged + 1;
        $classes = '';
        $tag .= PostLoopAjaxGetPagination_getPageLink($classes, $link, $next_page_str, $target_class, $instance);
    }
    $tag .= '</div>';

    return $tag;
}

function PostLoopAjaxGetPagination_getPageLink($classes, $page, $text, $target_class, $instance) {
    return sprintf('<a %s href="#" page="%d" onclick="post_loop_ajax_move_page(\'%s\',%d,%d,%d,%d,%d,%d,\'%s\',%d,1,0);return false;">%s</a>', $classes, $page, $instance['widget_number'], $page, $instance['cookie_expires'], $instance['need_reload_page'], $instance['scroll_content_top_after_reloaded'], $instance['scroll_offset_top'], $instance['scroll_speed'], $target_class, $instance['do_not_display_loading_image'], $text);
}

