module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    uglify: {
      js: {
        files: {
          'dist/js/site.min.js': ['assets/js/vendor/*.js', 'assets/js/*.js', '!assets/js/login.js', '!assets/js/admin/*.js' ],
          'dist/js/admin-scripts.min.js': 'assets/js/admin/admin-scripts.js'
        }
      }
    },
    'dart-sass': {
      target: {
        files: {
          'dist/css/main.min.css': 'assets/scss/main.scss',
          'dist/css/login.min.css': 'assets/scss/login.scss',
        },
        options: {
          outputStyle: 'compressed',
          sourceMap: true
        }
      }
    },
    watch: {
      js: {
        files: [
            'assets/js/**/*.js',
            '!assets/js/**/login.js',
        ],
        tasks: ['uglify:js'],
        options: {
          spawn: false,
        },
      },
      scss: {
        files: ['**/*.scss'],
        tasks: ['dart-sass'],
        options: {
          livereload: false,
        },
      },
      css: {
        files: ['**/*.css'],
        options: {
          livereload: true,
        },
      },
      php: {
        files: ['**/*.php'],
        options: {
          livereload: true,
        },
      }
    }
});

 // Load the plugin that provides the tasks.
  grunt.loadNpmTasks('grunt-contrib-uglify-es');
  grunt.loadNpmTasks('grunt-dart-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // define tasks
  grunt.registerTask('default', ["watch"]);
  grunt.registerTask('build', ["dart-sass","uglify"]);

};
