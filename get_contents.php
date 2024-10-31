<?php
/*
get_contents.php

esc_url
*/

require_once('pagination.php');

$PostLoopAjax_excerpt_ellipsis = '';

function PostLoopAjaxGetArticle($widget_number, $instance, $paged, $is_get_request, $is_click) {
    global $PostLoopAjax_excerpt_ellipsis;
    //echo '<div>widget_number: '.$widget_number.'</div>';
    //echo '<div>widget_number: '.$instance['widget_number'].'</div>';

    $cookie_key = 'postloopajax-'.$widget_number;

    // Can't get instance if use "multiwidget" and "Ajax Request" case. for the reason, instance get from option.
    $opts = get_option('postloopajax');
    $instance = $opts[$widget_number];
    //--

    //print_r($instance['effect_front_types']);

    if ($paged < 1) {
        if (array_key_exists($cookie_key, $_COOKIE)) {
            $paged = intval($_COOKIE[$cookie_key]);
        } else {
            $paged = 1;
        }
    }
    if (!$paged) {
        $paged = 1;
    }
    //echo 'paged: '.$paged;

    $articles = PostLoopAjax_getArticle($instance);
    $result = $articles[0]; // header

    // execute query
    //echo 'PostLoopAjaxGetQuery-1/';
    $query = PostLoopAjaxGetQuery($instance, $paged);
    $numberOfPost = $query->found_posts;
    if ($instance['max_page_number'] > 0 && $instance['max_page_number'] < $query->max_num_pages) {
        $maxNumberOfPages = $instance['max_page_number'];
    } else {
        $maxNumberOfPages = $query->max_num_pages;
    }
    //echo 'max: ' .$maxNumberOfPages;

    if ($maxNumberOfPages < 1) {
        // executed query by irregular page number.
        //echo 'PostLoopAjaxGetQuery-2/';
        $query = PostLoopAjaxGetQuery($instance, 1); // re-query by page number is 1.
        $numberOfPost = $query->found_posts;
        //$maxNumberOfPages = $query->max_num_pages;
        if ($instance['max_page_number'] > 0 && $instance['max_page_number'] < $query->max_num_pages) {
            $maxNumberOfPages = $instance['max_page_number'];
        } else {
            $maxNumberOfPages = $query->max_num_pages;
        }
    }

    if ($instance['page_at_random'] && !$is_click && !$instance['need_reload_page'] && $maxNumberOfPages > 1) {
        $paged = mt_rand(1, $maxNumberOfPages);
        //echo 'PostLoopAjaxGetQuery-3/';
        $query = PostLoopAjaxGetQuery($instance, $paged);
    } else if ($paged > $maxNumberOfPages) {
        $paged = $maxNumberOfPages;
        //echo 'PostLoopAjaxGetQuery-4/';
        $query = PostLoopAjaxGetQuery($instance, $paged);
    }
    //echo 'PostLoopAjaxGetQuery-3/';
    //$query = PostLoopAjaxGetQuery($instance, $paged);


    //$result .= '<input type="hidden" name="contents-paged" value="'.$paged.'" />'; // page number

    $PostLoopAjax_excerpt_ellipsis = $instance['excerpt_ellipsis']; // insert global variable
    $count = 0;
    if ($query->have_posts()){

        while ($query->have_posts()) {
            $count++;

            $query->the_post();
            $templateA1 = $articles[1]; // <article>...</article>

            $post_id = get_the_ID();
            $permalink = get_the_permalink();
            $title = get_the_title();
            $content = apply_filters('the_content', get_the_content());
            /*
            // replace: %post_class%
            Losing part of article class on change

            When moving from the first page to another (either manually or automatically), the article is losing part of it’s CSS class.

            Example, in a 2 post loop, in a page, as a SiteOrigin widget:
            – loading the page, i get this:
            <article id=”post-2398″ class=”post-2398 post type-post status-publish format-standard hentry category-uncategorized”>
            – moving to the next page:
            <article id=”post-1996″ class=”post-1996 type-post status-publish format-standard hentry category-uncategorized”>
            – moving back to the first page:
            <article id=”post-2398″ class=”post-2398 type-post status-publish format-standard hentry category-uncategorized”>

            The post class gets lost somehow. Any idea why this should happen?
            */
            $post_type = get_post_type($post_id);
            if (!$post_type) {
                $post_type = '';
            }

            $templateA1 = str_replace('%post_id%', $post_id, $templateA1);
            $templateA1 = str_replace('%post_class%', 'class="'.join(' ', get_post_class($post_type, $post_id)).'"', $templateA1);
            $templateA1 = str_replace('%permalink%', $permalink, $templateA1);
            $templateA1 = str_replace('%title%', $title, $templateA1);
            $templateA1 = str_replace('%author%', get_the_author(), $templateA1);
            $templateA1 = str_replace('%time%', get_the_time($instance['time_format']), $templateA1);
            $templateA1 = str_replace('%author_posts_link%', get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename')), $templateA1);
            $templateA1 = str_replace('%excerpt%', PostLoopAjax_getTheExcerptMaxCharlength($instance['excerpt_char_length'], $instance['excerpt_more'], $permalink, $title), $templateA1);
            $templateA1 = str_replace('%category_links%', PostLoopAjaxGetCategoryLinks(), $templateA1);

            // replace function type string.
            list($templateA1, $content) = PostLoopAjax_replace_functions($post_id, $content, $templateA1, get_the_author_meta('ID'));
            // old function (ABCDE)
            $templateA1 = PostLoopAjax_replaceFirstImageKeyword($content, $templateA1);     // first image

            $templateA1 = str_replace('%content%', $content, $templateA1);
            $result .= $templateA1;
        }
        $result .= $articles[3];    // footer
        $result .= '<input type="hidden" name="contents-paged" value="'.$paged.'" />';   // page number
        if (!$is_get_request && $instance['remove_shortcode_not_work']) {
            $result = strip_shortcodes($result);
        }
        wp_reset_postdata();
    } else {
        $result .= $articles[2] . $articles[4]; // no content and no content footer
    }

    // 1 page only
    if ($numberOfPost == 0 && $count > 0) {
        //echo '<p>faild to number of post</p>';
        $numberOfPost = $count;
        if ($count == 0) {
            $maxNumberOfPages = 0;
            $paged = 0;
        } else {
            $maxNumberOfPages = 1;
            $paged = 1;
        }
    }

    $result = str_replace('%widget_number%', $widget_number, $result);
    $result = str_replace('%loop_interval%', $instance['loop'], $result);
    $result = str_replace('%paged%', $paged, $result);
    $result = str_replace('%max_number_of_pages%', $maxNumberOfPages, $result);
    $result = str_replace('%number_of_posts%', $numberOfPost, $result);
    if (intval($instance['pagination_type']) == 99) {
        $instance['pagination_type'] = 1; // normal
        $pagination = '<div class="post-loop-ajax-pagination-normal">'.PostLoopAjaxGetPagination($paged, $maxNumberOfPages, $instance).'</div>';
        $instance['pagination_type'] = 2; // lite
        $pagination .= '<div class="post-loop-ajax-pagination-lite">'.PostLoopAjaxGetPagination($paged, $maxNumberOfPages, $instance).'</div>';
        $instance['pagination_type'] = 3; // prev next only
        $pagination .= '<div class="post-loop-ajax-pagination-prev-next-only">'.PostLoopAjaxGetPagination($paged, $maxNumberOfPages, $instance).'</div>';
        $instance['pagination_type'] = 99;
    } else {
        $pagination = PostLoopAjaxGetPagination($paged, $maxNumberOfPages, $instance);
    }
    $result = str_replace('%pagination%', $pagination, $result);
    $result = do_shortcode($result);

    if ($instance['do_not_display_loading_image']) {
        $do_not_display_loading_image = '1';
    } else {
        $do_not_display_loading_image = '0';
    }
    $f = preg_split('/\s+/', $instance['class']);
    $target_class = array_pop($f).'-'.$widget_number;

    $loopScript = '';
    if (!$instance['need_reload_page'] && $instance['loop'] > 0 && $maxNumberOfPages > 1) {
        $loopInterval = $instance['loop'] * 1000;
$loopScript=<<<EODFOOTER
<script>
jQuery(document).ready(function(){
    var timer;
    var onHover = false;
    jQuery(".${target_class}").hover(function(){
        //console.log('hover in');
        onHover = true;
    },function(){
        //console.log('hover out');
        onHover = false;
    });

    function PostLoopAjaxLoop() {
        //console.log('PostLoopAjaxLoop');
        clearTimeout(timer);
        timer = setTimeout(function() {
            //console.log('PostLoopAjaxLoop: timeout');
            //if (jQuery('html.fl-builder-edit')[0] || onHover) {
            if (onHover) {
                PostLoopAjaxLoop();
                return false;
            }

            var max_page = ${maxNumberOfPages};
            var page = jQuery.cookie("${cookie_key}");
            if (isNaN(page)) {
                page = 0;
            }
            page++;
            if (page > max_page) {
                page = 1;
            }
            var need_reload_page = 1;
            if ("${instance['need_reload_page']}" == "") {
                need_reload_page = 0;
            }
            var scroll_content_top_after_reloaded = 1;
            if ("${instance['scroll_content_top_after_reloaded']}" == "") {
                scroll_content_top_after_reloaded = 0;
            }
            var scroll_offset_top = 1;
            if ("${instance['scroll_offset_top']}" == "") {
                scroll_offset_top = 0;
            }
            var scroll_speed = 1;
            if ("${instance['scroll_speed']}" == "") {
                scroll_speed = 0;
            }
            post_loop_ajax_move_page("${widget_number}", page, ${instance['cookie_expires']}, need_reload_page, scroll_content_top_after_reloaded, scroll_offset_top, scroll_speed, "${target_class}", ${do_not_display_loading_image}, 0, PostLoopAjaxLoop);
        }, ${loopInterval});
    }
    //console.log('PostLoopAjax Ready Function');
    PostLoopAjaxLoop();
});
</script>
EODFOOTER;
    }
    if (!$instance['need_reload_page']) {
$loopScript.=<<<EODFOOTER
<script>
jQuery(window).on('load', function(){
    //console.log('load: click back-button?');

    var paged = parseInt(jQuery.cookie('postloopajax-unload-${widget_number}'));   // get
    jQuery.cookie('postloopajax-unload-${widget_number}', null);        // unset
    if (!paged) {
        paged = 1;
    }

    //console.log("back to the: " + paged);

    var currnetPaged = parseInt(jQuery('.${target_class} input[name=contents-paged]').val());
    //console.log("currnetPaged: " + currnetPaged + ", paged: " + paged);
    if (!currnetPaged || currnetPaged == paged) { // not back-button or same page.
        return;
    }

    // restore page
    /* 2017-03-29
    it is need restore the post when come back widget placement page.
    but widget not know is it is if came back.
    it is working too if random and disabled loop.
    following code is disable restore the post for ajax if random and disabled loop.
    */
    //console.log('restore page: page_at_random: ${instance['page_at_random']}, loopInterval: ${instance['loop']}');
    var page_at_random = '${instance['page_at_random']}';
    var loop = '${instance['loop']}';
    if (page_at_random > 0 && loop < 1) {
        //console.log('random and disabled loop');
    } else {
        //console.log('Not: random and disabled loop');
        post_loop_ajax_move_page("${widget_number}", paged, ${instance['cookie_expires']}, 0, 0, 0, 0, "${target_class}", ${do_not_display_loading_image}, 0, 0);
    }
});
jQuery(window).on('unload', function(){
    //console.log('unload');
    var paged = jQuery.cookie('postloopajax-${widget_number}');
    jQuery.cookie('postloopajax-unload-${widget_number}', paged, { expires: 1 }); // set
    location.reload(true); // important. call load event when click back-button.
});
</script>
EODFOOTER;
    }

    return array($result, $loopScript, $instance);
}


function  PostLoopAjax_getArticle($instance) {
    //print_r($instance);
    $use_this = __("Use this:",POSTLOOPAJAX_DOMAIN);
    $template_name = $instance['template_name'];
    if ($template_name == $use_this) {
        return explode('<!--split-->', $instance['article']);
    }

    $options = get_option(POSTLOOPAJAX_DOMAIN);
    if (array_key_exists($template_name, $options['templates'])) {
        $template = $options['templates'][$template_name]['template'];
        if ($template) {
            $template_array = explode('<!--split-->', $template);
            if (is_array($template_array)) {
                return $template_array;
            }
        }
    }
    return explode('<!--split-->', $instance['article']);
}

//-----------------------------------------------------

function PostLoopAjax_replace_functions($post_id, $content, $template, $author_id) {
    //if (preg_match('/%(first_image|custom_field|avatar_image|featured_image_url)\(([^\(\)]*?)\)%/', $template, $matches)) {
    if (preg_match('/%(first_image|custom_field|avatar_image|featured_image_url)\(((?!.*(first_image|custom_field|avatar_image|featured_image_url)).+?)\)%/', $template, $matches)) {
        $match = $matches[0];
        $function_name = $matches[1];
        $args = explode(',', $matches[2]);
        //$args = PostLoopAjax_splitArgs($matches[2]);
        if ($function_name == 'first_image') {
            //echo '<p>match:'.$match.'</p>';
            list($template, $content) = PostLoopAjax_replace_firstImage($match, $args, $content, $template);
            return PostLoopAjax_replace_functions($post_id, $content, $template, $author_id);
        } else if ($function_name == 'custom_field') {
            //echo '<p>match:'.$match.'</p>';
            return PostLoopAjax_replace_functions($post_id, $content, PostLoopAjax_replace_customField($match, $args, $post_id, $template), $author_id);
        } else if ($function_name == 'avatar_image') {
            //echo '*avatar_image';
            return PostLoopAjax_replace_functions($post_id, $content, PostLoopAjax_replace_avatarImage($match, $args, $author_id, $template), $author_id);
        } else if ($function_name == 'featured_image_url') {
            return PostLoopAjax_replace_functions($post_id, $content, PostLoopAjax_replace_featuredImageUrl($post_id, $match, $args, $author_id, $template), $author_id);
        }
    } else {
        return array($template, $content);   // return is only one.
    }
}


function PostLoopAjax_replace_featuredImageUrl($post_id, $match, $args, $content, $template) {
    if (!has_post_thumbnail($post_id)) {
        return str_replace($match, '', $template);
    }

    $argc = count($args);
    if ($argc == 1) {
        $size = $args[0];
    } else if ($argc == 2) {
        $size = array($args[0], $args[1]);
    }

    $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
    $imageUrl = $image[0];
    return str_replace($match, $imageUrl, $template);
}

function PostLoopAjax_replace_firstImage($match, $args, $content, $template) {
    $link = '';
    $remove = false;
    $html = '';
    $argc = count($args);

    $size = $args[0];
    if ($argc > 1) {
        $link = $args[1];
        if (!$link) {
            $link = '%url%';
        }
    }
    if ($argc > 2) {
        if ($args[2]) {
            $remove = true;
        }
    }
    if ($argc > 3) {
        $html = $args[3];
    }

    // find image tag from post
    if (!preg_match('/<img\s+[^>]+>/', $content, $matches)) {
        //echo '['.$match.']';
        return array(str_replace($match, '', $template), $content);
    }
    //echo $matches[0];
    $img_tag = $matches[0];
    // find image src
    if (!preg_match('/src=["\']{1}(.*?)["\']/', $img_tag, $matches)) {
        return array(str_replace($match, '', $template), $content);
    }
    //echo $matches[0];
    //echo $matches[1];
    $src = $matches[1];
    // find image id
    //if (!preg_match('/class=["\']{1}.*(wp-image-)([0-9]+)\s{0,}["\']{1}/', $img_tag, $matches)) {
    if (!preg_match('/class=["\']{1}.*(wp-image-)([0-9]+).*["\']{1}/', $img_tag, $matches)) {
        return array(str_replace($match, '', $template), $content);
    }
    //echo $matches[0];
    //echo $matches[1];
    //echo $matches[2];
    // check image id
    $id = $matches[2];
    // check image id
    if (!$id) {
        return array(str_replace($match, '', $template), $content);
    }
    $array = image_downsize($id, $size); // url,width,height, is size?
    // check got image?
    if (!$array) {
        return array(str_replace($match, '', $template), $content);
    }
    $image_url = $array[0];
    //print_r($path);
    //echo $array[0];
    //return $array[0];
    if ($remove) {
        //echo 'remove first image';
        $content = PostLoopAjax_removeTextFromContent($img_tag, $content);
    }
    if ($link == '%url%') {
        if ($html) {
            $html = str_replace('_REPLACE_', $image_url, $html);
        } else {
            $html = $image_url;
        }
        return array(str_replace($match, $html, $template), $content);
        //return array(str_replace($match, $image_url, $template), $content);
    } else if ($link) {
        $a = '<a class="postloopajax-image-link" href="'.$link.'"><img src="'.$image_url.'" class="wp-image-'.$id.'"/></a>';
        if ($html) {
            $html = str_replace('_REPLACE_', $a, $html);
        } else {
            $html = $a;
        }
        return array(str_replace($match, $html, $template), $content);
        //return array(str_replace($match, '<a class="postloopajax-image-link" href="'.$link.'"><img src="'.$image_url.'" class="wp-image-'.$id.'"/></a>', $template), $content);
    } else {
        $a = '<img src="'.$image_url.'" class="wp-image-'.$id.'"/>';
        if ($html) {
            $html = str_replace('_REPLACE_', $a, $html);
        } else {
            $html = $a;
        }
        return array(str_replace($match, $html, $template), $content);
        //return array(str_replace($match, '<img src="'.$image_url.'" class="wp-image-'.$id.'"/>', $template), $content);
    }
}

/*
 * remove text and this outer tags from content.
 */
function PostLoopAjax_removeTextFromContent($text, $content) {
    //echo '[['.$text.']]';
    // figure
    $pattern_f = '/<figure[^<]*>[\s]*__DELETE__.*?<\/figure>/i';    // html5
    $pattern   = '/<[^<]+>[\s]*__DELETE__[\s]*<\/[^>]+>/i';
    $c = 1;
    $content = str_replace($text, '__DELETE__', $content, $c);
    do {
        $content = preg_replace($pattern_f, '__DELETE__', $content);
        $content = preg_replace($pattern,   '__DELETE__', $content);
    } while (preg_match($pattern, $content)); // no match or error.
    return str_replace('__DELETE__', '', $content);
}

function PostLoopAjax_replace_customField($match, $args, $post_id, $template) {
    $type = $args[0];
    if ($type == 'text') {
        if (count($args) == 3) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], $args[2]);
            return str_replace($match, $cf, $template);
        } else if (count($args) == 2) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], 0);
            return str_replace($match, $cf, $template);
        } else {
            $cf = '';
            return str_replace($match, $cf, $template);
        }
    } else if (preg_match('/^image/', $type)) {
        if (count($args) == 4) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], $args[2], $args[3]);
            return str_replace($match, $cf, $template);
        } else if (count($args) == 3) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], $args[2], 'large');
            return str_replace($match, $cf, $template);
        } else if (count($args) == 2) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], 0, 'large');
            return str_replace($match, $cf, $template);
        } else {
            $cf = '';
            return str_replace($match, $cf, $template);
        }
    } else {
        if (count($args) == 4) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], $args[2]);
            if (!$args[3]) {
                //$args[3] = $cf;
                return str_replace($match, $cf, $template);
            }
            return str_replace($match, '<a href="'.$cf.'">'.$args[3].'</a>', $template);
        } else if (count($args) == 3) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], $args[2]);
            $link = $cf;
            //return str_replace($match, $cf, $template);
            if ($type == 'file') {
                //echo 'DIRECTORY_SEPARATOR: '.DIRECTORY_SEPARATOR;
                //$hoge = explode(DIRECTORY_SEPARATOR, $cf);
                $hoge = explode('/', $cf);
                //print_r($hoge);
                $link = array_pop($hoge);
            }
            return str_replace($match, '<a href="'.$cf.'">'.$link.'</a>', $template);
        } else if (count($args) == 2) {
            $cf =  PostLoopAjax_getCustomField($post_id, $type, $args[1], 0);
            return str_replace($match, $cf, $template);
        } else {
            $cf = '';
            return str_replace($match, $cf, $template);
        }
    }
}


