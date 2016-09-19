// -----------------------------------------------------------------------------
// CONFIGURATION
// -----------------------------------------------------------------------------

/**
 * Project modules.
 */
var modules = [
	"components/utilities",
	"components/grid",
	"components/tabs",
	"components/accordion",
	"components/dropdown",
	"components/icon",
	"components/modal",
	"components/utilities",
];

var scss_folder = 'scss';

function getCurrentDate() {
	var d = new Date();

    function pad(n) {return n<10 ? '0'+n : n}

    return d.getUTCFullYear()+'-'
         + pad(d.getUTCMonth()+1)+'-'
         + pad(d.getUTCDate())+'T'
         + pad(d.getUTCHours())+':'
         + pad(d.getUTCMinutes())+':'
         + pad(d.getUTCSeconds())+'Z'
}

// -----------------------------------------------------------------------------
// MODULES UTILITIES
// -----------------------------------------------------------------------------

/**
 * Get the SASS files in modules and store them in an array strucuture in order
 * for them to be compiled individually.
 *
 * @return {array}
 */
function get_modules_stylesheets() {
	var scss = {};

	for ( var module in modules ) {
		var dest = modules[module] + "/css/style.css",
			origin = modules[module] + "/css/style.scss";

		scss[dest] = origin;
	}

	return scss;
}

/**
 * Get the SASS files in modules and store them in an array strucuture in order
 * for them to be gruped in a single master SASS file to be compiled all at once.
 *
 * @return {array}
 */
function get_modules_raw_stylesheets() {
	var scss = [];

	for ( var module in modules ) {
		scss.push( modules[module] + '/css/*.scss' );
	}

	return scss;
}

/**
 * Get the compiled CSS files in modules and store them in an array strucuture
 * with the theme info header as first element of the array.
 *
 * @return {array}
 */
function get_modules_compiled_stylesheets() {
	var stylesheets = [];

	for ( var module in modules ) {
		stylesheets.push( modules[module] + '/css/style.css' );
	}

	return stylesheets;
}

/**
 * Get the modules' javascript files.
 *
 * @return {array}
 */
function get_modules_scripts() {
	var scripts = [
		"assets/js/utils.js",
		"assets/js/base.js"
	];

	for ( var module in modules ) {
		scripts.push( modules[module] + '/js/*.js' );
	}

	return scripts;
}

/**
 * Get the modules' admin javascript files.
 *
 * @return {array}
 */
function get_modules_admin_scripts() {
	var scripts = get_modules_scripts();

	scripts.push( "assets/js/admin/libs/jquery.minicolors.min.js" );
	scripts.push( "assets/js/admin/libs/selectize.min.js" );
	scripts.push( "assets/js/admin/libs/js-wp-editor.js" );
	scripts.push( "assets/js/admin/libs/jquery.scrollintoview.min.js" );
	scripts.push( "assets/js/admin/history.js" );
	scripts.push( "assets/js/admin/tooltip.js" );
	scripts.push( "assets/js/admin/ev.media_selector.js" );
	scripts.push( "assets/js/admin/repeatable.js" );
	scripts.push( "assets/js/admin/options.js" );
	scripts.push( "assets/js/admin/image_upload.js" );
	scripts.push( "assets/js/admin/attachment_upload.js" );
	scripts.push( "assets/js/admin/multiple_select.js" );
	scripts.push( "assets/js/admin/color.js" );
	scripts.push( "assets/js/admin/editor.js" );
	scripts.push( "assets/js/admin/icon.js" );
	scripts.push( "assets/js/admin/field.js" );
	scripts.push( "assets/js/admin/link.js" );
	scripts.push( "assets/js/admin/button.js" );
	scripts.push( "assets/js/admin/date.js" );

	return scripts;
}

