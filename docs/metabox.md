# Meta boxes

Option pages are data fields containers. They can have a one-level structure, or be divided in multiple subsections as tabs.

To create a meta box you should refer to the `add_meta_box` method in the `Ev_AdminController` class; for example:

~~~
thb_fw()->admin()->add_meta_box( "theme-options", __( "Theme options", "textdomain" ), array( "post", "page" ), array() );
~~~

The method is defined as follows:

* **`add_menu_page( $handle, $title, $post_types = 'post', $fields = array() )`**
    - `handle`, a unique name for the meta box (no spaces, slug-like),
    - `title`, the meta box title that's shown in admin editing screens,
    - `post_types`, an array of post types that will display the meta box,
    - `args`, an array of fields that composes the meta box.

Check the [data types documentation](data-types.md) for more information about what fields can be added to meta boxes.

To create a meta box you would write something like:

```
function my_theme_options_meta_box() {
    thb_fw()->admin()->add_meta_box( "options", __( "Options", "textdomain" ), array( "post", "page" ), array(
        array(
            "handle" => "copyright_text",
            "label" => __( "Copyright text", "textdomain" ),
            "type" => "text",
        )
    ) );
}

add_action( "init", "my_theme_options_meta_box" );
```

To create a meta box that's organized in tabs:

```
function my_tabbed_theme_options_meta_box() {
    thb_fw()->admin()->add_meta_box( "options", __( "Options", "textdomain" ), array( "post", "page" ), array(
        array(
            "handle" => "first-tab",
            "label"  => __( "First tab", "textdomain" ),
            "type"   => "group",
            "fields" => array(
                array(
                    "handle" => "copyright_text",
                    "label" => __( "Copyright text", "textdomain" ),
                    "type" => "text",
                )
            )
        ),
        array(
            "handle" => "second-tab",
            "label"  => __( "Second tab", "textdomain" ),
            "type"   => "group",
            "fields" => array(
                array(
                    "handle" => "another_text_field",
                    "label" => __( "Another text field", "textdomain" ),
                    "type" => "text",
                )
            )
        ),
    ) );
}

add_action( "init", "my_tabbed_theme_options_meta_box" );
```

*If only one tab is defined, the meta box won't show the tabs navigation.*

Since the meta box name is set to `options`, its fields can also be manipulated using the following filters:

* `ev[post_type:$post_type][metabox:options]`,
* `ev[post_type:$post_type][template:{$page_template}][metabox:options]`, for pages only.

## Adding a meta box to a different post type

When you, or a plugin you happen to be using, is declaring a Custom Post Type, you can attach a previously declared meta box to that post type as well.

In order to do it, you might use the following filter:

```
function my_custom_post_types( $types ) {
    $types[] = "test-post-type";

    return $types;
}

add_filter( "ev_metabox_post_types[metabox:options]", "my_custom_post_types" )
```

## Validation

See the validation section in the documentation page about [Options pages](option-page.md).