function PostLoopAjax_splitArgs($str) {
    $cs = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY); // UTF-8
    $fDq = false;
    $fQu = false;
    $fEs = false;
    $i = 0;
    $a = array();
    foreach($cs as $c) {
        //echo $c;
        if ($c == ',' && !$fDq && !$fQu && !$fEs) {
            $a[$i++] .= '';
            $a[$i] = '';
            continue;
        } else if ($c == '"' && !$fQu && !$fEs) {
            if ($fDq) {
                $fDq = false;
            } else {
                $fDq = true;
            }
            continue;
        } else if ($c == "'" && !$fDq && !$fEs) {
            if ($fQu) {
                $fQu = false;
            } else {
                $fQu = true;
            }
            continue;
        } else if ($c == "\\" && !$fEs) {
            $fEs = true;
            continue;
        } else if ($fEs) {
            $fEs = false;
        }

        $a[$i] .= $c;
    }
    return $a;
}

function PostLoopAjax_replace_avatarImage($match, $args, $author_id, $template) {
    $default_size = 96;
    $max_size = 512;
    $avatar_image_url = '';
    if (count($args) == 2) {
        $size = intval($args[0]);
        if (!$size) {
            $size = $default_size;
        } else if ($size > $max_size) {
            $size = $max_size;
        }
        $link = $args[1];
        if (!$link) {
            $link = '%url%';
            $avatar_image = get_avatar($author_id, $size);
            if (preg_match('/src=[\"\']{1}([^"\']*)[\"\']{1}/', $avatar_image, $matches)) {
                $avatar_image_url = $matches[1];
            }
        }
    } else {
        $size = intval($args[0]);
        if (!$size) {
            $size = $default_size;
        } else if ($size > $max_size) {
            $size = $max_size;
        }
        $link = '';
    }

    if ($size > $max_size) {
        $size = $max_size;
    }

    $avatar_image = get_avatar($author_id, $size);

    if (!$avatar_image) {
        return str_replace($match, '', $template);
    } else if ($avatar_image_url) {
        return str_replace($match, $avatar_image_url, $template);
    } else if ($link == '%url%') {
        return str_replace($match, $avatar_image, $template);
    } else if ($link) {
        return str_replace($match, '<a class="postloopajax-avatar-link postloopajax-avatar-link-'.$author_id.'" href="'.$link.'">'.$avatar_image.'</a>', $template);
    } else {
        return str_replace($match, $avatar_image, $template);
    }
}


