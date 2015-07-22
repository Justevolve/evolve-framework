module.exports = function( grunt ) {
	var pkg = grunt.file.readJSON( "package.json" );

	grunt.loadNpmTasks( "grunt-contrib-sass" );
	grunt.loadNpmTasks( "grunt-contrib-watch" );

	/**
	 * -------------------------------------------------------------------------
	 * Operations
	 * -------------------------------------------------------------------------
	 */

	grunt.initConfig( {

		sass: {
			options: {
				loadPath: [
					require( "path" ).resolve() + "/scss",
					require( "node-bourbon" ).includePaths
				],
				sourcemap: "none"
			},
			dev: {
				options: {
					style: "compressed",
					quiet: true
				},
				files: [ {
					expand: true,
					cwd: "scss/",
					src: [
						"*.scss",
						"!mixins.scss"
					],
					dest: "css/",
					ext: ".css"
				} ]
			}
		},

		watch: {
			css: {
				files: [
					"scss/*.scss",
					"!mixins.scss"
				],
				tasks: [ "sass" ],
				options: {
					spawn: false
				}
			},
		}

	} );

	/**
	 * -------------------------------------------------------------------------
	 * Tasks
	 * -------------------------------------------------------------------------
	 */

	/**
	 * grunt start
	 */
	grunt.registerTask( "start", [
		"sass",
		"watch"
	] );
};