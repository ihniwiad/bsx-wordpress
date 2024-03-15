require( 'dotenv' ).config();
const envConfig = process.env;

const { src, dest, watch, series, parallel } = require( 'gulp' );

const sass = require( 'gulp-sass' );
sass.compiler = require( 'sass' );

// const babel = require( 'gulp-babel' );
const concat = require( 'gulp-concat' );
const uglify = require( 'gulp-uglify' );
const rename = require( 'gulp-rename' );
const cleanCSS = require( 'gulp-clean-css' );
const clean = require( 'gulp-clean' );

const babelify = require( 'babelify' );
const browserify = require( 'browserify' );
const source = require( 'vinyl-source-stream' );
const buffer = require( 'vinyl-buffer' );
const sourcemaps = require( 'gulp-sourcemaps' );
const autoprefixer  = require( 'gulp-autoprefixer' );
const fs = require( 'fs' );
const replace = require( 'gulp-string-replace' );


const paths = {
    css: {
        src: 'src/scss/**/*.scss',
        dest: 'assets/css/',
        fileName: 'style.css',
        watchSrc: 'src/**/*.scss',
    },
    fonts: {
        dest: 'assets/fonts/',
        relativePath: '../fonts',
    },
    js: {
        // src: 'src/js/**/*.js',
        // src: 'src/js/index.js',
        dest: 'assets/js/',
        fileName: 'scripts.js',
        watchSrc: 'src/**/*.js',
    },
    vendorJs: {
        srcJsonFile: 'src/js/vendor-js.json',
        dest: 'assets/js/',
        fileName: 'vendor.js',
        watchSrc: 'src/js/vendor-js.json',
    },
    publish: {
        watchSrc: [ 
            'src/**/*.php', 
            '*.php', 
            'template-parts/**/*.php', 
            'inc/**/*.(php|svg)',
            'languages/*.mo',
            'assets/img/**/*',
            'img/**/*',
        ],
    },
};

const templates = {
    fontPreload: {
        before: '<?php \n    // this file was generated by gulpfile.js \n    print( \'\n',
        main: '<link rel="preload" href="\' . $assetsPath . \'###HREF###" as="font" type="font/###TYPE###" crossorigin>',
        after: '\' );',
        fileName: 'fonts-preloads.php',
        destPath: paths.css.dest,
    },
};