//-----------------------------------------------------


function PostLoopAjax_replaceAvatarImage($author_id, $template) {
    $default_size = 96;
    $max_size = 512;
    // find first image keyword
    $avatar_image_url = '';
    if (preg_match('/%avatar_image_([0-9]+)\((.*)\)%/', $template, $matches)) {
        //echo $matches[0];
        $match = $matches[0];
        //echo $matches[1];
        $size = $matches[1];
        $link = $matches[2];
    } else if (preg_match('/%avatar_image_([0-9]+)%/', $template, $matches)) {
        $match = $matches[0];
        $size = $matches[1];
        $link = '%url%';
    } else if (preg_match('/%avatar_image\(([^\)]*)\)%/', $template, $matches)) {
        $match = $matches[0];
        $args = explode(',', $matches[1]);
        //$args = PostLoopAjax_splitArgs($matches[1]);
        if (count($args) == 2) {
            $size = intval($args[0]);
            if (!$size) {
                $size = $default_size;
            } else if ($size > $max_size) {
                $size = $max_size;
            }
            $link = $args[1];
            if (!$link) {
                $link = '%url%';
                $avatar_image = get_avatar($author_id, $size);
                if (preg_match('/src=[\"\']{1}([^"\']*)[\"\']{1}/', $avatar_image, $matches)) {
                    $avatar_image_url = $matches[1];
                }
            }
        } else {
            $size = intval($args[0]);
            if (!$size) {
                $size = $default_size;
            } else if ($size > $max_size) {
                $size = $max_size;
            }
            $link = '';
        }
    } else {
        return $template;   // return is only one.
    }

    if ($size > $max_size) {
        $size = $max_size;
    }

    $avatar_image = get_avatar($author_id, $size);

    if (!$avatar_image) {
        return PostLoopAjax_replaceAvatarImage($author_id, str_replace($match, '', $template));
    } else if ($avatar_image_url) {
        return PostLoopAjax_replaceAvatarImage($author_id, str_replace($match, $avatar_image_url, $template));
    } else if ($link == '%url%') {
        return PostLoopAjax_replaceAvatarImage($author_id, str_replace($match, $avatar_image, $template));
    } else if ($link) {
        return PostLoopAjax_replaceAvatarImage($author_id, str_replace($match, '<a class="postloopajax-avatar-link postloopajax-avatar-link-'.$author_id.'" href="'.$link.'">'.$avatar_image.'</a>', $template));
    } else {
        return PostLoopAjax_replaceAvatarImage($author_id, str_replace($match, $avatar_image, $template));
    }
}

