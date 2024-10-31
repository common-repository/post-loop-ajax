=== Post Loop Ajax ===
Contributors: AI.Takeuchi
Description: Post Loop and Pagination Widget. Multiple Posts Loop placement. Auto page feed and at random.
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P8LCVREFDKWFW
Tags: plugin, widget, post loop, category, pagination, auto page feed, page at random, effects, image slider has layer for text
Requires at least: 4.1
Tested up to: 4.9.5
Stable tag: 1.60
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Post Loop and Pagination Widget. Multiple Posts Loop placement. Auto page feed and at random.
It works inside Page Builder by SiteOrigin.
Page Builder by SiteOrigin is great plugin.

== Installation ==

1. Install plugin and activate.
2. Placement at widget area.

= How to make Image Slider has layer for text =

1. Define Custom Field, data type is Image and Name is 'postloopajax_slider_image'.
2. Define Category or Custom Post Type.
3. Add new Post or Custom Post.
    1. Select Category if case in Post.
    2. Set Image to Custom Field.
    3. Write Content.
4. Open Settings Screen and select Post Loop Ajax.
    1. Select 'IMAGE SLIDER' or 'IMAGE SLIDER OBJECT-FIT' of Templates.
    2. Edit Template, size and color and more.
5. Placement this Widget and settings.
    1. Enter fields 'post type name' and 'category name'.
    2. Enter 1 to 'posts per page'.
    3. Select 'full' of 'pagination type'.
    4. Check 'hide prev and next button'.
    5. Enter 'loop interval time'.
    6. Select 'IMAGE SLIDER' or 'IMAGE SLIDER OBJECT-FIT' of Templates.
6. Fix height and layout of Slider Block by CSS.


== Settings ==

= system =

* unique id

Normally enter default.
Checked and enter unique value, if doesn't work.
Will be destroyed this value when copied widget.

* remember page number until cookie expires

Default: 7 days

* remove the shortcode when not work

= title =

Enter widget title or empty. Checkbox: display or not.

= put before and after widget =

= need reload page =

If checked then set page number to cookie then reload page, not use ajax call.

= scroll to content top after reloaded =

= class =

Specify widget block class.

Default:

    post-loop-ajax-use-default-css

= display posts =

* post type name, separated by comma:

Enter Post Type name, also including Custom Post Type.
Default: post

* category name, separated by comma:

* without category name, separated by comma:

* additional (key=value&key2=value2...):

See: https://codex.wordpress.org/Class_Reference/WP_Query

* to result in (query after assembly):

Display query after assembly.

Doesn't work if use this query as argument of WP_Query function.

e.g.

`post_type=(post,topic)&cat=-1,-3`

The expansion of this formula, WP_Query function accept this array.

`
Array(
    ['post_type'] => Array (
        [0] => 'post'
        [1] => 'topic'
    )
    ['cat'] => '-1,-3'
)
`

* Can enter query code by json string format. This field is prioritize than above.

e.g.

`
{
"post_type":"products",
"orderby":"date",
"order":"ASC"
}
`

e.g.

`
{
"post_type": "post",
"posts_per_page": 2,
"orderby":"date",
"order":"DESC",
"date_query":[{"after": "12 month ago"}]
}
`

Don't use single quote.
This string is convert to PHP Array by 'json_decode' function.

= posts per page =

Default: 10

= max page number =

Default: 0 (0: disabled)

= excerpt char length =

Default: 140

= excerpt ellipsis =

Default: "..."

= excerpt more =

Default: Read More

= date time format =

Default:

    F jS, Y

= pagination type =

    nothing
    normal
    lite
    prev and next only
    full
    multi (for responsive design, see: css/post-loop-ajax.css)

= hide prev and next button =

= pagination previous string =

Enter previous navigation button string or empty(default button).

= pagination next string =

Enter next navigation button string or empty(default button).

= scroll to content top after rewrite content by ajax, without while loop enabled =

* scroll speed (-1: disabled)

Default: 200 (msec)

* scroll offset top

Default: -150 (px)

* scroll offset top margin

Default: 20 (px)

= auto loop contents =

* loop interval time (0: disabled)

Default: 0

* time until changed contents