module.exports = function( grunt ) {
	var pkg = grunt.file.readJSON( "package.json" );

	/**
	 * SASS libraries.
	 */
	var libs = [
		require( "path" ).resolve() + "/scss",
		require( "node-bourbon" ).includePaths
	];

	grunt.initConfig( {
		// SASS
		sass: {
			options: {
				loadPath: libs,
				style: "compressed",
				sourcemap: "none"
			},
			admin: {
				options: {
					style: "compact",
					quiet: true
				},
				files: [ {
					expand: true,
					cwd: "assets/" + scss_folder + "/",
					src: [
						"*.scss",
						"fields/*.scss",
						"!_utils.scss",
						"!libs.scss",
						"!config.scss",
						"!import.scss",
						"!admin.scss",
					],
					dest: "assets/" + scss_folder + "/compiled/",
					ext: ".css"
				} ]
			}
		},

		// Concat
		concat: {
			prod: {
				options: {
					separator: '\n\n',
					banner: '',
				},
				src: get_modules_raw_stylesheets(),
				dest: 'scss/components-libs.scss'
			},
			admin_css: {
				src: [ "assets/" + scss_folder + "/compiled/*.css", "assets/" + scss_folder + "/compiled/fields/*.css" ],
				dest: "assets/css/admin.css"
			},
		},

		// Append
		'file_append': {
			'admin_css': {
				files: [
					{
						prepend: '@charset "UTF-8";\n',
						input: "assets/css/admin.css",
						output: "assets/css/admin.css"
					}
				]
			}
		},

		clean: {
			start: [ "assets/" + scss_folder + "/compiled" ],
		},

		// Uglify
		uglify: {
			js: {
				files: {
					"assets/js/min/admin.min.js": get_modules_admin_scripts()
				}
			}
		},

		// Replace
		'string-replace': {
			'readme': {
				files: {
					'README.md': 'README.md',
				},
				options: {
					replacements: [
						{
							pattern: /Last updated on: (.*)/,
							replacement: "Last updated on: " + getCurrentDate()
						}
					]
				}
			},
			'framework-info': {
				files: {
					'evolve-framework.php': 'evolve-framework.php',
				},
				options: {
					replacements: [
						{
							pattern: /define\( 'EV_FRAMEWORK_VERSION', '(.*)' \);/,
							replacement: "define\( 'EV_FRAMEWORK_VERSION', '" + pkg.version + "' \);"
						},
						{
							pattern: /\* Version: (.*)/,
							replacement: "\* Version: " + pkg.version + "",
						}
					]
				}
			},
			'admin_css': {
				files: {
					'assets/css/admin.css': 'assets/css/admin.css',
				},
				options: {
					replacements: [
						{
							pattern: /@charset "UTF-8";/g,
							replacement: "",
						}
					]
				}
			},
		},

		// Watch
		watch: {
			admin_js_dev: {
				files: get_modules_admin_scripts(),
				tasks: [ "uglify", /*"concat:js_admin_dev",*/ "notify:done" ],
				options: {
					spawn: false
				}
			},
			prod_css: {
				files: get_modules_raw_stylesheets(),
				tasks: [ "prod" ],
				options: {
					spawn: false,
				}
			},
			admin_css: {
				files: [
					"assets/" + scss_folder + "/*.scss",
					"assets/" + scss_folder + "/fields/*.scss"
				],
				tasks: [ "sass:admin", "concat:admin_css", "string-replace:admin_css", "file_append:admin_css" ],
				options: {
					spawn: false
				}
			},
		},

		// ---------------------------------------------------------------------
		// PROJECT BUILD
		// ---------------------------------------------------------------------

		// POT
		makepot: {
			target: {
				options: {
					type: 'wp-plugin',
					'potHeaders': {
						'poedit'               : true,
						'x-poedit-keywordslist': true,
						'report-msgid-bugs-to' : 'https://github.com/Justevolve/evolve-framework',
						'last-translator'      : 'Evolve',
						'language-team'        : 'Evolve <info@justevolve.it>',
						'x-poedit-country'     : 'Italy'
					},
					'updateTimestamp': false
				}
			}
		},

		// Notify
		notify: {
			options: {
				title: pkg.title
			},
			done: {
				options: {
					message: "Ok!",
				}
			},
			start: {
				options: {
					message: "Ok!",
				}
			}
		},
	} );

	grunt.loadNpmTasks( "grunt-contrib-sass" );
	grunt.loadNpmTasks( "grunt-contrib-watch" );
	grunt.loadNpmTasks( "grunt-contrib-uglify" );
	grunt.loadNpmTasks( "grunt-contrib-concat" );
	grunt.loadNpmTasks( "grunt-string-replace" );
	grunt.loadNpmTasks( "grunt-markdown" );
	grunt.loadNpmTasks( "grunt-notify" );
	grunt.loadNpmTasks( "grunt-wp-i18n" );
	grunt.loadNpmTasks( "grunt-contrib-clean" );
	grunt.loadNpmTasks( "grunt-file-append" );

	grunt.task.run( "notify_hooks" );

	// -------------------------------------------------------------------------
	// EVENTS
	// -------------------------------------------------------------------------

	/**
	 * Target and run specific operations according to the type of file that has
	 * been modified.
	 */
	grunt.event.on( 'watch', function( action, filepath, target ) {
		switch ( target ) {
			case "admin_css":
				grunt.config( 'sass.admin.files.0.src', [ filepath.replace( "assets/scss/", "" ) ] );

				break;
			default:
				break;
		}
	} );

	// -------------------------------------------------------------------------
	// TASKS
	// -------------------------------------------------------------------------

	/**
	 * Update the framework info, concatenate all the SASS files in the project in
	 * a single master SASS file and compile it. The result of the compiling is
	 * put in style.css in the project's root folder.
	 */
	grunt.registerTask( "prod", [
		"string-replace:framework-info",
		"concat:prod",
		"uglify",
		"makepot",
		"notify:done"
	] );

	/**
	 * Procedure to be executed when the working session on the project begins.
	 * Recompiles files individually and starts the watch process.
	 */
	grunt.registerTask( "start", [
		"string-replace:framework-info",
		"string-replace:readme",
		"clean:start",
		"sass",
		"concat",
		"string-replace:admin_css",
		"file_append:admin_css",
		"uglify",
		"makepot",
		"notify:start",
		"watch"
	] );

	/**
	 * Procedure to be executed when the working session on the project begins.
	 * Recompiles files individually and starts the watch process for SCSS files.
	 */
	grunt.registerTask( "start_css", [
		"prod",
		"notify:start",
		"watch:admin_css"
	] );

	/**
	 * Procedure to be executed when the working session on the project begins.
	 * Recompiles files individually and starts the watch process for the components SCSS files.
	 */
	grunt.registerTask( "start_components_css", [
		"prod",
		"notify:start",
		"watch:prod_css"
	] );
};