// old function (ABCDE)
/* */
function PostLoopAjax_replaceFirstImageKeyword($content, $template) {
    // find first image keyword
    if (preg_match('/%first_image_([^%]+)\((.*)\)%/', $template, $matches)) {
        $match = $matches[0];
        $size = $matches[1];
        $link = $matches[2];
    } else if (preg_match('/%first_image_([^%]+)%/', $template, $matches)) {
        $match = $matches[0];
        $size = $matches[1];
        $link = '%url%';
    } else {
        return $template;
    }
    // find image tag from post
    if (!preg_match('/<img\s+[^>]+>/', $content, $matches)) {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '', $template));
    }
    // find image src
    if (!preg_match('/src=["\']{1}(.*?)["\']/', $content, $matches)) {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '', $template));
    }
    $src = $matches[1];
    // find image id
    if (!preg_match('/class=["\']{1}.*(wp-image-)([0-9]+)\s{0,}["\']{1}/', $content, $matches)) {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '', $template));
    }
    // check image id
    $id = $matches[2];
    // check image id
    if (!$id) {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '', $template));
    }
    $array = image_downsize($id, $size); // url,width,height, is size?
    // check got image?
    if (!$array) {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '', $template));
    }
    $image_url = $array[0];
    if ($link == '%url%') {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, $image_url, $template));
    } else if ($link) {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '<a class="postloopajax-image-link" href="'.$link.'"><img src="'.$image_url.'" class="wp-image-'.$id.'"/></a>', $template));
    } else {
        return PostLoopAjax_replaceFirstImageKeyword($content, str_replace($match, '<img src="'.$image_url.'" class="wp-image-'.$id.'"/>', $template));
    }
}
/* */

