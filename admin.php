<?php

namespace postloopajax;

function plugin_menu() {
    add_options_page(__('Plugin Options', POSTLOOPAJAX_DOMAIN), __('Post Loop Ajax', POSTLOOPAJAX_DOMAIN), 'administrator', POSTLOOPAJAX_DOMAIN . '-settings.php', 'postloopajax\plugin_options');
}

add_action('admin_menu', 'postloopajax\plugin_menu');

/*
function stripbackslashes($value) {
    if (get_magic_quotes_gpc()) {
        return stripslashes($value);
    }
    return $value;
}
*/

function getPostValueAdmin($key) {
    if (!array_key_exists($key, $_POST)) {
        return null;
    }

    $postValue = $_POST[$key];

    // double quotation test
    if ($_POST["_doublequotation"] == '\"') {
        $postValue = stripslashes_deep($postValue);
    }

    if ($key === '_doublequotation') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'submit_hidden') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'cmd-load') {   // template name
        return strip_tags($postValue);
    } else if ($key === 'cmd-add-new') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'cmd-update') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'cmd-delete') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'confirm-delete') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'template-name') {      // template name
        return strip_tags($postValue);
    } else if ($key === 'template-name-save') { // template name
        return strip_tags($postValue);
    } else if ($key === POSTLOOPAJAX_DOMAIN.'-nonce') {
        return preg_replace('/[^a-zA-Z0-9_\-\. ]/', '', $postValue);
    } else if ($key === 'template') {  // trim
        return trim($postValue);
    } else {
        return null;
    }
}

/*
function getHtmlFromDatabaseValue($value) {
     return esc_html(stripslashes($value));
}
*/

