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
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'setTransitent' ) );
		add_filter( 'plugins_api', array( $this, 'setPluginInfo' ), 10, 3 );
		add_filter( 'upgrader_post_install', array( $this, 'postInstall' ), 10, 3 );

		$this->pluginFile  = $pluginFile;
		$this->username    = $gitHubUsername;
		$this->repo        = $gitHubProjectName;
		$this->accessToken = $accessToken;
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
		$url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";

		/* We need the access token for private repos. */
		if ( ! empty( $this->accessToken ) ) {
			$url = esc_url( add_query_arg( array( "access_token" => $this->accessToken ), $url ) );
		}

		/* Get the results. */
		$this->githubAPIResult = wp_remote_retrieve_body( wp_remote_get( $url ) );

		if ( ! empty( $this->githubAPIResult ) ) {
			$this->githubAPIResult = @json_decode( $this->githubAPIResult );
		}

		/* Use only the latest release. */
		if ( is_array( $this->githubAPIResult ) && isset( $this->githubAPIResult[0] ) ) {
			$this->githubAPIResult = $this->githubAPIResult[0];
		}
	}

	/**
	 * Push in plugin version information to get the update notification.
	 *
	 * @param string $transient The transient data.
	 * @return stdClass
	 */
	public function setTransitent( $transient ) {
		/* If we have checked the plugin data before, don't re-check. */
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		if ( empty( $this->githubAPIResult ) ) {
			return $transient;
		}

		/* Get plugin & GitHub release information. */
		$this->initPluginData();
		$this->getRepoReleaseInfo();

		/* Check the versions if we need to do an update. */
		$doUpdate = version_compare( $this->githubAPIResult->tag_name, $transient->checked[$this->baseslug] );

		/* Update the transient to include our updated plugin data. */
		if ( $doUpdate == 1 ) {
			$package = $this->githubAPIResult->zipball_url;

			/* Include the access token for private GitHub repos. */
			if ( ! empty( $this->accessToken ) ) {
				$package = esc_url( add_query_arg( array( "access_token" => $this->accessToken ), $package ) );
			}

			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->plugin = $this->baseslug;
			$obj->new_version = $this->githubAPIResult->tag_name;
			$obj->url = $this->pluginData["PluginURI"];
			$obj->package = $package;
			$transient->response[$this->baseslug] = $obj;
		}

		return $transient;
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
		$response->slug = $this->slug;
		$response->plugin_name  = $this->pluginData["Name"];
		$response->name  = $this->pluginData["Name"];
		$response->version = $this->githubAPIResult->tag_name;
		$response->author = $this->pluginData["AuthorName"];
		$response->homepage = $this->pluginData["PluginURI"];

		/* This is our release download zip file. */
		$downloadLink = $this->githubAPIResult->zipball_url;

		/* Include the access token for private GitHub repos. */
		if ( ! empty( $this->accessToken ) ) {
			$downloadLink = add_query_arg(
				array( "access_token" => $this->accessToken ),
				$downloadLink
			);
		}
		$response->download_link = $downloadLink;

		/* We're going to parse the GitHub markdown release notes, include the parser. */
		// require_once( plugin_dir_path( __FILE__ ) . "Parsedown.php" );

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