function PostLoopAjaxGetQuery($instance, $paged) {
    //echo 'PostLoopAjaxGetQuery-body/';
    //require_once 'print_r_reverse.php';
    //$q = postloopajax\print_r_reverse($instance['query_code']);
    //print $q;

    // use 'query_code_array' ----------------------------------
    $query_code_array = $instance['query_code_array'];
    //print_r($query_code_array);
    //echo '/';
    if ($query_code_array) {
        if (!is_array($query_code_array)) {
            //$query_code_array = array($query_code_array);
        }
        if (!array_key_exists('post_status', $query_code_array) || !$query_code_array['post_status']) {
            $query_code_array['post_status'] = 'publish';
        }
        if (!array_key_exists('posts_per_page', $query_code_array) || !$query_code_array['posts_per_page']) {
            $query_code_array['posts_per_page'] = $instance['posts_per_page'];
        }
        $query_code_array['paged'] = $paged;
        //print_r($query_code_array);
        //echo '/';
        return new WP_Query($query_code_array);
    }

    // use 'qs_array' ------------------------------------------
    $qs_array = $instance['qs_array'];
    // version 1.19 to 1.20 --
    if (is_null($qs_array) || !is_array($qs_array)) {
        //echo '<p style="color:red;">Post Loop Ajax Plugin: Save Settings Again. This message is by Update.</p>';
        $qs_array = array();
        $qss = explode('&', $instance['qs']);
        foreach ($qss as $key => $key_value) {
                list($key, $value) = explode('=', $key_value, 2);
            //if ($key == 'post_type') {
            if (preg_match('/^\(([^\(\)]+)\)$/', $value, $matches)) {
                $qs_array[$key] = explode(',', $matches[1]);
            } else {
                $qs_array[$key] = $value;
            }
        }
        //print_r($qs_array);
        $instance['qs_array'] = $qs_array;
    }
    //--
    if (!array_key_exists('post_status', $qs_array) || !$qs_array['post_status']) {
        $qs_array['post_status'] = 'publish';
    }
    $qs_array['posts_per_page'] = $instance['posts_per_page'];
    $qs_array['paged'] = $paged;

    //print_r($qs_array);

    return new WP_Query($qs_array);
}

