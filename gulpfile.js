// Gulp.js настройки
'use strict';

const wp = {
  root: "/home/localhost/www/wordpress",
  themeName: 'ktheme1'
}

const

// исходная папка и папка билда
  dir = {
    src: 'src/',
    build: wp.root + '/wp-content/themes/' + wp.themeName + '/' /* '/var/www/wp-content/themes/mytheme/' */
  },

  // Gulp и плагины
  gulp = require('gulp'),
  gutil = require('gulp-util'),
  newer = require('gulp-newer'),
  imagemin = require('gulp-imagemin'),
  sass = require('gulp-sass'),
  postcss = require('gulp-postcss'),
  deporder = require('gulp-deporder'),
  concat = require('gulp-concat'),
  stripdebug = require('gulp-strip-debug'),
  uglify = require('gulp-uglify'),
  plumber = require('gulp-plumber'),
  autoprefixer = require('gulp-autoprefixer');

// Browser-sync
var browsersync = false;

// PHP-настройки
const php = {
  src: dir.src + '**/*.php',
  build: dir.build
};

const staticFiles = {
  src: [dir.src + '**/*.txt', dir.src + '**/*.pot', dir.src + '**/*.po', dir.src +
    'LICENSE', dir.src +
    'README.md'
  ],
  build: dir.build
};

// копирование staticFiles-файлов
gulp.task('staticFiles', () => {
  return gulp.src(staticFiles.src)
    .pipe(plumber())
    .pipe(newer(staticFiles.build))
    .pipe(gulp.dest(staticFiles.build));
});

// копирование PHP-файлов
gulp.task('php', () => {
  return gulp.src(php.src)
    .pipe(plumber())
    .pipe(newer(php.build))
    .pipe(gulp.dest(php.build));
});

const images = {
  src: dir.src + 'images/**/*',
  build: dir.build + 'images/'
};

// обработка изображений
gulp.task('images', () => {
  return gulp.src(images.src)
    .pipe(plumber())
    .pipe(newer(images.build))
    .pipe(imagemin())
    .pipe(gulp.dest(images.build));
});

// CSS-настройки
var css = {
  src: dir.src + 'scss/style.scss',
  watch: dir.src + 'scss/**/*',
  build: dir.build,
  sassOpts: {
    outputStyle: 'nested',
    imagePath: images.build,
    precision: 3,
    errLogToConsole: true
  },
  processors: [
    require('postcss-assets')({
      loadPaths: ['images/'],
      basePath: dir.build,
      baseUrl: '/wp-content/themes/wptheme/'
    }),
    require('autoprefixer')({
      browsers: ['last 2 versions', '> 2%']
    }),
    require('css-mqpacker'),
    require('cssnano')
  ]
};

// обработка CSS
gulp.task('css', () => {
  gulp.series('images');
  return gulp.src(css.src)
    .pipe(plumber())
    .pipe(sass(css.sassOpts))
    .pipe(postcss(css.processors))
    .pipe(autoprefixer())
    .pipe(gulp.dest(css.build))
    .pipe(browsersync ? browsersync.reload({
      stream: true
    }) : gutil.noop());
});

// JavaScript-настройки
const js = {
  src: dir.src + 'js/**/*',
  build: dir.build + 'js/',
  filename: 'scripts.js'
};

// обработка JavaScript
gulp.task('js', () => {

  return gulp.src(js.src)
    .pipe(plumber())
    .pipe(deporder())
    //.pipe(concat(js.filename))
    .pipe(stripdebug())
    .pipe(uglify())
    .pipe(gulp.dest(js.build))
    .pipe(browsersync ? browsersync.reload({
      stream: true
    }) : gutil.noop());

});

// запустить все задачи
// gulp.task('build', ['php', 'css', 'js']);

const syncOpts = {
  proxy: 'localhost',
  files: dir.build + '**/*',
  open: false,
  notify: false,
  ghostMode: false,
  ui: {
    port: 8001
  }
};

// browser-sync
gulp.task('browsersync', () => {
  if (browsersync === false) {
    browsersync = require('browser-sync')
      .create();
    browsersync.init(syncOpts);
  }
});

gulp.task('watch',
  gulp.parallel('browsersync', () => {

    // изменения страниц
    gulp.watch(php.src, gulp.series('php'), browsersync ? browsersync.reload :
      {});

    // изменения изображений
    gulp.watch(images.src, gulp.series('images'));

    // CSS-изменения
    gulp.watch(css.watch, gulp.series('css'));

    // основные изменения в JavaScript
    gulp.watch(js.src, gulp.series('js'));

    // основные изменения в JavaScript
    gulp.watch(staticFiles.src, gulp.series('staticFiles'));

  }));

gulp.task('build', gulp.series(gulp.series('images'), gulp.parallel('php',
  'staticFiles', 'css', 'js')));

gulp.task('default', gulp.series('build', 'watch'));