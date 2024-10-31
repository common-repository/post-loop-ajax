<?php
/*
  Plugin Name: Post Loop Ajax
  Plugin URI: https://wordpress.org/plugins/post-loop-ajax/
  Description: Post Loop and Pagination Widget. Multiple Posts Loop placement. Auto page feed and at random.
  Author: AI.Takeuchi
  Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P8LCVREFDKWFW
  Version: 1.60
  Author URI:
 */

// The magic constant __DIR__ is only available as of PHP v.5.3.0.
if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

define('POSTLOOPAJAX_DOMAIN', 'postloopajax');

define('POSTLOOPAJAX_PLUGIN_URL', rtrim(esc_url(plugin_dir_url(__FILE__)), '/'));
define('POSTLOOPAJAX_PLUGIN_DIR', rtrim(__DIR__, '/'));

load_textdomain(POSTLOOPAJAX_DOMAIN, __DIR__ . '/language/' . POSTLOOPAJAX_DOMAIN . '-' . get_locale() . '.mo');

$postloopajax_effects = array(
    'fade',
    'blind',
    'explode',
    'puff',
    //'size',
    'bounce',
    'pulsate',
    'slide',
    'clip',
    'fold',
    'scale',
    //'transfer',
    'drop',
    'highlight',
    'shake',
);

if (is_admin()) {
    require_once(__DIR__.'/admin.php');
}

register_activation_hook(__FILE__, 'PostLoopAjax_activate');
function PostLoopAjax_activate(){
    register_uninstall_hook(__FILE__, 'PostLoopAjax_uninstall');
    add_option('postloopajax');
}

// And here goes the uninstallation function:
function PostLoopAjax_uninstall(){
    //  codes to perform during unistallation
    delete_option('postloopajax');
}

function PostLoopAjax_wp_enqueue_scripts() {
    // Doesn't execute admin.
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery.cookie', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery.cookie.js', array('jquery'), '1.4.1', true);
    wp_enqueue_script('jquery-ui', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery-ui.min.js', array('jquery'));

    $postloopajaxlanguage = get_locale();
    wp_enqueue_style('post-loop-ajax', POSTLOOPAJAX_PLUGIN_URL.'/css/post-loop-ajax.css');
    ?>
    <script type="text/javascript">
        <!--
        var postloopajax_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var postloopajax_ajax_nonce = '<?php echo wp_create_nonce('postloopajax'); ?>';
        var postloopajaxServer = "<?php echo home_url() . '/'; ?>";
        var postloopajaxBaseUrl = "<?php echo POSTLOOPAJAX_PLUGIN_URL; ?>";
        var postloopajaxlanguage = "<?php echo $postloopajaxlanguage; ?>";   // en, ja
        // -->
    </script>
    <?php
    wp_enqueue_script('postloopajax', POSTLOOPAJAX_PLUGIN_URL.'/js/postloopajax.js', array('jquery'));
}

add_action('wp_enqueue_scripts', 'PostLoopAjax_wp_enqueue_scripts');

function PostLoopAjax_admin_enqueue_scripts() {
    // Execute admin.
    wp_enqueue_style('post-loop-ajax-admin', POSTLOOPAJAX_PLUGIN_URL.'/css/post-loop-ajax-admin.css');

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery.cookie', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery.cookie.js', array('jquery'), '1.4.1', true);
    wp_enqueue_script('jquery-ui', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery-ui.min.js', array('jquery'));
    wp_enqueue_script('autocomplete', POSTLOOPAJAX_PLUGIN_URL.'/js/autocomplete.js', array('jquery', 'jquery-ui'));
}

add_action('admin_enqueue_scripts', 'PostLoopAjax_admin_enqueue_scripts');


class PostLoopAjax extends WP_Widget{

    function __construct() {
        parent::__construct(false, $name = 'Post Loop Ajax', array('description' => 'Post Loop and pagination Widget. Use javascript and cookie.'));

        //register_activation_hook(__FILE__, 'PostLoopAjax_activate');
        register_activation_hook(__FILE__, array(__CLASS__, 'PostLoopAjax_activate'));

        // add to admin menu.
        add_action('plugin_row_meta', array(__CLASS__, 'plugin_row_meta'), 10, 4);

        //add_action('wp_enqueue_scripts', array(__CLASS__, 'wp_enqueue_scripts'));
        //add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));

        require_once('server.php');
    }