function plugin_options() {

    $content_not_found = __('Content not found.',POSTLOOPAJAX_DOMAIN);
    $page = __('Page',POSTLOOPAJAX_DOMAIN);
    $posted_by = __('Posted by',POSTLOOPAJAX_DOMAIN);
    $category = __('Category',POSTLOOPAJAX_DOMAIN);
    $posts = __('posts',POSTLOOPAJAX_DOMAIN);
    $not_found = __('Not Found',POSTLOOPAJAX_DOMAIN);

$post_template = <<<EOD
<!--header-->
<div>
    <!-- <div style="overflow-y:auto; height:500px;"> -->
        <!--split-->
        <!--article-->
        <article id="post-%post_id%" %post_class%>
            <header class="entry-header">
                <h2 class="entry-title"><a href="%permalink%" rel="bookmark">%title%</a></h2>
            </header><!-- .entry-header -->
            <div class="entry-content">
                <!--
                %first_image(medium,%permalink%)%
                %excerpt%
                -->
                %content%
            </div><!-- .entry-content -->
            <footer class="entry-footer">
                <div>{$posted_by} <a href="%author_posts_link%">%author%</a> / %time%</div>
                <div>{$category}: %category_links%</div>
            </footer><!-- .entry-footer -->
        </article>
        <!--split-->
        <!--no article-->
        <article id="post-%post_id%" %post_class%>
            <header class="entry-header">
                <h2 class="entry-title">{$not_found}</h2>
            </header><!-- .entry-header -->
            <div class="entry-content">
                <p>{$content_not_found}</p>
            </div><!-- .entry-content -->
            <footer class="entry-footer">
            </footer><!-- .entry-footer -->
        </article>
        <!--split-->
        <!--footer-->
    <!-- </div> -->
    <div>{$page}: %paged% / %max_number_of_pages% (%number_of_posts% {$posts})</div>
    %pagination%
</div>
        <!--split-->
        <!--no article footer-->
    <!-- </div> -->
</div>
EOD;

$post_scroll_template = <<<EOD
<!--header-->
<div>
    <div style="overflow-y:auto; height:400px;">
        <!--split-->
        <!--article-->
        <article id="post-%post_id%" %post_class%>
            <header class="entry-header">
                <h2 class="entry-title"><a href="%permalink%" rel="bookmark">%title%</a></h2>
            </header><!-- .entry-header -->
            <div class="entry-content">
                <!--
                %first_image(medium,%permalink%)%
                %excerpt%
                -->
                %content%
            </div><!-- .entry-content -->
            <footer class="entry-footer">
                <div>{$posted_by} <a href="%author_posts_link%">%author%</a> / %time%</div>
                <div>{$category}: %category_links%</div>
            </footer><!-- .entry-footer -->
        </article>
        <!--split-->
        <!--no article-->
        <article id="post-%post_id%" %post_class%>
            <header class="entry-header">
                <h2 class="entry-title">{$not_found}</h2>
            </header><!-- .entry-header -->
            <div class="entry-content">
                <p>{$content_not_found}</p>
            </div><!-- .entry-content -->
            <footer class="entry-footer">
            </footer><!-- .entry-footer -->
        </article>
        <!--split-->
        <!--footer-->
    </div>
    <div>{$page}: %paged% / %max_number_of_pages% (%number_of_posts% {$posts})</div>
    %pagination%
</div>
        <!--split-->
        <!--no article footer-->
    </div>
</div>
EOD;

$excerpt_template = <<<EOD
<!--header-->
<div>
    <!-- <div style="overflow-y:auto; height:500px;"> -->
        <!--split-->
        <!--article-->
        <article id="post-%post_id%" %post_class%>
            <header class="entry-header">
                <h2 class="entry-title"><a href="%permalink%" rel="bookmark">%title%</a></h2>
            </header><!-- .entry-header -->
            <div class="entry-content">
                %first_image(medium,%permalink%)%
                %excerpt%
            </div><!-- .entry-content -->
            <footer class="entry-footer">
                <div>{$posted_by} <a href="%author_posts_link%">%author%</a> / %time%</div>
                <div>{$category}: %category_links%</div>
            </footer><!-- .entry-footer -->
        </article>
        <!--split-->
        <!--no article-->
        <article id="post-%post_id%" %post_class%>
            <header class="entry-header">
                <h2 class="entry-title">{$not_found}</h2>
            </header><!-- .entry-header -->
            <div class="entry-content">
                <p>{$content_not_found}</p>
            </div><!-- .entry-content -->
            <footer class="entry-footer">
            </footer><!-- .entry-footer -->
        </article>
        <!--split-->
        <!--footer-->
    <!-- </div> -->
    <div>{$page}: %paged% / %max_number_of_pages% (%number_of_posts% {$posts})</div>
    %pagination%
</div>
        <!--split-->
        <!--no article footer-->
    <!-- </div> -->
</div>
EOD;

$slider_template = <<<EOD
<!-- This is Image Slider. Need Custom-Field, that data type is Image and name is 'postloopajax_slider_image'. Set image to that and write Content at Post. Change size and color and more at here. Don't forget fixed height of Slider Block. -->
<!-- preload image, if need. --
<div style="position:absolute; z-index:0; width:1px; height:1px;"><img src="" /><img src="" /></div>
-->
<!--header-->
<div id="postloopajax-slider-%widget_number%">
    <!--split-->
    <!--article-->
    <style>
    #postloopajax-slider-%widget_number% {
        position: relative;
        height: 400px;
        overflow: hidden;
        background-image: url(%custom_field(image_url,postloopajax_slider_image,0,large)%);
        background-size: cover;
        background-position: 50% 50%;
    }
    #postloopajax-slider-%widget_number% img {
        width: 100%;
        height: 400px;
    }
    #postloopajax-slider-%widget_number% .post-loop-ajax-navigation {
        position: absolute;
        bottom: 8px;
        left: 0;
        right: 0;
    }
    #postloopajax-slider-%widget_number% .post-loop-ajax-navigation a {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        font-size: 0;
        line-height: 0;
        overflow: hidden;
        padding: 0;
        border: 1px solid #eee;
    }
    #postloopajax-slider-%widget_number% .post-loop-ajax-navigation a.current {
        background-color: #D1E8B5;
    }
    </style>
    <article id="post-%post_id%" %post_class%>
        <div class="entry-content" style="box-sizing:border-box;width:100%;padding:5%;color:#fff;font-size:17px;line-height:1.5;font-weight:800;text-shadow: 2px 2px 3px #333,-2px -2px 3px #333,-2px 2px 3px #333,2px -2px 3px #333;">
            %content%
        </div><!-- .entry-content -->
    </article>
    <!--split-->
    <!--no article-->
    <article id="post-%post_id%" %post_class%>
        <div class="entry-content">
            <p>contents not found</p>
        </div><!-- .entry-content -->
    </article>
    <!--split-->
    <!--footer-->
    %pagination%
</div>
    <!--split-->
    <!--no article footer-->
</div>
EOD;

