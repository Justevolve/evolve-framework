# User meta boxes

User meta boxes are data fields containers. They can have a one-level structure, or be divided in multiple subsections as tabs.

To create a meta box you should refer to the `add_user_meta_box` method in the `Ev_AdminController` class; for example:

~~~
ev_fw()->admin()->add_user_meta_box( "profile", __( "Profile", "textdomain" ) );
~~~

The method is defined as follows:

* **`add_user_meta_box( $handle, $title, $roles = '', $fields = array() )`**
    - `handle`, a unique name for the user meta box (no spaces, slug-like),
    - `title`, the user meta box title that's shown in admin editing screens,
    - `roles`, an array of capabilities/roles. Users with any of these capabilities will display the meta box,
    - `fields`, an array of fields that composes the user meta box.

Check the [data types documentation](data-types.md) for more information about what fields can be added to meta boxes.

To create a meta box you would write something like:

```
function my_theme_user_meta_box() {
    ev_fw()->admin()->add_user_meta_box( "profile", __( "Profile", "textdomain" ), array( "administrator" ), array(
        array(
            "handle" => "alt_avatar",
            "label" => __( "Alternative avatar", "textdomain" ),
            "type" => "image",
        )
    ) );
}

add_action( "init", "my_theme_user_meta_box" );
```

To create a tabbed user meta box, please refer to the meta boxes/option pages documentation, since the syntax is exactly the same.

*If only one tab is defined, the meta box won't show the tabs navigation.*

Since the user meta box name is set to `profile`, its fields can also be manipulated using the following filters:

* `ev_user_metabox[metabox:profile]`
* `ev_user_metabox[metabox:profile][group:{$group_handle}]`

## Adding a user meta box to a different user role

You can attach a previously declared user meta box to a different users group as well.

In order to do it, you might use the following filter:

```
function my_custom_user_roles( $roles ) {
    $roles[] = "editor";

    return $roles;
}

add_filter( "ev_user_metabox_roles[metabox:profile]", "my_custom_user_roles" )
```

As a result, both Administrators and Editors will feature the Profile user meta box in the user profile editing screen.

## Validation

See the validation section in the documentation page about [Options pages](option-page.md).