    function plugin_row_meta($plugin_meta, $plugin_file, $plugin_data, $status) {
        if ( plugin_basename( __FILE__ ) != $plugin_file ) {
            return $plugin_meta;
        }
        //print_r($plugin_meta);
        $links = $plugin_meta;
        $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P8LCVREFDKWFW">'.__('donate',POSTLOOPAJAX_DOMAIN).'</a>';
        return $links;
    }

    function PostLoopAjax_activate(){
        //register_uninstall_hook(__FILE__, 'PostLoopAjax_uninstall');
        register_uninstall_hook(__FILE__, array(__CLASS__, 'PostLoopAjax_uninstall'));
        add_option('postloopajax');
    }

    // And here goes the uninstallation function:
    function PostLoopAjax_uninstall(){
        //  codes to perform during unistallation
        delete_option('postloopajax');
    }

    /*
    function admin_enqueue_scripts() {
        // Execute admin.
        wp_enqueue_style('post-loop-ajax-admin', POSTLOOPAJAX_PLUGIN_URL.'/css/post-loop-ajax-admin.css');

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery.cookie', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery.cookie.js', array('jquery'), '1.4.1', true);
        wp_enqueue_script('jquery-ui', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery-ui.min.js', array('jquery'));
        wp_enqueue_script('autocomplete', POSTLOOPAJAX_PLUGIN_URL.'/js/autocomplete.js', array('jquery', 'jquery-ui'));
    }
    */

    /*
    function wp_enqueue_scripts() {
        // Doesn't execute admin.
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery.cookie', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery.cookie.js', array('jquery'), '1.4.1', true);
        wp_enqueue_script('jquery-ui', POSTLOOPAJAX_PLUGIN_URL.'/js/jquery-ui.min.js', array('jquery'));

        $postloopajaxlanguage = get_locale();
        wp_enqueue_style('post-loop-ajax', POSTLOOPAJAX_PLUGIN_URL.'/css/post-loop-ajax.css');
        ?>
        <script type="text/javascript">
            <!--
            var postloopajax_ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var postloopajax_ajax_nonce = '<?php echo wp_create_nonce('postloopajax'); ?>';
            var postloopajaxServer = "<?php echo home_url() . '/'; ?>";
            var postloopajaxBaseUrl = "<?php echo POSTLOOPAJAX_PLUGIN_URL; ?>";
            var postloopajaxlanguage = "<?php echo $postloopajaxlanguage; ?>";   // en, ja
            // -->
        </script>
        <?php
        wp_enqueue_script('postloopajax', POSTLOOPAJAX_PLUGIN_URL.'/js/postloopajax.js', array('jquery'));
    }
    */

    function widget($args, $instance) {
        //print_r($instance);
        //echo $args['widget_id'];
        //print_r($args);
        extract($args);
        require_once('get_contents.php');

        $title = htmlspecialchars_decode(apply_filters('widget_title', $instance['title']));
        //$title = apply_filters('widget_title', $title, $instance, $this->id_base);

        if ($instance['put_before_and_after_widget']) {
            echo $before_widget;
        }
        //if ($title) echo $before_title . $title . $after_title;
        if ($title && $instance['display_title']) echo $before_title . $title . $after_title;

        $f = preg_split('/\s+/', $instance['class']);
        $classes = join(' ', $f);

        //echo '<div>widget_number: '.$this->number.'</div>';
        //echo '<div>widget_number: '.$instance['widget_number'].'</div>';

        //list($contents, $loopScript, $instance, $paged) = PostLoopAjaxGetArticle($instance['widget_number'], $instance, -1, true, false); // widget number, instance, paged, get request
        list($contents, $loopScript, $instance) = PostLoopAjaxGetArticle($instance['widget_number'], $instance, -1, true, false); // widget number, instance, paged, get request

        $aname = POSTLOOPAJAX_DOMAIN.'_'.$instance['widget_number'];
        echo '<a name="'.$aname.'" id="'.$aname.'" class="'.POSTLOOPAJAX_DOMAIN.'-anchor"></a>';
        echo '<div class="'.$classes.' '.array_pop($f).'-'.$instance['widget_number'].'">';
        echo $contents;
        echo '</div>';
        echo $loopScript;

        // debug --------------------------------------
        //echo $this->id_base;
        //echo '<div>option_name: '.$this->option_name.'</div>';
        //echo $this->number;
        //print_r($instance);
        //echo 'Hello ' . htmlspecialchars($instance['name']) . '!';

        if ($instance['put_before_and_after_widget']) {
            echo $after_widget;
        }
    }



