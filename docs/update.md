# Update

EF is integrated with WordPress' native update system, which means that should a new version of the framework be available, a notice will appear in the Plugin section of the WordPress admin.

The release list for the plugin is available [here](https://github.com/Justevolve/evolve-framework/releases).

## Disabling update notifications

To disable update notifications, you could use a filter that was made just for this purpose:

~~~
add_filter( "ev_framework_can_update", "__return_false" );
~~~

*It's worth mentioning that if the plugin folder is named something other than `evolve-framework`, update notifications are turned off by default.*

*Update notifications are also turned off when the plugin folder is a checkout from a Version Control System.*