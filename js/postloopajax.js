/*
 * postloopajax.js
 *
 * -*- Encoding: utf8n -*-
 *
 * Copyright (C) 2016 AI.Takeuchi
 * Author: AI.Takeuchi
 */

//postloopajax = {};

// scroll
jQuery(document).ready(function() {
    var result = jQuery.cookie('postloopajax-hash');
    //console.log(result);

    if (!result  || result == null || result == 'null' || result == undefined) { // null is not null?
        //console.log('postloopajax: null');
        return;
    }
    //console.log('postloopajax: result: ' + result);
    var values = result.split(",");
    if (values.length != 3 || !jQuery(values[0])[0]) {
        //console.log('postloopajax: not scroll: result: ' + result);
        return;
    }

    //console.log('postloopajax: scroll: result: ' + result);
    var positionY = jQuery(values[0]).offset().top + parseInt(values[1]);
    var scroll_speed = parseInt(values[2]);
    jQuery("html, body").animate({'scrollTop': positionY}, scroll_speed, "swing");
    // delete cookie
    jQuery.cookie('postloopajax-hash', null);
});


function post_loop_ajax_move_page(widget_number, page, cookie_expires, need_reload_page, scroll_content_top_after_reloaded_or_rewrite, scroll_offset_top, scroll_speed, target_class, do_not_display_loading_image, is_click, loopCallback) {
    //console.log(widget_number + ', ' + page);

    var selector = '.' + target_class;
    var cookie_key = 'postloopajax-' + widget_number;
    //var cookie_expires = 7;
    //console.log('page = ' + page);
    // next page is 2 when loop is first time.
    if (!jQuery.cookie(cookie_key) && loopCallback && page == 1) {
        page = 2;
    }
    jQuery.cookie(cookie_key, page, { expires: cookie_expires }); // set page number to cookie

    // show loading image
    var loadingCover = jQuery('<div class="post-loop-ajax-loading-cover"></div>');
    jQuery(selector).append(loadingCover);

    // change loading image state. nothing or move suitable Y position.
    if (do_not_display_loading_image && is_click == 0) {
        jQuery('.post-loop-ajax-use-default-css .post-loop-ajax-loading-cover').css('background-image', 'none');
    } else {
        // calculate loading image Y position.
        var imghh = 32 / 2; // loading image height size (px)

        var top = jQuery(selector).offset().top;
        var height = jQuery(selector).height();
        var bottom = top + height;

        var wtop = jQuery(window).scrollTop();
        var wbottom = wtop + jQuery(window).height();

        //console.log('top: ' + top + ', wtop: ' + wtop);

        var yp = 50; // 50%
        if (top < wtop && bottom > wbottom) {
            //console.log('a');
            yp = ((height - (wtop - top) - (bottom - wbottom) -imghh) / 2 + (wtop - top)) / height * 100;
        } else if (top > wtop && bottom > wbottom) {
            //console.log('b');
            yp = (height - (bottom - wbottom) - imghh) / 2 / height * 100;
        } else if (top < wtop && bottom < wbottom) {
            //console.log('c');
            yp = ((height - (wtop - top) - imghh) / 2 + (wtop - top)) / height * 100;
        }
        //console.log(yp);
        jQuery('.post-loop-ajax-use-default-css .post-loop-ajax-loading-cover').css('background-position', '50% ' + yp + '%');
    }

    if (need_reload_page) {
        if (scroll_content_top_after_reloaded_or_rewrite) {
            //alert("set scroll information to cookie");
            jQuery.cookie('postloopajax-hash', selector + ',' + scroll_offset_top + ',' + scroll_speed, { expires: cookie_expires });
        }
        // remove words after to '#' from reload url
        location.href = location.protocol + "//" + location.host + location.pathname + location.search;
        // location.reload(true);
        return;
    }

    var action = 'postloopajax_session_start';
    var postloopajaxServer = postloopajax_ajaxurl;

    var sendData = {
        "ajax_nonce": postloopajax_ajax_nonce,
        "action": action,
        "cmd": "update",
        "widget_number": widget_number,
        "page": page,
        "is_click": is_click
    };
    //console.log($scope.obj2str($scope.matchSelectors));
    //console.log("data: " + $scope.obj2str($scope.matchSelectors));

    jQuery.ajax({
        timeout: 10000,
        url: postloopajaxServer,
        type: "POST",
        dataType: "json",
        data: sendData,
        cache: false
    }).success(function (data) {
        //console.log(data);
        if (data == '#_multiwidget_reload_#' || data == '#_nonce_error_reload_#') {
            error("Post Loop Ajax: receive data: " + data);
            //error("Post Loop Ajax: receive data: " + data + ", " + jQuery.cookie('postloopajax_reload'));
            //alert(data);
            /*
            if (!jQuery.cookie('postloopajax_reload')) {
                jQuery.cookie('postloopajax_reload', '1', { 'max-age': 15 }); // set cookie 15 sec
                location.reload(true);
                return false;
            }
            */
            location.reload(true);
            return false;
        }

        if (!data || !data.contents || data.instance == undefined) {
            error("receive data: " + data);
            if (jQuery.cookie('postloopajax_ajax_data_error') !== '1') {
                jQuery.cookie('postloopajax_ajax_data_error', '1', { 'max-age': 60 }); // set cookie 60sec
                location.reload(true);
            }
            return false;
        }
        var contents = data.contents;
        var instance = data.instance;

        // rewrite contents.
        /*
        var effects_options = { pieces: 25, horizFirst: false, color: '#ffffff', percent: 120, direction: 'vertical', distance: 20, times: 20, origin: ["middle", "center"]};
        */

        var fade_time = instance.fade_time / 2;
        if (is_click > 0 || fade_time == 0) {
            //jQuery(selector).html(contents); // no effect
            fade_time = 100;
            jQuery(selector).fadeOut(fade_time, function(){
                jQuery(selector).html(contents).fadeIn(fade_time);
            });
        } else {
            jQuery(selector).hide(instance.effect_type_front, instance.effect_options_front, fade_time, function(){
                jQuery(selector).html(contents).show(instance.effect_type_end, instance.effect_options_end, fade_time);
            });
        }


        // scroll to contents top
        if (scroll_content_top_after_reloaded_or_rewrite) {
            var scrollSpeed = parseInt(instance.scroll_speed);
            var scrollOffsetTop = parseInt(instance.scroll_offset_top);
            var scrollOffsetTopMargin = parseInt(instance.scroll_offset_top_margin);
            var scrollTop = jQuery(window).scrollTop();
            var articleTop = jQuery(selector).offset().top;
            var articleTopOffset = jQuery(selector).offset().top + scrollOffsetTop;
            //console.log("offsetTop: " + articleTop);

            //if (is_click > 0 && scrollSpeed >= 0 && (articleTop < scrollTop || articleTop > scrollBottom) && Math.abs(scrollTop - articleTop) > scrollOffsetTopMargin) {
            if (is_click > 0 && scrollSpeed >= 0 && articleTop < scrollTop && Math.abs(scrollTop - articleTopOffset) > scrollOffsetTopMargin) {
                //jQuery(window).scrollTop(articleTop); // no effect
                jQuery("html, body").animate({scrollTop: articleTopOffset}, scrollSpeed, "swing");
            }
        }


        if (loopCallback) {
            loopCallback();
        }

        /*
        jQuery(selector).html(contents, function () {
            // kick load
            var e = document.createEvent("HTMLEvents");
            e.initEvent("load",false,false);
            window.dispatchEvent(e);
            // kick load 2
            window.addEventListener('load','',false);
        });
        */

    }).error(function (data) {
        error("failed to ajax request.");
    });

    function error(msg) {
        console.log("Post Loop Ajax: widget_number: " + widget_number + ": \n" + msg);
        jQuery(selector + ' .post-loop-ajax-loading-cover').remove();
        if (loopCallback) {
            loopCallback();
        }
    }

}