    function update($new_instance, $old_instance) {
        //$instance['widget_number'] = strip_tags($new_instance['widget_number']);
        //$instance['widget_number_free'] = strip_tags($new_instance['widget_number_free']);

        $instance['title'] = strip_tags($new_instance['title'], '<br><strong><i><b><span><div><font>');
        $instance['display_title'] = strip_tags($new_instance['display_title']);
        $instance['class'] = strip_tags($new_instance['class']);

        $instance['posts_per_page'] = strip_tags($new_instance['posts_per_page']);
        $instance['max_page_number'] = strip_tags($new_instance['max_page_number']);
        $instance['qs_post_type_names'] = strip_tags($new_instance['qs_post_type_names']);
        $instance['qs_category_names'] = strip_tags($new_instance['qs_category_names']);
        $instance['qs_without_category_names'] = strip_tags($new_instance['qs_without_category_names']);
        $instance['qs_additional'] = strip_tags($new_instance['qs_additional']);

        $qss = array();
        if ($instance['qs_post_type_names']) {
            $qss[] = 'post_type=('.preg_replace('/,\s*/', ',', preg_replace('/,\s*$/', '', $instance['qs_post_type_names'])).')';
        }

        $qs_category_names = explode(',', strip_tags($new_instance['qs_category_names']));
        if ($new_instance['qs_category_names'] && !empty($qs_category_names)) {
            $cat_slugs = array();
            foreach ($qs_category_names as $key => $cat_name) {
                $cat_ID = get_cat_ID(trim($cat_name));
                if ($cat_ID) {
                    $obj = get_category($cat_ID);
                    $cat_slugs[] = $obj->slug;
                } else {
                    $cat_slugs[] = $cat_name; // raw value
                }
            }
            if (!empty($cat_slugs)) {
                $qss[] = 'category_name='.preg_replace('/,\s*$/', '', join(',',$cat_slugs));
            }
        }
        $instance['qs'] = join('&', $qss);

        $qs_without_category_names = explode(',', strip_tags($new_instance['qs_without_category_names']));
        if ($new_instance['qs_without_category_names'] && !empty($qs_without_category_names)) {
            $cat_ids = array();
            foreach ($qs_without_category_names as $key => $cat_name) {
                $id = get_cat_ID(trim($cat_name));
                if ($id > 0) {
                    $cat_ids[] = $id;
                }
            }
            if (!empty($cat_ids)) {
                //$qss[] = 'category__not_in='.join(',',$cat_ids);
                $qss[] = 'cat=-'.join(',-',$cat_ids);
            }
        }
        if ($instance['qs_additional']) {
            $qss[] = $instance['qs_additional'];
        }
        $instance['qs'] = join('&', $qss);

        // string to array
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
        $instance['qs_array'] = $qs_array;

        // convert json code to query string array.
        $instance['query_code'] = strip_tags($new_instance['query_code']);
        $hoge = strip_tags(str_replace("\n","",$new_instance['query_code']));
        $instance['query_code_array'] = json_decode($hoge, true);


        // excerpt
        $instance['excerpt_char_length'] = strip_tags($new_instance['excerpt_char_length']);
        $instance['excerpt_ellipsis'] = strip_tags($new_instance['excerpt_ellipsis']);
        $instance['excerpt_more'] = strip_tags($new_instance['excerpt_more']);

        $instance['cookie_expires'] = strip_tags($new_instance['cookie_expires']);
        $instance['time_format'] = strip_tags($new_instance['time_format']);//'F jS, Y'
        $instance['template_name'] = /*strip_tags*/($new_instance['template_name']);
        $instance['article'] = /*strip_tags*/($new_instance['article']);

        $instance['put_before_and_after_widget'] = strip_tags($new_instance['put_before_and_after_widget']);
        $instance['need_reload_page'] = strip_tags($new_instance['need_reload_page']);
        $instance['remove_shortcode_not_work'] = strip_tags($new_instance['remove_shortcode_not_work']);
        $instance['scroll_content_top_after_reloaded'] = strip_tags($new_instance['scroll_content_top_after_reloaded']);
        $instance['page_at_random'] = strip_tags($new_instance['page_at_random']);
        // pagination
        $instance['pagination_type'] = strip_tags($new_instance['pagination_type']);
        $instance['navi_string_prev'] = /*strip_tags*/($new_instance['navi_string_prev']);
        $instance['navi_string_next'] = /*strip_tags*/($new_instance['navi_string_next']);
        $instance['hide_prev_and_next_button'] = strip_tags($new_instance['hide_prev_and_next_button']);
        // scroll
        $instance['scroll_speed'] = strip_tags($new_instance['scroll_speed']);
        $instance['scroll_offset_top'] = strip_tags($new_instance['scroll_offset_top']);
        $instance['scroll_offset_top_margin'] = strip_tags($new_instance['scroll_offset_top_margin']);

        // effect
        $instance['do_not_display_loading_image'] = strip_tags($new_instance['do_not_display_loading_image']);
        $instance['loop'] = strip_tags($new_instance['loop']);
        $instance['fade_time'] = strip_tags($new_instance['fade_time']);

        // create widget_number
        if (!array_key_exists('widget_number', $new_instance) || !trim(strip_tags($new_instance['widget_number']))) {
            $opts = get_option('postloopajax');
            $keys = array_keys($opts);
            rsort($keys);
            $instance['widget_number'] = intval(array_shift($keys)) + 1;
            //echo 'new widget number: ' + $instance['widget_number'];
        } else {
            $instance['widget_number'] = trim(strip_tags($new_instance['widget_number']));
        }


        global $postloopajax_effects;
        //
        $ea = array();
        foreach ($postloopajax_effects as $i => $name) {
            $field_name = 'effect_front_'.$name;
            if (strip_tags($new_instance[$field_name]) == $name) {
                $ea[$name] = $name;
            }
        }
        $instance['effect_front_types'] = $ea;
        //
        $la = $postloopajax_effects;
        $la['same as front'] = 'same as front';
        $ea = array();
        foreach ($la as $i => $name) {
            $field_name = 'effect_end_'.$name;
            if (strip_tags($new_instance[$field_name]) == $name) {
                $ea[$name] = $name;
            }
        }
        $instance['effect_end_types'] = $ea;

        // save instance as options
        // can't get instance if use "multiwidget" and "Ajax Request" case. for the reason, need get instance from option.
        $opts = get_option('postloopajax');
        if (!is_array($opts)) {
            $opts = array();
        }
        // save if get request
        //print_r($instance);
        //print_r($opts);
        //echo '<div>widget_number: '.$widget_number.'</div>';
        $opts[$instance['widget_number']] = $instance;
        //$opts = array_merge($opts, array($widget_number => $instance));
        update_option('postloopajax', $opts);
        //$opts = get_option('postloopajax');
        //print_r($opts);
        //--

        return $instance;
    }