function PostLoopAjaxGetCategoryLinks(){
    $tag = '';
    foreach((get_the_category()) as $category) {
        $category_link = get_category_link($category->cat_ID);
        $tag .= sprintf("<span><a href=\"%s\" title=\"%s\">%s</a></span>", $category_link, $category->name, $category->name);
    }
    return $tag;
}

function PostLoopAjax_excerpt_more($more) {
    global $PostLoopAjax_excerpt_ellipsis;
	return $PostLoopAjax_excerpt_ellipsis;
}
add_filter('excerpt_more', 'PostLoopAjax_excerpt_more');

function PostLoopAjax_getTheExcerptMaxCharlength($charlength, $more, $permalink, $title) {
	$excerpt = get_the_excerpt();
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
        add_filter('excerpt_more', 'PostLoopAjax_excerpt_more');

		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$excerpt = mb_substr( $subex, 0, $excut );
		} else {
			$excerpt = $subex;
		}
        // ...[Raed More]
		//$excerpt .= $ellipsis;

        remove_filter('excerpt_more', 'PostLoopAjax_excerpt_more');
	}
    $excerpt .= '<a class="post-loop-ajax-more" href="'.$permalink.'" title="'.$title.'">'.$more.'</a>';
    return $excerpt;
}


function  PostLoopAjax_getCustomField($post_id, $type, $name, $index, $size = 'large') {
    //$cfs = get_post_custom($post_id);
    //print_r($cfs);

    $metas = get_post_meta($post_id, $name, false);
    $meta = $metas[$index];
    //print_r($meta);

    if ($type == 'text') {
        return $meta;
    }

    if (!$meta) {
        //echo 'custom field not found';
        return '';
    }

    if ($type == 'image_tag' || $type == 'image') {
        return wp_get_attachment_image($meta, $size);
    } else if ($type == 'image_width') {
        $img_src = wp_get_attachment_image_src($meta, $size);
        return $img_src[1];
    } else if ($type == 'image_height') {
        $img_src = wp_get_attachment_image_src($meta, $size);
        return $img_src[2];
    } else if ($type == 'image_is_resized') {
        $img_src = wp_get_attachment_image_src($meta, $size);
        return $img_src[3];
    } else {
        return wp_get_attachment_url($meta, $size);
    }
}
