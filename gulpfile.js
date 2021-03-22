require( 'dotenv' ).config();
const envConfig = process.env;

const gulp = require( 'gulp' );
const { series, parallel } = require( 'gulp' );
const sass = require( 'gulp-sass' );
// const babel = require( 'gulp-babel' );
// const concat = require( 'gulp-concat' );
const uglify = require( 'gulp-uglify' );
const rename = require( 'gulp-rename' );
const cleanCSS = require( 'gulp-clean-css' );
const clean = require( 'gulp-clean' );

const babelify = require( 'babelify' );
const browserify = require( 'browserify' );
const source = require( 'vinyl-source-stream' );
const buffer = require( 'vinyl-buffer' );
// const sourcemaps = require( 'gulp-sourcemaps' );
const autoprefixer  = require( 'gulp-autoprefixer' );


const paths = {
    css: {
        src: 'src/scss/**/*.scss',
        dest: 'assets/css/',
        fileName: 'style.js',
        watchSrc: 'src/**/*.scss',
    },
    js: {
        src: 'src/js/**/*.js',
        dest: 'assets/js/',
        fileName: 'scripts.js',
        watchSrc: 'src/**/*.js',
    },
    php: {
        watchSrc: [ 'src/**/*.php', '*.php', 'template-parts/**/*.php' ],
    },
};

// PUBLISH HOWTO: 
// If you like to copy your files to another folder after build make 
// `.env` file with content `PUBLISH_PATH=path_to_your_folder`, 
// e.g. `PUBLISH_PATH=../../../../../Applications/MAMP/htdocs/`
// Have a look at `publishConfig` which files to include / exclude
// and how to name your created destination folder
// 
// NOTE: within `src` all (1..n) non-negative globs must be followed by (0..n) only negative globs
const publishConfig = {
    "src": [
        "**/*",
        "!**/node_modules",
        "!**/node_modules/**", 
    ],
    "base": ".",
    "folderName": "bsx-wordpress"
};


const cssFolderClean = ( cb ) => { 

    return gulp.src( paths.css.dest, { read: false, allowEmpty: true } )
        .pipe( clean() )
    ;

    cb();
}

exports.css_clean = cssFolderClean;



const jsFolderClean = ( cb ) => {

    return gulp.src( paths.js.dest, { read: false, allowEmpty: true } )
        .pipe( clean() )
    ;

    cb();
}

exports.js_clean = jsFolderClean;


// const css = ( cb ) => {
//   return gulp.src( paths.css.src, { sourcemaps: true } )
//     .pipe( sass() )
//     .pipe( cleanCSS() )
//     // pass in options to the stream
//     .pipe( rename( {
//       basename: 'style',
//       suffix: '.min'
//     } ) )
//     .pipe( gulp.dest( paths.css.dest ) );

//   cb();
// }




const makeCss = ( cb ) => {

    return gulp.src( paths.css.src, { sourcemaps: true } )
        // .pipe( sourcemaps.init() )
        .pipe( sass().on( 'error', sass.logError ) )
        .pipe( autoprefixer() )
        // .pipe( sourcemaps.write( '.' ) )
        .pipe( gulp.dest( paths.css.dest ) )
    ;

    cb();
}

const cssCleanAndMinify = ( cb ) => {

    return gulp.src( paths.css.dest + '/**/*.css' )
        .pipe( cleanCSS( { debug: true }, ( details ) => {
            console.log( details.name + ': ' + details.stats.originalSize );
            console.log( details.name + ': ' + details.stats.minifiedSize );
        } ) )
        .pipe( rename( ( path ) => {
            path.basename += '.min';
        } ) )
        .pipe( gulp.dest( paths.css.dest ) )
    ;

    cb();
}


const css = series( 
    cssFolderClean,
    makeCss, 
    cssCleanAndMinify,
);

exports.css = css;


const jsMinify = ( cb ) => {

    return gulp.src( paths.js.dest + '*.js' )
        .pipe( uglify() )
        .pipe( rename( { suffix: '.min' } ) )
        .pipe( gulp.dest( paths.js.dest ) )
    ;

    cb();
}


// const js = ( cb ) => {

//   return gulp.src( paths.js.src, { sourcemaps: true } )
//     .pipe( babel() )
//     .pipe( concat( paths.js.fileName ) )
//     .pipe( gulp.dest( paths.js.dest ) )
//   ;

//    cb();
// }

const makeJs = ( cb ) => {

    return browserify( {
            entries: [ './src/js/index.js' ],
            debug: true,
            transform: [
                babelify.configure( { presets: [ '@babel/preset-env' ] } ),
            ],
        } )
        .bundle()
        .pipe( source( 'scripts.js' ) )
        .pipe( gulp.dest( paths.js.dest ) )
        .pipe( buffer() )
        // .pipe( sourcemaps.init() )
        // .pipe( sourcemaps.write() )
    ;

    cb();
}

const js = series( 
    jsFolderClean,
    makeJs, 
    jsMinify,
);

exports.js = js;


// NOTE: take care at this path since you’re deleting files outside your project
const publishFullPath = envConfig.PUBLISH_PATH + '/' + publishConfig.folderName;


const publishFolderDelete = ( cb ) => {

    if ( !! envConfig.PUBLISH_PATH && !! publishConfig.folderName ) {
        // console.log( 'delete: ' + publishFullPath );
        return gulp.src( publishFullPath, { read: false, allowEmpty: true } )
            .pipe( clean( { force: true } ) ) // NOTE: take care at this command since you’re deleting files outside your project
        ;
    }
    else {
        // do nothing
    }

    cb();
}

const publishFolderCreate = ( cb ) => {

    if ( !! envConfig.PUBLISH_PATH && !! publishConfig.folderName ) {
        // console.log( 'create: ' + publishFullPath + ' (src: ' + publishConfig.src + ', base: ' + publishConfig.base + ')' );
        return gulp.src( publishConfig.src, { base: publishConfig.base } )
            .pipe( gulp.dest( publishFullPath ) )
        ;
    }
    else {
        // log note, do nothing
        console.log( 'Note: No publishing done since publish configuration empty.' );
    }

    cb();
}

const publish = series(
    // copy all project but `node_modules` to configured dest
    publishFolderDelete,
    publishFolderCreate,
);

exports.publish = publish;



function cssWatch() {
    gulp.watch( paths.css.watchSrc, 
        series(
            css,
            publish,
        )
    );
}

exports.css_watch = cssWatch;

function jsWatch() {
    gulp.watch( paths.js.watchSrc, 
        series(
            js,
            publish,
        )
    );
}

exports.js_watch = jsWatch;


function allWatch() {
    gulp.watch( paths.css.watchSrc, 
        series(
            css,
            publish,
        ) 
    );
    gulp.watch( paths.js.watchSrc, 
        series(
            js,
            publish,
        ) 
    );
    gulp.watch( paths.php.watchSrc, publish );
}

exports.watch = allWatch;


const build = series(
    parallel(
        css, 
        js,
    ),
    publish,
);

exports.build = build;