    function getSelectTag($name, $options, $defaultValue) {
        $result = '<select name="'.$name.'">';
        foreach ($options as $value => $string) {
            if ($value == $defaultValue) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            $result .= '<option value="'.$value.'" '.$selected.'>'.$string.'</option>';
        }
        return $result.'</select>';
    }

    function getTemplateTag($name, $defaultName) {
        $options = get_option(POSTLOOPAJAX_DOMAIN);
        $templates = $options['templates'];
        if (!is_array($templates)) {
            $templates = array();
        }

        $use_this = __("Use this:",POSTLOOPAJAX_DOMAIN);
        $result = '<select name="'.$name.'">';
        if ($defaultName == $use_this) {
            $selected = 'selected="selected"';
        } else {
            $selected = '';
        }
        $result .= '<option value="'.$use_this.'" '.$selected.'>'.$use_this.'</option>';
        foreach ($templates as $name => $value) {
            $template = $value['template'];
            if ($name == $defaultName) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            $result .= '<option value="'.$name.'" '.$selected.'>'.$name.'</option>';
        }
        return $result.'</select>';
    }

    function form($instance) {
        // nonce
        //echo '<input type="hidden" name="'.POSTLOOPAJAX_DOMAIN.'-nonce" value="'.wp_create_nonce(POSTLOOPAJAX_DOMAIN.'-nonce').'" />';

        $content_not_found = __('Content not found.',POSTLOOPAJAX_DOMAIN);
        $page = __('Page',POSTLOOPAJAX_DOMAIN);
        $posted_by = __('Posted by',POSTLOOPAJAX_DOMAIN);
        $category = __('Category',POSTLOOPAJAX_DOMAIN);
        $posts = __('posts',POSTLOOPAJAX_DOMAIN);
        $not_found = __('Not Found',POSTLOOPAJAX_DOMAIN);
$art = <<<EOD
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
                %first_image_medium(%permalink%)%
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

        $def = array('cookie_expires' => '7', 'pagination_type' => 99, 'qs_post_type_names' => 'post', 'posts_per_page' => 10, 'max_page_number' => 0, 'time_format' => __('F jS, Y',POSTLOOPAJAX_DOMAIN), 'article' => $art, 'class' => 'post-loop-ajax-use-default-css', 'loop' => 0, 'fade_time' => 500, 'navi_string_prev' => '', 'navi_string_next' => '', 'excerpt_char_length' => 140, 'excerpt_ellipsis' => '...', 'excerpt_more' => __('Read More',POSTLOOPAJAX_DOMAIN), 'display_title' => 1, 'template_name' => __("Use this:",POSTLOOPAJAX_DOMAIN), 'scroll_speed' => 200, 'scroll_offset_top' => -150, 'scroll_offset_top_margin' => 20, 'effect_type_front' => 'fade', 'effect_type_end' => 'fade', 'scroll_content_top_after_reloaded' => 1, '
remove_shortcode_not_work' => 1,);
        foreach ($def as $key => $value) {
            if (!array_key_exists($key, $instance)) {
            //echo 'key:'.$key;
            //exit;
            $instance[$key] = $value;
            }
        }



        $defaultValue = intval($instance['pagination_type']);
        if ($defaultValue < 0 || ($defaultValue > 80 && $defaultValue != 99)) {
            $defaultValue = 1;
        }
        $paginationType = $this->getSelectTag($this->get_field_name('pagination_type'), array(0 => __('nothing',POSTLOOPAJAX_DOMAIN), 1 => __('normal',POSTLOOPAJAX_DOMAIN), 2 => __('lite',POSTLOOPAJAX_DOMAIN), 3 => __('prev and next only',POSTLOOPAJAX_DOMAIN), 4 => __('full',POSTLOOPAJAX_DOMAIN), 99 => __('multi',POSTLOOPAJAX_DOMAIN)), $defaultValue);


        echo '<div class="post-loop-ajax-widget-settings">';

        echo '<div>'.__('system',POSTLOOPAJAX_DOMAIN).' [<a id="'.$this->get_field_id('postloopajax-a-system').'" href="#" onclick="console.log(\'system\'); if(jQuery(\'#'.$this->get_field_id('postloopajax-block-system').'\').css(\'display\') == \'none\'){jQuery(\'#'.$this->get_field_id('postloopajax-block-system').'\').css(\'display\',\'block\');jQuery(\'#'.$this->get_field_id('postloopajax-a-system').'\').text(\'-\');}else{jQuery(\'#'.$this->get_field_id('postloopajax-block-system').'\').css(\'display\',\'none\');jQuery(\'#'.$this->get_field_id('postloopajax-a-system').'\').text(\'+\');} return false;">+</a>]</div>';
        echo '<div id="'.$this->get_field_id('postloopajax-block-system').'" style="display:none; border:2px dotted #ffcc55; padding: 4px;">';

        // widget number
        echo '<div>'.__('unique id',POSTLOOPAJAX_DOMAIN).': <input class="w30" id="'.$this->get_field_id('widget_number').'" name="' . $this->get_field_name('widget_number') . '" type="text" value="' . $instance['widget_number'] . '" /></div>';
        /*
        echo '<div>'.__('unique id',POSTLOOPAJAX_DOMAIN).': <input class="w30" readonly="readonly" value="'.$instance['widget_number'].'" /></div>';
        */

        echo '<div>'.__('remember page number until cookie expires',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('cookie_expires') . '" type="text" value="' . $instance['cookie_expires'] . '" /> '.__('days',POSTLOOPAJAX_DOMAIN).'</div>';
        if ($instance['remove_shortcode_not_work']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('remove_shortcode_not_work') . '" id="'.$this->get_field_id('remove_shortcode_not_work').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('remove_shortcode_not_work').'">'.__("remove the shortcode when not work",POSTLOOPAJAX_DOMAIN).'</label></div>';
        echo '</div><!-- .#postloopajax-block-system -->';

        echo '<hr />';

        if ($instance['display_title']) $checked = 'checked="checked"'; else $checked = '';
  		echo '<div>'.__('Title:',POSTLOOPAJAX_DOMAIN).'<br /><input class="w80" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_html($instance['title']).'" /> <input name="' . $this->get_field_name('display_title') . '" id="'.$this->get_field_id('display_title').'" type="checkbox" value="1" '.$checked.' /></div>';

        if ($instance['put_before_and_after_widget']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('put_before_and_after_widget') . '" id="'.$this->get_field_id('put_before_and_after_widget').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('put_before_and_after_widget').'">'.__('put before and after widget html tag',POSTLOOPAJAX_DOMAIN).'</label></div>';

        if ($instance['need_reload_page']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('need_reload_page') . '" id="'.$this->get_field_id('need_reload_page').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('need_reload_page').'">'.__('need reload page',POSTLOOPAJAX_DOMAIN).'</label></div>';

        if ($instance['scroll_content_top_after_reloaded']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('scroll_content_top_after_reloaded') . '" id="'.$this->get_field_id('scroll_content_top_after_reloaded').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('scroll_content_top_after_reloaded').'">'.__('scroll to content top after reloaded',POSTLOOPAJAX_DOMAIN).'</label></div>';

        echo '<hr />';

        echo '<div>class: (<a href="'.POSTLOOPAJAX_PLUGIN_URL.'/css/post-loop-ajax.css" target="_blank">'.__('see: default css',POSTLOOPAJAX_DOMAIN).'</a>)<br /><input class="w100" name="' . $this->get_field_name('class') . '" type="text" value="' . $instance['class'] . '" /></div>';

        echo '<hr />';
        echo '<div style="font-weight:bold;">'.__('display posts',POSTLOOPAJAX_DOMAIN).'</div>';

        echo '<div>'.__('post type name, separated by comma',POSTLOOPAJAX_DOMAIN).':<br /><input class="w100" name="' . $this->get_field_name('qs_post_type_names') . '" id="'.$this->get_field_id('qs_post_type_names').'" type="text" value="' . $instance['qs_post_type_names'] . '" /></div>';
        echo '<div>'.__('category name, separated by comma',POSTLOOPAJAX_DOMAIN).':<br /><input class="w100" name="' . $this->get_field_name('qs_category_names') . '" id="'.$this->get_field_id('qs_category_names').'" type="text" value="' . $instance['qs_category_names'] . '" /></div>';
        echo '<div>'.__('without category name, separated by comma',POSTLOOPAJAX_DOMAIN).':<br /><input class="w100" name="' . $this->get_field_name('qs_without_category_names') . '" id="'.$this->get_field_id('qs_without_category_names').'" type="text" value="' . $instance['qs_without_category_names'] . '" /></div>';
        echo '<div>'.__('additional',POSTLOOPAJAX_DOMAIN).' (key=value&key2=value2...) [<a href="https://codex.wordpress.org/Class_Reference/WP_Query" target="_blank">?</a>]'.':<br /><input class="w100" name="' . $this->get_field_name('qs_additional') . '" type="text" value="' . $instance['qs_additional'] . '" /></div>';

        echo '<div>'.__('to result in (query after assembly)',POSTLOOPAJAX_DOMAIN).':<br /><input class="w100" name="' . $this->get_field_name('qs') . '" type="text" value="' . $instance['qs'] . '" readonly="readonly" /></div>';

        echo '<div style="margin-top:8px;">'.__('enter query code, json format. it is prioritize than above.',POSTLOOPAJAX_DOMAIN).'</div>';

        if ($instance['query_code'] && !$instance['query_code_array']) {
            echo '<div style="color:red;">'.__('query code format error.', POSTLOOPAJAX_DOMAIN).'</div>';
        }
        echo '<div><textarea name="' . $this->get_field_name('query_code') . '">' . $instance['query_code'] . '</textarea></div>';
        //print_r($instance['query_code_array']); // debug: put assembled array.

        echo '<hr />';

        echo '<div>'.__('posts per page',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('posts_per_page') . '" type="text" value="' . $instance['posts_per_page'] . '" /></div>';

        echo '<div>'.__('max page number',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('max_page_number') . '" type="text" value="' . $instance['max_page_number'] . '" /></div>';

        echo '<hr />';

        echo '<div>'.__('excerpt char length',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('excerpt_char_length') . '" type="text" value="' . $instance['excerpt_char_length'] . '" /></div>';
        echo '<div>'.__('excerpt ellipsis',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('excerpt_ellipsis') . '" type="text" value="' . $instance['excerpt_ellipsis'] . '" /></div>';
        echo '<div>'.__('excerpt more',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('excerpt_more') . '" type="text" value="' . $instance['excerpt_more'] . '" /></div>';

        echo '<hr />';

        echo '<div>'.__('date time format',POSTLOOPAJAX_DOMAIN).' [<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">?</a>]:<br /><input name="' . $this->get_field_name('time_format') . '" type="text" value="' . $instance['time_format'] . '" /></div>';

        echo '<hr />';

        echo '<div>'.__('pagination type (mult: for responsive)',POSTLOOPAJAX_DOMAIN).':<br />'.$paginationType.'</div>';
        echo '<div>'.__('pagination previous string',POSTLOOPAJAX_DOMAIN).':<br /><input class="w100" name="' . $this->get_field_name('navi_string_prev') . '" type="text" value="' . esc_html($instance['navi_string_prev']) . '" /></div>';
        echo '<div>'.__('pagination next string',POSTLOOPAJAX_DOMAIN).':<br /><input class="w100" name="' . $this->get_field_name('navi_string_next') . '" type="text" value="' . esc_html( $instance['navi_string_next']) . '" /></div>';
        if ($instance['hide_prev_and_next_button']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('hide_prev_and_next_button') . '" id="'.$this->get_field_id('hide_prev_and_next_button').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('hide_prev_and_next_button').'">'.__('hide prev and next button',POSTLOOPAJAX_DOMAIN).'</label></div>';

        echo '<hr />';

        echo '<div style="font-weight:bold;">'.__("scroll to content top after rewrite content by ajax, without while loop enabled.",POSTLOOPAJAX_DOMAIN).'</div>';
        echo '<div>'.__('scroll speed (-1: disabled)',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('scroll_speed') . '" type="text" value="' . $instance['scroll_speed'] . '" /> '.__('msec',POSTLOOPAJAX_DOMAIN).'</div>';
        echo '<div>'.__('scroll offset top',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('scroll_offset_top') . '" type="text" value="' . $instance['scroll_offset_top'] . '" /> px</div>';
        echo '<div>'.__("don't scroll if scroll position less than this margin",POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('scroll_offset_top_margin') . '" type="text" value="' . $instance['scroll_offset_top_margin'] . '" /> px</div>';

        echo '<hr />';

        echo '<div style="font-weight:bold;">'.__("auto loop contents.",POSTLOOPAJAX_DOMAIN).'</div>';
        echo '<div>'.__('loop interval time (0: disabled)',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('loop') . '" type="text" value="' . $instance['loop'] . '" /> '.__('sec',POSTLOOPAJAX_DOMAIN).'</div>';
        echo '<div>'.__('time until changed contents',POSTLOOPAJAX_DOMAIN).':<br /><input name="' . $this->get_field_name('fade_time') . '" type="text" value="' . $instance['fade_time'] . '" /> '.__('msec',POSTLOOPAJAX_DOMAIN).'</div>';
        if ($instance['page_at_random']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('page_at_random') . '" id="'.$this->get_field_id('page_at_random').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('page_at_random').'">'.__('page at random',POSTLOOPAJAX_DOMAIN).'</label></div>';

        echo '<hr />';

        echo '<div>'.__('effects',POSTLOOPAJAX_DOMAIN).' [<a id="'.$this->get_field_id('postloopajax-a-effects').'" href="#" onclick="console.log(\'effects\'); if(jQuery(\'#'.$this->get_field_id('postloopajax-block-effects').'\').css(\'display\') == \'none\'){jQuery(\'#'.$this->get_field_id('postloopajax-block-effects').'\').css(\'display\',\'block\');jQuery(\'#'.$this->get_field_id('postloopajax-a-effects').'\').text(\'-\');}else{jQuery(\'#'.$this->get_field_id('postloopajax-block-effects').'\').css(\'display\',\'none\');jQuery(\'#'.$this->get_field_id('postloopajax-a-effects').'\').text(\'+\');} return false;">+</a>]</div>';
        echo '<div id="'.$this->get_field_id('postloopajax-block-effects').'" style="display:none; border:2px dotted #ffcc55; padding: 4px;">';

        if ($instance['do_not_display_loading_image']) $checked = 'checked="checked"'; else $checked = '';
        echo '<div><input name="' . $this->get_field_name('do_not_display_loading_image') . '" id="'.$this->get_field_id('do_not_display_loading_image').'" type="checkbox" value="1" '.$checked.' /> <label for="'.$this->get_field_id('do_not_display_loading_image').'">'.__("don't display loading image.",POSTLOOPAJAX_DOMAIN).'</label></div>';

        echo '<hr />';

        echo '<div><span style="font-weight:bold;">'.__('front',POSTLOOPAJAX_DOMAIN).'</span><br />';
        global $postloopajax_effects;
        foreach ($postloopajax_effects as $i => $name) {
            $field_name = 'effect_front_'.$name;
            if ($instance['effect_front_types'][$name] == $name) $checked = 'checked="checked"'; else $checked = '';
            echo ' <input name="' . $this->get_field_name($field_name) . '" id="'.$this->get_field_id($field_name).'" type="checkbox" value="'.$name.'" '.$checked.' /><label for="'.$this->get_field_id($field_name).'">'.$name.'</label> ';
        }
        echo '</div>';

        echo '<hr />';

        echo '<div><span style="font-weight:bold;">'.__('end',POSTLOOPAJAX_DOMAIN).'</span><br />';
        $ea = $postloopajax_effects;
        $ea[] = 'same as front';
        foreach ($ea as $i => $name) {
            $field_name = 'effect_end_'.$name;
            if ($instance['effect_end_types'][$name] == $name) $checked = 'checked="checked"'; else $checked = '';
            echo ' <input name="' . $this->get_field_name($field_name) . '" id="'.$this->get_field_id($field_name).'" type="checkbox" value="'.$name.'" '.$checked.' /><label for="'.$this->get_field_id($field_name).'">'.$name.'</label> ';
        }
        echo '</div>';
        echo '</div><!-- .#postloopajax-block-effects -->';

        echo '<hr />';

        echo '<div>'.__('template',POSTLOOPAJAX_DOMAIN).' '.$this->getTemplateTag($this->get_field_name('template_name'), $instance['template_name']).' <a href="'.admin_url('options-general.php?page=postloopajax-settings.php').'">'.__('edit template',POSTLOOPAJAX_DOMAIN).'</a><br /><textarea name="' . $this->get_field_name('article') . '">' . $instance['article'] . '</textarea></div>';

        echo '<hr />';

        echo '<div>'.__('see',POSTLOOPAJAX_DOMAIN).': <a href="'.POSTLOOPAJAX_PLUGIN_URL.'/readme.txt">readme.txt</a></div>';
        echo '<hr />';
        echo '</div>';


        // autocomplete post type
        $post_type_array = array();
        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        // built-in post type
        $args = array(
            'public'   => true,
            '_builtin' => true,
        );
        $post_types = get_post_types( $args, $output, $operator );
        foreach ( $post_types  as $post_type ) {
            //echo '<p>' . $post_type . '</p>';
            $post_type_array[] = $post_type;
        }
        // custom post type
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        $post_types = get_post_types( $args, $output, $operator );
        foreach ( $post_types  as $post_type ) {
            //echo '<p>' . $post_type . '</p>';
            $post_type_array[] = $post_type;
        }
        echo '<script>(function(){var post_type_array=["'.join('","',$post_type_array).'"]; postloopajax_autocomplete("#'.$this->get_field_id('qs_post_type_names').'",post_type_array);})();</script>';


        // autocomplete post category
        $category_array = array();
        $args = array(
            //'post_type' => $type,
            //'get' => 'all',//$type,
            //'type' => 'primitive3',
            'orderby' => 'name',
            //'taxonomy'         => 'category',
            //'related_taxonomy' => 'post_tag',
            //'term_id'          => 0,
        );
        $categories = get_categories($args);
        foreach ($categories as $category) {
            $category_array[] = $category->name;
            //$category_array[] = $category->slug;
        }
        //print_r($categories);
        echo '<script>(function(){var post_type_array=["'.join('","',$category_array).'"]; postloopajax_autocomplete("#'.$this->get_field_id('qs_category_names').', #'.$this->get_field_id('qs_without_category_names').'",post_type_array);})();</script>';

        /*
        $cat_name = 'Products';
        echo $cat_name . get_cat_ID( $cat_name );
        //echo $obj[0]->slug;
        $obj = get_category(get_cat_ID( $cat_name ));
        echo $obj->slug;
        //var_dump($obj[0]);
        //print_r($obj);
        */

        /*
        // string to array
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
        //$instance['qs_array'] = $qs_array;
        print_r($qs_array);
        */

    }
}


add_action('widgets_init', create_function('', 'return register_widget("PostLoopAjax");'));
