'use strict';

const fs = require('fs');

const clean = require('gulp-clean');
const concat = require('gulp-concat');
const csso = require('gulp-csso');
const shell = require('gulp-shell');
const filter = require('gulp-filter');
const gulp = require('gulp');
const gulpCopy = require('gulp-copy');
const gulpInclude = require('gulp-include');
const sourcemaps = require('gulp-sourcemaps');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const postcssAutoprefixer = require('autoprefixer');
const postcssNested = require('postcss-nested');
const postcssSimpleVars = require('postcss-simple-vars');
const postcssStripInlineComments = require('postcss-strip-inline-comments');
const scssSyntax = require('postcss-scss');
const uglify = require('gulp-uglify');
const urlAdjuster = require('gulp-css-url-adjuster');
const browserSync = require('browser-sync').create();
const jade = require('gulp-jade');
const runSequence = require('run-sequence');

const watch = require('gulp-watch');

var assetManifest = require('gulp-asset-manifest');
var rev = require('gulp-rev'); // Optional



// const dest = './dist/public';
const dest = './web/themes/public';

const autoprefixerOptions = {
    browsers: [
        'ie >= 10',
        'opera 12.1',
        '> 2%',
        'last 5 versions'
    ]
};
const postcssProcessors = [
    postcssStripInlineComments,
    postcssSimpleVars(),
    postcssNested(),
    postcssAutoprefixer(autoprefixerOptions)
];
const krtechBLPath = './node_modules/krtech-bl/common.blocks/';
const jsPath = [
    '.tmp/script.js',
    './node_modules/jquery/dist/jquery.js',

    './node_modules/tooltipster/dist/js/tooltipster.bundle.js',
    './node_modules/ym/modules.js',
    './node_modules/chosen-js/chosen.jquery.js',
    './node_modules/js-cookie/src/js.cookie.js',
    // './node_modules/vow/lib/vow.js',
    krtechBLPath + 'form2js/form2js.js',
    // krtechBLPath + 'js2form/js2form.js',
    './blocks/**/!(lib.)*.js'
];
const stylePath = [
    '../node_modules/chosen-js/chosen.css',
    '../node_modules/tooltipster/dist/css/tooltipster.bundle.css',
    '../node_modules/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/' +
            'tooltipster-sideTip-borderless.min.css',
    '../blocks/**/*.{scss,css}'
];


const browserSyncOptions = {
    server: './web/themes',
        notify: false,
        reloadOnRestart: true,
        snippetOptions: {
        rule: {
            match: /<\/body>/i
        }
    }
};

//Jade
gulp.task('jade', () => {
    return gulp.src('bundles/*.jade')
        .pipe(jade({
            pretty: true
        }))
        .pipe(gulp.dest('./web/themes/'))
});


gulp.task('jadeReload', () => {
    return runSequence('jade', browserSync.reload);
});



//Serve
gulp.task('serve', () => {
    return browserSync.init(browserSyncOptions);
});

// Clean
gulp.task('clean', () => {
    return gulp.src(dest, {read: false})
        .pipe(clean());
});
gulp.task('cleanCSS', () => {
    return gulp.src(dest + '/*.css', {read: false})
        .pipe(clean());
});
gulp.task('cleanImg', () => {
    return gulp.src(dest + '/img', {read: false})
        .pipe(clean());
});
gulp.task('cleanJs', () => {
    return gulp.src(dest + '/*.js', {read: false})
        .pipe(clean());
});
gulp.task('cleanLib', () => {
    return gulp.src(dest + '/lib', {read: false})
        .pipe(clean());
});
gulp.task('cleanDesc', () => {
    return gulp.src(dest + '/bl.html', {read: false})
        .pipe(clean());
});

// Prepare tmp file
gulp.task('_preparetmp', () => {
    if (!fs.existsSync('.tmp')) {
        fs.mkdirSync('.tmp');
    }
    fs.writeFileSync('.tmp/style.scss', stylePath.map((path) => {
        return '//=require ' + path;
    }).join('\n'));
    fs.writeFileSync('.tmp/script.js', '"use strict";');
});

// CopyImg
gulp.task('copyImg', ['cleanImg'], () => {
    return gulp.src('./blocks/**/*.{jpg,png,svg,gif,webp,ico}')
        .pipe(gulpCopy(dest + '/img', {prefix: 2}));
});

// CopyLib
gulp.task('copyLib', ['cleanLib'], () => {
    return gulp.src('./blocks/**/lib.*.js')
        .pipe(gulpCopy(dest + '/lib', {prefix: 2}));
});

