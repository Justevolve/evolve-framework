# Option pages

Option pages are data fields containers. They can have a one-level structure, or be divided in multiple subsections as tabs.

Following WordPress way of declaring pages in the admin area, there are two ways to declare an option page that would appear in the admin menu:

* to create a top-level page, you should use the `add_menu_page` method of the `Ev_AdminController` class,
* to create a sub menu page, you should use the `add_submenu_page` of the same class.

We suggest to call both methods with their own shortcuts:

* `ev_fw()->admin()->add_menu_page()`
* `ev_fw()->admin()->add_submenu_page()`

Methods are defined as follows:

* **`add_menu_page( $handle, $title, $fields = array(), $args = array() )`**
    - `handle`, a unique name for the page (no spaces, slug-like),
    - `title`, the page title that's shown in the admin menu and in the page iself,
    - `fields`, an array of fields that composes the page,
    - `args`, a configuration array. You can add a unique `group` subkey that allows to group option pages together. Pages belonging to the same group, will display an extra horizontal navigation.
* **`add_submenu_page( $parent, $handle, $title, $fields = array(), $args = array() )`**
    - The method shares the same parameters as the `add_menu_page` method, with the only difference being that the first parameter, `parent`, must be used to indicate the page of which the one being created will be child of.

Check the [data types documentation](data-types.md) for more information about what fields can be added to pages.

To create an option page you would write something like:

```
function my_theme_options_page() {
    ev_fw()->admin()->add_menu_page( "options", __( "Options", "textdomain" ), array(
        array(
            "handle" => "copyright_text",
            "label" => __( "Copyright text", "textdomain" ),
            "type" => "text",
        )
    ) );
}

add_action( "init", "my_theme_options_page" );
```

To create a page that's organized in tabs:

```
function my_tabbed_theme_options_page() {
    ev_fw()->admin()->add_menu_page( "options", __( "Options", "textdomain" ), array(
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

add_action( "init", "my_tabbed_theme_options_page" );
```

*If only one tab is defined, the page won't show the tabs navigation.*

Since the page name is set to `options`, its fields can also be manipulated using the `ev_admin_page[page:options]` filter.

## Validation

The system that allows you to create pages, tabs and fields is subject to a strict validation. For example, both tabs and fields must have a non-empty `handle` parameter, their `type` must belong to a field type that's been registered in the system, and so on.

If one of the validation criteria fails, the whole structure will be considered as "invalid", and won't be displayed. In many cases this will be due to simple mistakes, that can happen if you don't pay enough attention. In the future, we'll add a debug information message to allow developers to understand precisely what went wrong.