Default: 500 (msec)

* page at random

= effects =

Using jQuery UI Effects.

* don't display loading image.

* front

select front effect types

* end

select end effect types

= template =

Enter or select template.

That have 5th blocks, split by `<!--split-->` tag.

Default:

    `
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
                    <div>Posted by <a href="%author_posts_link%">%author%</a> / %time%</div>
                    <div>Category: %category_links%</div>
                </footer><!-- .entry-footer -->
            </article>
            <!--split-->
            <!--no article-->
            <article id="post-%post_id%" %post_class%>
                <header class="entry-header">
                    <h2 class="entry-title">Not Found</h2>
                </header><!-- .entry-header -->
                <div class="entry-content">
                    <p>Content not found.</p>
                </div><!-- .entry-content -->
                <footer class="entry-footer">
                </footer><!-- .entry-footer -->
            </article>
            <!--split-->
            <!--footer-->
        <!-- </div> -->
        <div>Page: %paged% / %max_number_of_pages% (%number_of_posts% posts)</div>
        %pagination%
    </div>
            <!--split-->
            <!--no article footer-->
        <!-- </div> -->
    </div>
    `

Keyword:

    %widget_number%
    %post_id%
    %post_class%
    %permalink%
    %title%
    %content%
    %excerpt%
    %category_links%
    %time%
    %author%
    %author_posts_link%
    %pagination%
    %paged%
    %max_number_of_pages%
    %number_of_posts%
    %loop_interval%
    %first_image()%
    %custom_field()%
    %avatar_image()%
    %featured_image_url()%


== keyword: "%custom_field(type,name,index,size)%" ==

Replaced this keyword to custom field value.

* type : data type

    file, text, image, image_tag, image_url, image_width, image_height or image_is_resized

* name : custom field name
* index: index of custom field name
* size : image size

    thumbnail, medium, large or user defined

e.g.

    %custom_field(image,img1,0,large)%
    %custom_field(text,customer_name,0)%
    %custom_field(file,file,0)%
    %custom_field(file,file,0,)%
    %custom_field(file,file,1,download)%


== keyword: "%featured_image_url(size)%" ==

Replaced this keyword to featured image url.

* size is pixel or word (thumbnail, medium, large, full, ...).

e.g.

    `<img src="%featured_image_url(32,32)%" /> `

    `<img src="%featured_image_url(thumbnail)%" />`

    `<img src="%featured_image_url(large)%" />`


== keyword: "%avatar_image(size,link)%" ==

Replaced this keyword to avatar image / image with link.

* size is pixel size. max 512px.