$slider_object_fit_template = <<<EOD
<!-- This is Image Slider. Need Custom-Field, that data type is Image and name is 'postloopajax_slider_image'. Set image to that and write Content at Post. Change size and color and more at here. if need preload images. -->
<!-- preload image, if need. --
<div style="position:absolute; z-index:0; width:1px; height:1px;"><img src="" /><img src="" /></div>
-->
<!--header-->
<div id="postloopajax-slider-%widget_number%" style="position:relative;">
    <!--split-->
    <!--article-->
    <style>
    #postloopajax-slider-%widget_number% {
        position: relative;
        overflow: hidden;
        height: 400px;
    }
    #postloopajax-slider-%widget_number% .post-loop-ajax-navigation {
        position: absolute;
        z-index: 4;
        bottom: 8px;
        left: 0;
        right: 0;
    }
    #postloopajax-slider-%widget_number% .post-loop-ajax-navigation a {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        font-size: 0;
        line-height: 0;
        overflow: hidden;
        padding: 0;
        border: 1px solid #eee;
    }
    #postloopajax-slider-%widget_number% .post-loop-ajax-navigation a.current {
        background-color: #D1E8B5;
    }
    #postloopajax-slider-%widget_number% article {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    #postloopajax-slider-%widget_number% article .entry-content {
        box-sizing: border-box;
        position: absolute;
        z-index: 3;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        padding: 5%;
        color: #fff;
        font-size: 17px;
        line-height: 1.5;
        font-weight: 800;
        text-shadow: 2px 2px 3px #333, -2px -2px 3px #333, -2px 2px 3px #333, 2px -2px 3px #333;
    }
    #postloopajax-slider-%widget_number% article img {
        border-radius: 0;
        position:absolute;
        z-index:2;
        object-fit: cover;
        width: 100%;
        height: 400px;
    }
    </style>
    <article id="post-%post_id%" %post_class%>
        <img src="%custom_field(image_url,postloopajax_slider_image,0,large)%" />
        <div class="entry-content">
            %content%
        </div><!-- .entry-content -->
    </article>
    <!--split-->
    <!--no article-->
    <article id="post-%post_id%" %post_class%>
        <div class="entry-content">
            <p>contents not found</p>
        </div><!-- .entry-content -->
    </article>
    <!--split-->
    <!--footer-->
    %pagination%
</div>
    <!--split-->
    <!--no article footer-->
