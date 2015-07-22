# Data management

One of the primary features provided by the framework is custom data management, that is the ability to manage (adding, editing, removing) custom data that's relevant for your project.

Currently the framework manages two types of data:

* `option` data are project-wide settings,
* `meta` data associated to posts, pages and other Custom Post Types entries.

These implementations are based on WordPress' [Options API](https://codex.wordpress.org/Options_API) and [Metadata API](https://codex.wordpress.org/Metadata_API) respectively.

*Currently the framework doesn't manage data associated to users or taxonomies, but we'd love to do something about that in the future.*

## Data structure

Custom options are stored in the `wp_options` table of WordPress' database and are serialized under the `ev` option key. This turns out pretty useful when you want to batch-export all of the custom options in your installation.

To retrieve the entire array of custom data options, you could use the standard WordPress function to retrieve options: `get_option( 'ev' )`.

Post Type meta data are stored individually.

See the [Data types](data-types.md) documentation for more information.

## Utility functions

To manipulate custom options the following functions are available:

* **`ev_get_option( $key )`**
    - Returns the value of an option.
    - Returns `false` if the option doesn't exist.
    - The returned value is filtered through `ev_get_option[key:{$key}]`.
* **`ev_update_option( $key, $value )`**
    - Updates the value of an option.
* **`ev_delete_option( $key )`**
    - Removes an option.