// CSS
gulp.task('cssDev', ['_preparetmp', 'cleanCSS'], () => {
    return gulp.src(['.tmp/style.scss', './blocks/**/*.{scss,css}'])
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(filter(['.tmp/style.scss']))
        .pipe(gulpInclude())
        .pipe(postcss(postcssProcessors, {syntax: scssSyntax}))
        .pipe(concat('style.css'))
        .pipe(urlAdjuster({prepend: 'img/'}))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(dest))
        .pipe(browserSync.reload({
            stream: true,
            match: '**/*.css'
        }));
});
gulp.task('css', ['_preparetmp', 'cleanCSS'], () => {
    return gulp.src(['.tmp/style.scss'])
        .pipe(gulpInclude())
        .pipe(postcss(postcssProcessors, {syntax: scssSyntax}))
        .pipe(concat('style.css'))
        // .pipe(rev())
        // .pipe(assetManifest({
        //     bundleName: 'themes/public/style.css',
        //     pathPrepend: 'themes/public/',
        //     log: true,
        //     manifestFile: 'manifest.json'
        // }))
        .pipe(urlAdjuster({prepend: 'img/'}))
        .pipe(csso())
        .pipe(gulp.dest(dest));
});

// Description
gulp.task('desc', () => {
    return gulp.src([
        './blocks/_bl-header/bl-header.desc.html',
        './blocks/!(_bl-)*/**/*.desc.html',
        './blocks/_bl-footer/bl-footer.desc.html'
    ])
        .pipe(gulpInclude())
        .pipe(concat('bl.html'))
        .pipe(gulp.dest(dest));
});

// Js
gulp.task('jsDev', ['cleanJs'], () => {
    return gulp.src(jsPath)
        .pipe(sourcemaps.init())
        .pipe(concat('script.js'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(dest))
        .pipe(browserSync.reload({
            stream: true,
            match: '**/*.js'
        }));
});

gulp.task('js', ['cleanJs'], () => {
    return gulp.src(jsPath)
        .pipe(concat('script.js'))
        .pipe(uglify().on('error', function(e){
            console.log(e);
            return this.end();
         }))
        .pipe(gulp.dest(dest));
});


gulp.task('rev', ['js','css'], () =>
// by default, gulp would pick `assets/css` as the base,
// so we need to set it explicitly:
gulp.src(['web/themes/public/*.css', 'web/themes/public/*.js'], {base: 'web'})
    .pipe(gulp.dest('web'))
    .pipe(rev())
    .pipe(gulp.dest('web'))
    .pipe(rev.manifest())
    .pipe(gulp.dest(dest))
);


// Watch
gulp.task('watch', ['cssDev', 'jsDev', 'desc', 'copyImg', 'copyLib', 'jade', 'serve'], () => {
    gulp.watch('./blocks/**/*.js', ['jsDev']);
    gulp.watch(['./blocks/**/*.jade','./bundles/*.jade'], ['jadeReload']);
    gulp.watch('./blocks/**/*.{scss,css}', ['cssDev']);
    gulp.watch(['./blocks/**/*.desc.html', './blocks/a-colors/a-colors.scss'], ['desc']);
    gulp.watch('./blocks/**/*.{jpg,png,svg,gif,webp,ico}', ['copyImg']);
    gulp.watch('./blocks/**/lib.*.js', ['copyLib']);
    watch('./blocks/**/*.js')
        .pipe(plumber({errorHandler: () => {}}))
        .pipe(shell(['eslint <%= file.path %>']));
});
gulp.task('watchFix', ['cssDev', 'jsDev', 'desc', 'copyImg', 'copyLib', 'jade'], () => {
    gulp.watch('./blocks/**/*.js', ['jsDev']);
    gulp.watch('./blocks/**/*.{scss,css}', ['cssDev']);
    gulp.watch(['./blocks/**/*.desc.html', './blocks/a-colors/a-colors.scss'], ['desc']);
    gulp.watch('./blocks/**/*.{jpg,png,svg,gif,webp,ico}', ['copyImg']);
    gulp.watch('./blocks/**/lib.*.js', ['copyLib']);
    watch('./blocks/**/*.js')
        .pipe(plumber({errorHandler: () => {}}))
        .pipe(shell(['eslint --fix <%= file.path %>']));
    watch('./blocks/**/*.{scss,css}')
        .pipe(shell(['csscomb <%= file.path %>']));
});

gulp.task('dev', ['cssDev', 'jsDev', 'desc', 'copyImg', 'copyLib', 'jade', 'rev']);

gulp.task('build', ['css', 'js', 'desc', 'copyImg', 'copyLib',  'jade', 'rev']);

gulp.task('default', ['build', 'rev']);


