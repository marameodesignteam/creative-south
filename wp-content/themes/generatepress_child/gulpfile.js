let gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    cleanCss = require('gulp-clean-css'),
    rename = require('gulp-rename'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    gp_concat = require('gulp-concat'),
    gp_uglify = require('gulp-uglify'),
    headerComment = require('gulp-header-comment'),
    browserSync = require('browser-sync').create()


//PLEASE UPDATE THE DEST PATH

const paths = {
    scss: {
        src: './scss/style.scss',
        dest: './',
        watch: './scss/**/*.scss',
        bootstrap: './node_modules/bootstrap/scss/bootstrap.scss'
    },
    js: {
        bootstrap: './node_modules/bootstrap/dist/js/bootstrap.min.js',
        //for wordpress, many scripts relying on jquery. Better to include it by its own
        //jquery: './node_modules/jquery/dist/jquery.min.js',
        popper: 'node_modules/popper.js/dist/umd/popper.min.js',
        main: './js/main.js',
        dest: './js'
    },
    //OPTIONAL FOR DRUPAL
    //twig: {
    //watch: './templates/*.twig'
    //},
}


// Compile sass into CSS & auto-inject into browsers
function styles() {
    return gulp.src([paths.scss.bootstrap, paths.scss.src, ])
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer({
            browsers: [
                'Chrome >= 35',
                'Firefox >= 38',
                'Edge >= 12',
                'Explorer >= 10',
                'iOS >= 8',
                'Safari >= 8',
                'Android 2.3',
                'Android >= 4',
                'Opera >= 12'
            ]
        })]))
        .pipe(cleanCss({
            rebase: false,
            rebaseUrls: false
        }))
        //.pipe(sourcemaps.write())
        .pipe(gp_concat('style.css', {
            rebaseUrls: false
        }))
        .pipe(headerComment({
            file: 'headerComment.txt',
            encoding: 'utf-8', // Default is UTF-8
        }))
        .pipe(gulp.dest(paths.scss.dest))
        .pipe(browserSync.stream())
}

// Concat and minify js
function js() {
    return gulp.src([paths.js.bootstrap, /*paths.js.jquery,*/ paths.js.popper, paths.js.main])
        .pipe(gp_uglify())
        .pipe(gp_concat('main.min.js'))
        .pipe(gulp.dest(paths.js.dest))
        .pipe(browserSync.stream())
}

// Static Server + watching scss/html files
function serve() {
    // browserSync.init({
    //   //localhost
    //   //proxy: 'http://acta.localhost.com',
    // })

    gulp.watch([paths.scss.watch, paths.scss.bootstrap, /*OPTIONAL FOR DRUPAL paths.twig.watch*/ ], styles).on('change', browserSync.reload);
    gulp.watch([paths.js.main], js).on('change', browserSync.reload);
}

const build = gulp.series(styles, gulp.parallel(js, serve))

exports.styles = styles
exports.js = js
exports.serve = serve

exports.default = build