<?php if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

/**
 * Framework updater class. Picks up information from the framework Github
 * repository and integrates update notifications in the WordPress updates
 * screen.
 *
 * @since 0.1.0
 */
class Ev_Framework_Updater {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	private $pluginData;

	/**
	 * GitHub username.
	 *
	 * @var string
	 */
	private $username;

	/**
	 * GitHub repo name.
	 *
	 * @var string
	 */
	private $repo;

	/**
	 * __FILE__ of our plugin.
	 *
	 * @var string
	 */
	private $pluginFile;

	/**
	 * Holds data from GitHub.
	 *
	 * @var stdClass
	 */
	private $githubAPIResult;

	/**
	 * GitHub private repo token.
	 *
	 * @var string
	 */
	private $accessToken;

	/**
	 * Constructor for the updater class.
	 *
	 * @param string $pluginFile The plugin file name.
	 * @param string $gitHubUsername The Github username.
	 * @param string $gitHubProjectName The project name on Github.
	 * @param string $accessToken Private access token on Github.
	 */
	function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' ) {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'requestUpdate' ) );
		// add_filter( 'site_transient_update_plugins', array( $this, 'requestUpdate' ) );
		// add_filter( 'transient_update_plugins', array( $this, 'requestUpdate' ) );
		add_filter( 'upgrader_post_install', array( $this, 'postInstall' ), 10, 3 );
		add_filter( 'plugins_api', array( $this, 'setPluginInfo' ), 10, 3 );

		$this->pluginFile  = $pluginFile;
		$this->username    = $gitHubUsername;
		$this->repo        = $gitHubProjectName;
		$this->accessToken = $accessToken;
	}

	/**
	 * Attempt to request an update.
	 *
	 * @since 1.0.0
	 * @param stdClass $update_plugins
	 * @return stdClass
	 */
	public function requestUpdate( $update_plugins )
	{
		if ( ! is_object( $update_plugins ) ) {
			return $update_plugins;
		}

		if ( ! isset( $update_plugins->response ) || ! is_array( $update_plugins->response ) ) {
			$update_plugins->response = array();
		}

		$response = new stdClass();
		$response->slug = $this->slug;

		$info = $this->setPluginInfo( false, '', $response );

		if ( $info !== false ) {
			$obj              = new stdClass();
			$obj->slug        = $this->slug;
			$obj->new_version = $info->new_version;
			$obj->url         = $this->pluginData["PluginURI"];
			$obj->package     = $info->package;
			$obj->tested      = isset( $info->tested ) ? $info->tested : null;

			$update_plugins->response['evolve-framework/evolve-framework.php'] = $obj;
		}

		return $update_plugins;
	}

	/**
	 * Get information regarding our plugin from WordPress.
	 */
	private function initPluginData() {
		$this->slug = basename( dirname( $this->pluginFile ) );
		$this->baseslug = plugin_basename( $this->pluginFile );

		$this->pluginData = get_plugin_data( $this->pluginFile );
	}

	/**
	 * Get information regarding our plugin from GitHub.
	 */
	private function getRepoReleaseInfo() {
		/* Only do this once. */
		if ( ! empty( $this->githubAPIResult ) ) {
			return;
		}

		/* Query the GitHub API. */
		$url = "https://raw.githubusercontent.com/{$this->username}/{$this->repo}/stable/README.md";

		/* Get the results. */
		$body = wp_remote_retrieve_body( wp_remote_get( $url ) );
		$result = new stdClass();
		$result->body = $body;

		preg_match_all( '/(\n\n### )(\d\.\d\.?\d?)/', $body, $versions );

		if ( isset( $versions[2] ) && isset( $versions[2][0] ) && ! empty( $versions[2][0] ) ) {
			$result->tag_name = $versions[2][0];
			$result->zipball_url = "https://github.com/{$this->username}/{$this->repo}/archive/{$result->tag_name}.zip";
		}

		preg_match_all( '/(Last updated on: )(.*)/', $body, $date );

		if ( isset( $date[2] ) && isset( $date[2][0] ) && ! empty( $date[2][0] ) ) {
			$result->published_at = $date[2][0];
		}

		$this->githubAPIResult = $result;
	}

	/**
	 * Push in plugin version information to display in the details lightbox.
	 *
	 * @param boolean $false
	 * @param string $action
	 * @param stdClass $response The response object.
	 * @return stdClass
	 */
	public function setPluginInfo( $false, $action, $response ) {
		/* Get plugin & GitHub release information. */
		$this->initPluginData();
		$this->getRepoReleaseInfo();

		/* If nothing is found, do nothing. */
		if ( empty( $response->slug ) || $response->slug != $this->slug ) {
			return false;
		}

		/* Add our plugin information. */
		$response->last_updated = $this->githubAPIResult->published_at;
		$response->slug         = $this->slug;
		$response->plugin_name  = $this->pluginData["Name"];
		$response->name         = $this->pluginData["Name"];
		$response->version      = $this->githubAPIResult->tag_name;
		$response->new_version  = $this->githubAPIResult->tag_name;
		$response->author       = $this->pluginData["AuthorName"];
		$response->homepage     = $this->pluginData["PluginURI"];

		if ( version_compare( $response->version, EV_FRAMEWORK_VERSION ) <= 0 ) {
			return false;
		}

		/* This is our release download zip file. */
		$downloadLink = $this->githubAPIResult->zipball_url;

		/* Include the access token for private GitHub repos. */
		if ( ! empty( $this->accessToken ) ) {
			$downloadLink = add_query_arg(
				array( "access_token" => $this->accessToken ),
				$downloadLink
			);
		}

		$response->package = $downloadLink;
		$response->download_link = $downloadLink;

		/* We're going to parse the GitHub markdown release notes, include the parser. */
		require_once( plugin_dir_path( __FILE__ ) . "Parsedown.php" );

		/* Create tabs in the lightbox. */
		$response->sections = array(
			'description' => $this->pluginData["Description"],
			'changelog' => class_exists( "Parsedown" )
				? Parsedown::instance()->parse( $this->githubAPIResult->body )
				: $this->githubAPIResult->body
		);

		/* Gets the required version of WP if available. */
		$matches = null;
		preg_match( "/requires:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches );
		if ( ! empty( $matches ) ) {
			if ( is_array( $matches ) ) {
				if ( count( $matches ) > 1 ) {
					$response->requires = $matches[1];
				}
			}
		}

		/* Gets the tested version of WP if available. */
		$matches = null;
		preg_match( "/tested:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches );

		if ( ! empty( $matches ) ) {
			if ( is_array( $matches ) ) {
				if ( count( $matches ) > 1 ) {
					$response->tested = $matches[1];
				}
			}
		}

		return $response;
	}

	/**
	 * Perform additional actions to successfully install our plugin.
	 *
	 * @param boolean $true
	 * @param mixed $hook_extra
	 * @param array $result
	 * @return array
	 */
	public function postInstall( $true, $hook_extra, $result ) {
		// Get plugin information
		$this->initPluginData();

		// Remember if our plugin was previously activated
		$wasActivated = is_plugin_active( $this->baseslug );

		// Since we are hosted in GitHub, our plugin folder would have a dirname of
		// reponame-tagname change it to our original one:
		global $wp_filesystem;
		$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->baseslug );
		$wp_filesystem->move( $result['destination'], $pluginFolder );
		$result['destination'] = $pluginFolder;

		// Re-activate plugin if needed
		if ( $wasActivated ) {
			$activate = activate_plugin( $this->baseslug );
		}

		return $result;
	}

}