e.g.

    keyword: %avatar_image()%

    put avatar img tag. use Default size 96px.

    keyword: %avatar_image(32)%

    put avatar img tag. size 32px.

    keyword: %avatar_image(32,)%

    put avatar image url.

    keyword: %avatar_image(32,http://example.com)%

    put avatar image with link to `http://example.com`. size 32px.

    e.g. `<a class="postloopajax-avatar-image-link postloopajax-avatar-image-link-??" href="link"><img src="avatar"/></a>`

    ?? is author id


== keyword: "%first_image(size name,link,remove,html)%" ==

Replaced this keyword to image url / image tag / image tag with link.
this function used class 'wp-image-??', ?? is image id.

e.g.

    keyword: %first_image(thumbnail)%

    put thumbnail image tag.

    keyword: %first_image(thumbnail,)%

    put thumbnail image url.

    keyword: %first_image(thumbnail, %first_image(large,)%)%

    put thumbnail image tag with link to large image url.

    e.g. `<a class="postloopajax-image-link" href="large image url"><img src="thumbnail image url" class="wp-image-??"/></a>`

    keyword: %first_image(medium, %permalink%, remove,<div class="f-img">_REPLACE_</div>)%

    put medium image tag with link to permalink.

    e.g. `<a class="postloopajax-image-link" href="permalink"><img src="medium image url" class="wp-image-??"/></a>`

    * Remove first image from content if specified 'remove' option. There are failed cases if contain complicated tags around image tag.

== Changelog ==

= 1.60 =

* will remove words after to '#' from reload url, if checked option 'need reload page'.

= 1.59 =

* stopped use magic constant `__DIR__`. The magic constant `__DIR__` is only available as of PHP v.5.3.0.

= 1.58 =

* stopped use magic constant `__DIR__`. The magic constant `__DIR__` is only available as of PHP v.5.3.0.

= 1.57 =

* Can now specify html tag to "first_image" function.

= 1.56 =

* Add "remove" option to "first_image" function.

= 1.55 =

* "featured_image_url" keyword addition.

= 1.54 =

* Can now specify maximum number of pages.

= 1.53 =

Disabled reload post at first load page if random and disabled loop.
It is need restore the post when come back widget placement page.
But widget not know is it is if came back. has not way, now.
It was working too if random and disabled loop.

= 1.52 =

* The following html tags can now be used in title: `<br> <strong> <i> <b> <span> <div> <font>`

= 1.51 =

* Fix, Losing part of article class on change.

= 1.50 =

* Fix issues.

= 1.49 =

* Can enter query code by json string format.
* Changed way to create widget id.
* Fix issues.

= 1.48 =

* Fix issues.

= 1.47 =

* Fix issues.

= 1.46 =

* Fix issues.

= 1.45 =

* Fix CSS Color Code.

= 1.44 =

* Added Template 'IMAGE SLIDER OBJECT-FIT'.

= 1.43 =

* Update language file.

= 1.42 =

* Fixed: of the display widget title.
* Modify function: restore ajax contents when click back-button on web browser.
* Support Custom Field Value.
* Added Pagination type 'full'.
* Added Template 'IMAGE SLIDER'.
* Changed Keyword and Templates.
* Additional function: remove the shortcode when not work.

= 1.41 =

* Modify timing display loading image.

= 1.40 =

* Restore ajax contents when click back-button on web browser.
* Fixed Pagination.

= 1.39 =

* Modify: way to checking for null.

= 1.38 =

* Modify: way to checking for null.

= 1.37 =

* Modify function: scroll to content top after reloaded.

= 1.36 =

* Modify function: scroll to content top after reloaded.

= 1.35 =

* Additional function: scroll to content top after reloaded.

= 1.34 =

* preg_split instead of split function.

= 1.33 =

* Fixed ajax timeout and error.

= 1.32 =

* Fixed Templates

= 1.31 =

* Fixed of the Effects.
* Update readme.txt

= 1.30 =

* Modify, display loading image to suitable Y position.
* Can select effects type at while rewrite contents.
* "avatar_image_size" keyword addition.
* Some fix.

= 1.29 =

* Fixed, missing get_magic_quotes_gpc and stripslashes.

= 1.28 =

* Changed the criteria of scroll coordinate.

= 1.27 =

* Fixed of the scroll speed.

= 1.26 =

* Update internationalization.

= 1.25 =

* Additional function: scroll to content top after rewrite content by ajax, without while loop enabled.

= 1.24 =

* Internationalization and some fixed.

= 1.23 =

* Bug fixed of Template Editor.

= 1.22 =

* Template Editor addition.

= 1.21 =

* Fixed css.
* Update template html.

= 1.20 =

* Fixed, display twice same page when loop at first time.
* Of the excerpt.
* Change way to assemble query.
* "first_image_sizename" keyword addition.

= 1.19 =

* Fixed css.

= 1.18 =

* Change way to enter query string.

= 1.17 =

* Fixed excerpt permalink.

= 1.16 =

* Change way to enter query string.

= 1.15 =

* Fixed the_content filter.

= 1.14 =

* Update html template.

= 1.13 =

* Fixed page at random function.

= 1.12 =

* Page at random function addition.

= 1.11 =

* Fixed wp-query.

= 1.10 =

* Internationalization.

= 1.9 =

* Added css file.

= 1.8 =

* Function addition.

= 1.7 =

* Fixed input tag.

= 1.6 =

* Published.


== Screenshots ==

1. Screenshot
2. Placement, Widget back-end by Page Builder SiteOrigin
3. Setting Screen
4. Template Editor


== Frequently Asked Questions ==

= How to include bbPress topics in it? =

Enter "topic" to post type field.

see: https://codex.wordpress.org/Class_Reference/WP_Query

