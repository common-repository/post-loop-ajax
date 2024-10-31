<?php
/*
server.php
 */

namespace postloopajax;

// ajax for wordpress
function postloopajax_session_start() {
    if (check_ajax_referer('postloopajax', 'ajax_nonce', false) == false) {
        wp_send_json('#_nonce_error_reload_#');
        wp_die();
    }
    ajaxMain();
    wp_die();
}

// action name: 'postloopajax_session_start'
add_action('wp_ajax_postloopajax_session_start', 'postloopajax\postloopajax_session_start');
add_action('wp_ajax_nopriv_postloopajax_session_start', 'postloopajax\postloopajax_session_start');

//add_action('init', 'postloopajax\postloopajax_session_start', 8);


function ajaxMain() {
    require_once('get_contents.php');

    $cmd = getPostValue('cmd');
    if ($cmd == 'update') {
        $widget_number = getPostValue('widget_number');
        $is_click = getPostValue('is_click');
        $page = getPostValue('page');

        $instances = get_option('postloopajax');
        $instance = $instances[$widget_number];

        //  [_multiwidget] => 1
        //list($contents, $loopScript, $instance, $paged) = PostLoopAjaxGetArticle($widget_number, $instance, $page, 0, $is_click); // widget number, instance, paged, get request
        list($contents, $loopScript, $instance) = PostLoopAjaxGetArticle($widget_number, $instance, $page, 0, $is_click); // widget number, instance, paged, get request

        $same = false;
        if (array_key_exists('same as front' , $instance['effect_end_types'])) {
            $same = true;
        }
        unset($instance['effect_end_types']['same as front']);

        if (count($instance['effect_front_types']) > 1) {
            $effect_front_type = array_rand($instance['effect_front_types']);
        } else {
            $effect_front_type = array_shift($instance['effect_front_types']);
        }
        if (!$effect_front_type) {
            $effect_front_type = 'fade';
        }

        if (count($instance['effect_end_types']) > 1) {
            $effect_end_type = array_rand($instance['effect_end_types']);
        } else {
            $effect_end_type = array_shift($instance['effect_end_types']);
        }
        if (!$effect_end_type) {
            $effect_end_type = 'fade';
        }
        if ($same) {
            $effect_end_type = $effect_front_type;
        }

        $instance['effect_type_front'] = $effect_front_type;
        $instance['effect_type_end'] = $effect_end_type;

        /*
        if ($result == -1) {
            // unknown error
            $result = '#_multiwidget_reload_#';
        }
        */

        //wp_send_json(print_r($instances, 1));
        //$contents = $widget_number;
        //$result = array('abc');
        //$contents = 'abc';
        //$json = json_encode($result);
        //header("Access-Control-Allow-Origin: *");
        //header("Content-Type: application/json; charset=utf-8");
        //print_r($json);
        //return; // error

        //wp_send_json(array('contents' => $contents, 'instance' => $instance, 'paged' => $paged));
        wp_send_json(array('contents' => $contents, 'instance' => $instance));
    } else {
        header("HTTP/1.1 403 Forbidden");
        ?>
        <!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
        <html>
            <head>
                <title>403 Forbidden</title>
            </head>
            <body>
                <h1>Forbidden</h1>
                <p>You don't have permission to access <?php echo $_SERVER['SCRIPT_NAME']; ?>
                    on this server.</p>
                <p>Additionally, a 403 Forbidden
                    error was encountered while trying to use an ErrorDocument to handle the request.</p>
            </body>
        </html>
        <?php
    }
}


function getPostValue($s) {
    return stripslashes(get_POSTvalue($s));
}

function get_POSTvalue($key) {
    if (array_key_exists($key, $_POST)) {
        return $_POST[$key];
    }
    return null;
}