// search pattern – include `@font-face` followed by letters, numbers, minus, underscore, dot, colon, double quote, single quote, semicolon, slash, ... ,line break, tab
const replacePatterns = {
    cssFonts: {
        match: /@font-face+([a-zA-Z0-9-/-_.:;,"'/(){ ?=#&\n\t])*/g,
        check: 'font-display:',
        add: ' \n  font-display: fallback; ',
    },
    cssFontsSrc: {
        match: /@font-face+([a-zA-Z0-9-/-_.:;,"'/(){ ?=#&\n\t])+url\((\"|'|)(node_modules|src)\/+([a-zA-Z0-9-/-_@.:;,"'/(){ ?=#&\n\t])*/g,
    }
}


// PUBLISH HOWTO: 
// If you like to copy your files to another folder after build make 
// `.env` file with content `FOLDER_NAME=your_folder_name` and `PUBLISH_PATH=path_to_your_folder`, 
// e.g.: 
// `FOLDER_NAME=my_project`
// `PUBLISH_PATH=../../../../../Applications/MAMP/htdocs/`
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
    "folderName": ( !! envConfig.FOLDER_NAME ? envConfig.FOLDER_NAME : '' )
};


const cssFolderClean = ( cb ) => { 

    return src( paths.css.dest, { read: false, allowEmpty: true } )
        .pipe( clean() )
    ;

    cb();
}

exports.css_clean = cssFolderClean;


const jsFolderClean = ( cb ) => {

    return src( paths.js.dest, { read: false, allowEmpty: true } )
        .pipe( clean() )
    ;

    cb();
}

exports.js_clean = jsFolderClean;


// const css = ( cb ) => {
//   return src( paths.css.src, { sourcemaps: true } )
//     .pipe( sass() )
//     .pipe( cleanCSS() )
//     // pass in options to the stream
//     .pipe( rename( {
//       basename: 'style',
//       suffix: '.min'
//     } ) )
//     .pipe( dest( paths.css.dest ) );

//   cb();
// }




const makeCss = ( cb ) => {

    return src( paths.css.src, { sourcemaps: true } )
        .pipe( sourcemaps.init() )
        .pipe( sass().on( 'error', sass.logError ) )
        .pipe( autoprefixer() )
        .pipe( sourcemaps.write( '.' ) )
        .pipe( dest( paths.css.dest ) )
    ;

    cb();
}

const cssCleanAndMinify = ( cb ) => {

    return src( paths.css.dest + '/**/*.css' )
        .pipe( cleanCSS( { debug: true }, ( details ) => {
            console.log( details.name + ': ' + details.stats.originalSize );
            console.log( details.name + ': ' + details.stats.minifiedSize );
        } ) )
        .pipe( rename( ( path ) => {
            path.basename += '.min';
        } ) )
        .pipe( dest( paths.css.dest ) )
    ;

    cb();
}


const makeFontsPreloads = ( cb ) => {
    // this function needs to be executed after css has been built

    // get fonts from minimized css file
    const cssFileContent = String( fs.readFileSync( paths.css.dest + 'style.min.css' ) );
    const allowedFormats = [ 'woff2', 'woff' ]; //, ordered, add woff2 if available, else add woff if availabe, else do nothing
    const fontsList = [];
    const addedFontsUrlTruncs = [];
    const fontsSnippets = cssFileContent.match( replacePatterns.cssFonts.match );

    fontsSnippets.forEach( ( fontSnippet, index ) => {

        // extract font sources – get content between `src:` and `;`
        const fontSrcList = fontSnippet.split( 'src:' );
        const lastFontSrc = fontSrcList[ fontSrcList.length - 1 ].split( ';' )[ 0 ];
        const singleFontExplode = lastFontSrc.split( ',' );

        for ( let j = 0; j < singleFontExplode.length; j++ ) {

            // extract each font’s url and format
            const urlFormatExplode = singleFontExplode[ j ].split( ' ' );
            let url = urlFormatExplode[ 0 ].replace( 'url(', '' ).replace( ')', '' ).replace( '../', '' );
            const format = urlFormatExplode[ 1 ].replace( 'format("', '' ).replace( '")', '' );

            // get url trunc to avoid duplication when woff2 & woff available
            const urlTrunc = url.split( '.' )[ 0 ];

            // check if allowed format and not added yet, then push to list
            if ( allowedFormats.indexOf( format ) != -1 && addedFontsUrlTruncs.indexOf( urlTrunc ) == -1 ) {
                fontsList.push(
                    {
                        url: url,
                        format: format,
                    }
                );
                // remember to avoid duplication when woff2 & woff available
                addedFontsUrlTruncs.push( urlTrunc );
            }
        }

    } ); 

    // remove duplicates (caused by different css font names for same font file)
    const uniqueFontsList = [ ...new Map( fontsList.map( item => [ item[ 'url' ], item ] ) ).values() ];
    const template = templates.fontPreload.main;

    let preloadsFileContent = templates.fontPreload.before;

    uniqueFontsList.forEach( ( item, index ) => {
        preloadsFileContent += template.replace( '###HREF###', item.url ).replace( '###TYPE###', item.format ) + '\n';
    } ); 

    preloadsFileContent += templates.fontPreload.after;

    fs.writeFileSync( templates.fontPreload.destPath + templates.fontPreload.fileName, preloadsFileContent );

    cb();
}


const fontsFolderClean = ( cb ) => { 

    return src( paths.fonts.dest, { read: false, allowEmpty: true } )
        .pipe( clean() )
    ;

    cb();
}


const copyFontsToFolder = ( cb ) => {
    // this function needs to be executed after css has been built and minified

    // get fonts from minimized css file
    const cssFileContent = String( fs.readFileSync( paths.css.dest + 'style.min.css' ) );
    const copyFontsStack = [];
    const fontfaceSnippets = cssFileContent.match( replacePatterns.cssFonts.match );

    fontfaceSnippets.forEach( ( fontfaceSnippet, index ) => {

        // extract font sources – get content between `src:` and `;`
        const fontSrcList = fontfaceSnippet.split( 'src:' );
        const lastFontSrc = fontSrcList[ fontSrcList.length - 1 ].split( ';' )[ 0 ];
        const singleFontExplode = lastFontSrc.split( ',' );

        for ( let j = 0; j < singleFontExplode.length; j++ ) {

            // extract each font’s url and format
            const urlFormatExplode = singleFontExplode[ j ].split( ' ' );
            let url = urlFormatExplode[ 0 ].replace( 'url(', '' ).replace( ')', '' );
            if ( url.indexOf( '?' ) > -1 ) {
                url = url.split( '?' )[ 0 ]
            }
            if ( url.indexOf( '#' ) > -1 ) {
                url = url.split( '#' )[ 0 ]
            }

            // check if copy files into fonts folder
            if ( url.indexOf( 'node_modules' ) === 0 || url.indexOf( 'src' ) === 0 ) {
                // remember font to (later) copy font into fonts folder
                copyFontsStack.push( url );
            }
            else {
                // do nothing
            }
        }

    } ); 

    // copy fonts into fonts folder
    if ( copyFontsStack.length > 0 ) {
        let stream;
        copyFontsStack.forEach ( ( fontPath ) => {
            stream = src( fontPath );
            stream = stream.pipe( dest( paths.fonts.dest ) );
            //LOG += fontPath + ' ––> ' + paths.fonts.dest + '\n';
        } );
        //fs.writeFileSync( LOG_FILE_PATH, LOG );
        return stream;
    }

    cb();
}


const cssChangeFontsPathsToFolder = ( cb ) => {

    return src( paths.css.dest + '/**/*.css' )
        .pipe( replace( replacePatterns.cssFontsSrc.match, ( match ) => {

            // get url: url("...") / url('...') / url(...)
            const fontFaceExplode = match.split( 'url(' )
            let rebuildFontFace = fontFaceExplode[ 0 ]

            // ignore 1st item since not containing any src
            for ( let i = 1; i < fontFaceExplode.length; i++ ) {
                const srcListExplode = fontFaceExplode[ i ].split( ')' )
                const src = srcListExplode[ 0 ]

                // get only file name from src path (might contain closing double or single quote and params)
                const fontFileExplode = src.split( '/' )
                const fontFile = fontFileExplode[ fontFileExplode.length - 1 ]
                const relativeSrc = paths.fonts.relativePath + '/' + fontFile

                rebuildFontFace += 'url('

                // keep opening double or single quote if contained
                if ( fontFileExplode[ 0 ].substring( 0, 1 ) === '"' ) {
                    rebuildFontFace += '"'
                }
                else if ( fontFileExplode[ 0 ].substring( 0, 1 ) === "'" ) {
                    rebuildFontFace += "'"
                }

                rebuildFontFace += relativeSrc

                // add rest after url, ignore 1st item since already rebuilt by src
                for ( let i = 1; i < srcListExplode.length; i++ ) {
                    rebuildFontFace += ')' + srcListExplode[ i ]
                }
            }

            return rebuildFontFace;
        } ) )
        .pipe( dest( paths.css.dest ) )
    ;

    cb();

}


const cssFontsOptimize = ( cb ) => {

    return src( paths.css.dest + '/**/*.css' )
        .pipe( replace( replacePatterns.cssFonts.match, ( match ) => {
            if ( match.indexOf( replacePatterns.cssFonts.check ) == -1 ) {
                return match + replacePatterns.cssFonts.add;
            }
            else {
                return match;
            }
        } ) )
        .pipe( dest( paths.css.dest ) )
    ;

    cb();

}


const makeAndLinkFontsFolder = series (
    fontsFolderClean,
    copyFontsToFolder,
    cssChangeFontsPathsToFolder,
);


const css = series( 
    cssFolderClean,
    makeCss, 
    cssFontsOptimize, 
    cssCleanAndMinify,
    makeAndLinkFontsFolder,
    makeFontsPreloads,
);

exports.css = css;


const jsMinify = ( cb ) => {

    return src( paths.js.dest + '*.js' )
        .pipe( uglify() )
        .pipe( rename( { suffix: '.min' } ) )
        .pipe( dest( paths.js.dest ) )
    ;

    cb();
}


// const js = ( cb ) => {

//   return src( paths.js.src, { sourcemaps: true } )
//     .pipe( babel() )
//     .pipe( concat( paths.js.fileName ) )
//     .pipe( dest( paths.js.dest ) )
//   ;

//    cb();
// }

const makeJs = ( cb ) => {

    return browserify( {
            // entries: [ './' . paths.js.src ],
            entries: [ './src/js/index.js' ],
            debug: true,
            transform: [
                babelify.configure( { presets: [ '@babel/preset-env' ] } ),
            ],
        } )
        .bundle()
        .pipe( source( 'scripts.js' ) )
        .pipe( dest( paths.js.dest ) )
        .pipe( buffer() )
        // .pipe( sourcemaps.init() )
        // .pipe( sourcemaps.write() )
    ;

    cb();
}

const makeVendorJs = ( cb ) => {

    const srcJsonFileContent = JSON.parse( fs.readFileSync( paths.vendorJs.srcJsonFile ) );
    const VENDOR_STACK = ( typeof srcJsonFileContent !== 'undefined' && typeof srcJsonFileContent.use !== 'undefined' ) ? srcJsonFileContent.use : [];

    return src( VENDOR_STACK )
        .pipe( sourcemaps.init() )
        .pipe( concat( paths.vendorJs.fileName ) )
        .pipe( sourcemaps.write( '.' ) )
        .pipe( dest( paths.vendorJs.dest ) )
    ;

    cb();
}

// exports.vendor_js = makeVendorJs;

const js = series( 
    jsFolderClean,
    parallel( makeJs, makeVendorJs ),
    jsMinify,
);

exports.js = js;


// NOTE: take care at this path since you’re deleting files outside your project
const publishFullPath = envConfig.PUBLISH_PATH + publishConfig.folderName;


const publishFolderDelete = ( cb ) => {

    if ( !! envConfig.PUBLISH_PATH && !! publishConfig.folderName ) {
        console.log( 'delete: ' + publishFullPath );
        return src( publishFullPath, { read: false, allowEmpty: true } )
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
        // console.log( 'src: ' + publishConfig.src + ', base: ' + publishConfig.base );
        console.log( 'create: ' + publishFullPath );
        return src( publishConfig.src, { base: publishConfig.base } )
            .pipe( dest( publishFullPath ) )
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
    watch( paths.css.watchSrc, 
        series(
            css,
            publish,
        )
    );
}

exports.css_watch = cssWatch;

function jsWatch() {
    watch( [ paths.js.watchSrc, paths.vendorJs.watchSrc ],
        series(
            js,
            publish,
        )
    );
}

exports.js_watch = jsWatch;


function allWatch() {
    watch( paths.css.watchSrc, 
        series(
            css,
            publish,
        ) 
    );
    watch( paths.js.watchSrc, 
        series(
            js,
            publish,
        ) 
    );
    watch( paths.vendorJs.watchSrc, 
        series(
            js,
            publish,
        ) 
    );
    watch( paths.publish.watchSrc, publish );
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




