# Integration

Due to its plugin nature, EF can be activated or deactivated at will. This is a fundamental difference compared to other frameworks that are included in the products that use them (drop-in).

For this reason it is essential to put yourself in the condition of knowing whether the framework is currently active in your installation or not.

Depending of whether the framework is active or not, you essentially have two strategies:

* if the theme/plugin functionality relies entirely on the framework being active, you can opt to completely halt the execution of the said theme/plugin,
* alternatively, you can choose to keep running the theme/plugin's code, making sure to manage the case that the framework isn't currently active.

The latter is the preferred solution, since it would allow the system to degrade nicely. For example, a plugin that declares a Custom Post Type and that relies on the framework to offer additional functionality could choose to keep declaring the CPT even when the framework is not active.

## Theme

To completely prevent the theme from being shown on frontend if the framework is not currently active, you could write something along these lines in the `functions.php` file.

~~~
$is_login_page = in_array( $GLOBALS["pagenow"], array( "wp-login.php", "wp-register.php" );

if ( ! class_exists( "Ev_Framework" ) && ! is_admin() && ! $is_login_page ) {
    wp_die( "Evolve Framework required" );
}
~~~

## Plugin

Initialization for framework-based plugins must happen at the `plugins_loaded` hook, since that action is executed as soon as all active plugins are loaded.

You can then verify the activation of the framework plugin by testing the existence of the `Ev_Framework` PHP class:

~~~
/**
 * Load the plugin instance.
 */
function ev_plugin_load() {
    if ( ! class_exists( "Ev_Framework" ) ) {
        return;
    }

    Ev_Plugin::instance();
}

add_action( "plugins_loaded", "ev_plugin_load" );
~~~