</div>
EOD;


    $options = get_option(POSTLOOPAJAX_DOMAIN);
    $templates = $options['templates'];
    if (!is_array($templates)) {
        $templates = array();
    }

    // default template
    $template_name = __('POST',POSTLOOPAJAX_DOMAIN);
    if (!array_key_exists($template_name, $templates)) {
        $templates[$template_name]['template'] = $post_template;
        $options['templates'] = $templates;
        update_option(POSTLOOPAJAX_DOMAIN, $options);
    }
    $template_name = __('POST SCROLL',POSTLOOPAJAX_DOMAIN);
    if (!array_key_exists($template_name, $templates)) {
        $templates[$template_name]['template'] = $post_scroll_template;
        $options['templates'] = $templates;
        update_option(POSTLOOPAJAX_DOMAIN, $options);
    }
    $template_name = __('EXCERPT',POSTLOOPAJAX_DOMAIN);
    if (!array_key_exists($template_name, $templates)) {
        $templates[$template_name]['template'] = $excerpt_template;
        $options['templates'] = $templates;
        update_option(POSTLOOPAJAX_DOMAIN, $options);
    }
    $template_name = __('IMAGE SLIDER',POSTLOOPAJAX_DOMAIN);
    if (!array_key_exists($template_name, $templates)) {
        $templates[$template_name]['template'] = $slider_template;
        $options['templates'] = $templates;
        update_option(POSTLOOPAJAX_DOMAIN, $options);
    }
    $template_name = __('IMAGE SLIDER OBJECT-FIT',POSTLOOPAJAX_DOMAIN);
    if (!array_key_exists($template_name, $templates)) {
        $templates[$template_name]['template'] = $slider_object_fit_template;
        $options['templates'] = $templates;
        update_option(POSTLOOPAJAX_DOMAIN, $options);
    }

    //print_r($_POST);

    if (getPostValueAdmin('submit_hidden') === 'Y') {
        if (!wp_verify_nonce(getPostValueAdmin(POSTLOOPAJAX_DOMAIN.'-nonce'), POSTLOOPAJAX_DOMAIN.'-nonce')) {
            die('Security check');
        } else if (getPostValueAdmin('cmd-load')) {
            $name = getPostValueAdmin('cmd-load');
            $template = $templates[$name]['template'];
        } else if (getPostValueAdmin('cmd-update')) {
            $name = getPostValueAdmin('template-name');
            $save = getPostValueAdmin('template-name-save');
            $template = getPostValueAdmin('template');
            if ($name != $save) {
                ?>
                <div class="error"><p><strong><?php _e("Can't update, template name is different.", POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            } else if (!$template) {
                ?>
                <div class="error"><p><strong><?php _e("Can't update, template is empty.", POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            } else {
                $options['templates'][$name]['template'] = $template; // before save
                update_option(POSTLOOPAJAX_DOMAIN, $options);
                $options = get_option(POSTLOOPAJAX_DOMAIN);
                $templates = $options['templates'];
                ?>
                <div class="updated"><p><strong><?php _e('Updated.', POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            }
        } else if (getPostValueAdmin('cmd-add-new')) {
            $name = getPostValueAdmin('template-name');
            $save = getPostValueAdmin('template-name-save');
            $template = getPostValueAdmin('template');
            if (array_key_exists($name, $templates)) {
                ?>
                <div class="error"><p><strong><?php _e("Can't add, template name was exists.", POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            } else if (!$template) {
                ?>
                <div class="error"><p><strong><?php _e("Can't add, template is empty.", POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            } else {
                $options['templates'][$name]['template'] = $template; // before save
                update_option(POSTLOOPAJAX_DOMAIN, $options);
                $options = get_option(POSTLOOPAJAX_DOMAIN);
                $templates = $options['templates'];
                ?>
                <div class="updated"><p><strong><?php _e('Added.', POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            }
        } else if (getPostValueAdmin('confirm-delete') && getPostValueAdmin('cmd-delete')) {
            $name = getPostValueAdmin('template-name');
            $save = getPostValueAdmin('template-name-save');
            $template = getPostValueAdmin('template');
            if (!array_key_exists($name, $templates)) {
                ?>
                <div class="error"><p><strong><?php _e("Can't delete, template name was not exists.", POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            } else {
                unset($templates[$name]);
                $options['templates'] = $templates;
                update_option(POSTLOOPAJAX_DOMAIN, $options);
                $options = get_option(POSTLOOPAJAX_DOMAIN);
                $templates = $options['templates'];
                ?>
                <div class="updated"><p><strong><?php _e('Deleted.', POSTLOOPAJAX_DOMAIN); ?></strong></p></div>
                <?php
            }
        } else {
            $name = getPostValueAdmin('template-name');
            $template = getPostValueAdmin('template');
        }
    }
    ?>

    <div class="wrap <?php echo POSTLOOPAJAX_DOMAIN; ?>_options">
        <h2><?php _e('Post Loop Ajax Widget Templates', POSTLOOPAJAX_DOMAIN); ?><div class="support-link"><a class="button" href="https://wordpress.org/plugins/post-loop-ajax/" target="_blank">WordPress.org</a>&nbsp;<form style="float:right;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="P8LCVREFDKWFW"><button type="submit" class="button" border="0" name="submit"><?php _e('donate', POSTLOOPAJAX_DOMAIN); ?></button></form></div></h2>


        <form name="<?php echo POSTLOOPAJAX_DOMAIN; ?>_admin" method="post" action="<?php echo esc_url(str_replace('%7E', '~', $_SERVER['REQUEST_URI'])); ?>" enctype="multipart/form-data">
            <input type="hidden" name="submit_hidden" value="Y" />
            <input type="hidden" name="_doublequotation" value='"' /><!-- doublequotation test -->
            <?php
            // nonce を生成し、アクションを実行するリンクのクエリ変数に追加。
            echo '<input type="hidden" name="'.POSTLOOPAJAX_DOMAIN.'-nonce" value="'.wp_create_nonce(POSTLOOPAJAX_DOMAIN.'-nonce').'" />';
            ?>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <?php /* _e('General'); */ ?>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body" style="width:100%">
                            <?php _e('Template Name',POSTLOOPAJAX_DOMAIN); ?>: <input type="text" size="40" name="template-name" value="<?php echo $name; ?>" /><input type="hidden" size="40" name="template-name-save" value="<?php echo $name; ?>" />
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body" style="width:100%">
                            <?php /* <h3><?php _e('General', POSTLOOPAJAX_DOMAIN); ?></h3> */ ?>
                            <br /><textarea name="template" cols="70" rows="12"><?php echo esc_textarea($template); ?></textarea>
                            <hr />
                            <input type="checkbox" name="confirm-delete" value="1" />
                            <button type="submit" name="cmd-delete" class="btn-red" value="1"><?php _e('Delete', POSTLOOPAJAX_DOMAIN) ?></button>
                            <button type="submit" name="cmd-add-new" class="btn-green" value="1"><?php _e('Add New', POSTLOOPAJAX_DOMAIN) ?></button>
                            <button type="submit" name="cmd-update" class="btn-blue" value="1"><?php _e('Update', POSTLOOPAJAX_DOMAIN) ?></button>
                            <?php echo ' <div>'.__('see',POSTLOOPAJAX_DOMAIN).': <a href="'.POSTLOOPAJAX_PLUGIN_URL.'/readme.txt">readme.txt</a></div>'; ?>
                        </div>
                    </div>
                    <hr />
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body" style="width:100%">
                            <?php
                            foreach ($templates as $name => $template) {
                                echo '<button type="submit" class="button" name="cmd-load" value="'.$name.'">'.$name.'</button> ';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php
}


