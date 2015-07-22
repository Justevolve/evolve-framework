# Data types

Here's a list of all the framework data types (fields) available:

* [Text](#text)
* [Textarea](#textarea)
* [Select](#select)
* [Radio](#radio)
* [Number](#number)
* [Multiple select](#multiple-select)
* [Image](#image)
* [Icon](#icon)
* [Divider](#divider)
* [Description](#description)
* [Color](#color)
* [Checkbox](#checkbox)

All the data types are defined by a common set of parameters:

* `type`: the field data type,
* `handle`: the key with whom the data will be stored, (no spaces, slug-like),
* `label`: the label displayed in the backend interface,
* `help` *(optional)*: a descriptive text for the field to be displayed in the backend interface,
* `default` *(optional)*: the default value for the field, if it hasn't been saved yet,

The `label` parameter supports three different styles:

* `inline` *(default)*: the label is displayed on the side of the field,
* `block`: the label is displayed above the field,
* `hidden`: the label is not shown.

In order to enable a specific display mode, you have to set the `label` value as an array, with the `text` key containing the label text and the `type` key containing the desired display mode:

~~~
"label" => array(
    "text" => "Label title",
    "type" => "block"
)
~~~

The `help` parameter supports two different styles:

* `inline` *(default)*: the help text is displayed below the field label,
* `tooltip`: the help text is displayed as a tooltip.

In order to enable a specific display mode, you have to set the `help` value as an array, with the `text` key containing the help text and the `type` key containing the desired display mode:

~~~
"help" => array(
    "text" => "Field help text",
    "type" => "tooltip"
)
~~~

Most of the fields support specific configuration parameters. These parameters must be added under `config` as an array:

~~~
"config" => array(
    // Configuration parameters...
    "data" => array()
)
~~~


## Text

~~~
array(
    "type"   => "text",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "size" => ""
    )
)
~~~

## Textarea

~~~
array(
    "type"   => "textarea",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "rows" => "2",
        "cols" => "20",
        "rich" => false
    )
)
~~~

When set to `true`, the `rich` parameter will generate a rich textarea field with *TinyMCE* enabled.

## Select

~~~
array(
    "type"   => "select",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "data" => array()
    )
)
~~~

The select options are listed under the `config` > `data` key as an array. The key value represents the key with whom the data will be stored:

~~~
"data" => array(
    "option1" => __( "Option 1", "ev_framework" ),
    "option2" => __( "Option 2", "ev_framework" ),
    "option3" => __( "Option 3", "ev_framework" ),
)
~~~

## Radio

~~~
array(
    "type"   => "radio",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "data" => array()
    )
)
~~~

The radio options are listed under the `config` > `data` key as an array. The key value represents the key with whom the data will be stored:

~~~
"data" => array(
    "option1" => __( "Option 1", "ev_framework" ),
    "option2" => __( "Option 2", "ev_framework" ),
    "option3" => __( "Option 3", "ev_framework" ),
)
~~~

## Number

~~~
array(
    "type"   => "number",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "step"  => "", // Increment value
        "min"   => "", // Minimum value
        "max"   => ""  // Maximum value
    )
)
~~~

## Multiple select

~~~
array(
    "type"   => "multiple_select",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "vertical" => false,
        "data"     => array()
    )
)
~~~

The multiple select options are listed under the `config` > `data` key as an array. The key value represents the key with whom the data will be stored:

~~~
"data" => array(
    "option1" => __( "Option 1", "ev_framework" ),
    "option2" => __( "Option 2", "ev_framework" ),
    "option3" => __( "Option 3", "ev_framework" ),
)
~~~

The order of the selected options can be altered by dragging the selected items.

The default value for the `vertical` parameter is `false`: this will cause the selected selected items to be displayed inline.
When setting the value to `true`, the selected items will be stacked vertically.

The field stores a series of comma-separated values under a single key.

## Image

The image field data is what we define as *complex*, which means that its structure is an array composed by one or more subkeys and that will be serialized upon saving. The structure of the saved data is as follows:

```
[desktop] => Array
    (
        [1] => Array 
            (
                [id] => 26
                [image_size] => full
            )
    )
```

In the above mentioned example, `id` represents the selected attachment ID and `image_size` is the name of the image size selected, when the `image_size` parameter is set to `true`.

Concerning the `desktop` and `1` keys, they represent the default screen width and density.

~~~
array(
    "type"   => "image",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array(
        "multiple"    => false,
        "sortable"    => false,
        "image_size"  => false, // Display a select listing the image sizes available
        "thumb_size"  => "medium" // Size of the preview image loaded in the admin interface
    )
)
~~~

When set to `true`, the `multiple` parameter allows to create a multiple image upload field.

The `sortable` parameter when used in conjunction with the `multiple` parameter allows to enable the sorting on the uploaded images.

## Icon

~~~
array(
    "type"   => "icon",
    "handle" => "",
    "label"  => "",
    "help"   => ""
    "config" => array()
)
~~~

The icon data type is also a *complex* field, which means that it will be serialized upon saving. The structure of the saved data is as follows:

* `set`: the icon library name,
* `icon`: the selected icon code,
* `prefix`: the icon library prefix.

**Configuration**

In order to add a new icon font, you can use the `ev_get_icon_fonts` filter. The filter acceps one parameter, representing an array of the already defined icon libraries. Each library is structured as follows:

~~~
array(
  'name'    => 'library-name',
  'label'   => 'Library label',
  'url'     => "URL to the library's CSS file",
  'prefix'  => '',  // Library's CSS prefix, (optional)
  'mapping' => array(
     'fa-envelope-o',
     'fa-heart',
     // ...
)
~~~

## Divider

~~~
array(
    "type"   => "divider",
    "handle" => "",
    "text"   => "",
    "config" => array(
        "style" => "section_break"
    )
)
~~~

This field is not stored in the database. The divider field can have different styles, that are set by changing the value of its `config` > `style` setting:

* `section_break`: the divider visually breaks the flow of the page,
* `in_page`: the divider text is inserted in the flow of the page.

When no `style` configuration is present, the `section_break` style will be used as default.

## Description

~~~
array(
    "type"   => "description",
    "handle" => "",
    "text"   => "",
    "config" => array(
        "style" => "standard"
    )
)
~~~

This field is not stored in the database. The description field can have different styles, that are set by changing the value of its `config` > `style` setting:

* `standard`: a text block with no formatting,
* `info`: an informative text block,
* `important` a text block representing a warning.

When no `style` configuration is present, the `standard` style will be used as default.

## Color

~~~
array(
    "type"   => "color",
    "handle" => "",
    "label"  => "",
    "help"   => ""
)
~~~

The field stores an HEX value of the selected color, with the `#` symbol included.

## Checkbox

~~~
array(
    "type"   => "checkbox",
    "handle" => "",
    "label"  => "",
    "help"   => ""
)
~~~

The field stores one between `0` and `1`, depending on the checkbox state.

---

## Repeatable fields

A field can be declared to be "repeatable", which means that it can be duplicated multiple times using the same control. Repeatable fiels are saved using the very same `handle` parameter, and they are serialized.

To set a field to be repeatable, just add a `repeatable` key to the field definition as set it to `true`:

~~~
array(
    "type"       => "text",
    "handle"     => "",
    "label"      => "",
    "help"       => "",
    "repeatable" => true
)
~~~