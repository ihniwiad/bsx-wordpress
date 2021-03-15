class BrowserAnalytics {
  isIos() {
    return /iPad|iPhone|iPod/.test( navigator.platform ) && ! window.MSStream
  }
  isAndroid() {
    return /(android)/i.test( navigator.userAgent )
  }
  isWin() {
    return navigator.platform.indexOf( 'Win' ) > -1
  }
  isMobileIe() {
    return navigator.userAgent.match( /iemobile/i )
  }
  isWinPhone() {
    return navigator.userAgent.match( /Windows Phone/i )
  }
  getIosVersion() {
    return parseInt(
      ( '' + ( /CPU.*OS ([0-9_]{1,5})|(CPU like).*AppleWebKit.*Mobile/i.exec( navigator.userAgent ) || [ 0,'' ] )[ 1 ] )
      .replace( 'undefined', '3_2' ).replace( '_', '.' ).replace( /_/g, '' )
    ) || false
  }
  getIosFullVersion() {
    return ( '' + ( /CPU.*OS ([0-9_]{1,9})|(CPU like).*AppleWebKit.*Mobile/i.exec( navigator.userAgent ) || [ 0,'' ] )[ 1 ] )
      .replace( 'undefined', '3_2' ) || false
  }
  addBodyClassNames() {
    if ( this.isIos() ) {
      // document.body.className += ' is-ios';
      document.body.classList.add( 'is-ios' )

      // detect version (required for fixes)
      const iosMaxVersion = 11;
      const iosVersion = this.getIosVersion()
      if ( iosVersion !== false ) {
        // document.body.className += ' ios' + iosVersion;
        document.body.classList.add( 'ios' + iosVersion )
        for ( let i = iosVersion; i <= iosMaxVersion; i++ ) {
          // document.body.className += ' ioslte' + i;
          document.body.classList.add( 'ioslte' + i )
        }
      }

    }
    else if ( this.isAndroid() ) {
      // document.body.className += ' is-android';
      document.body.classList.add( 'is-android' )
    }
    else if ( this.isWin() ) {
      // document.body.className += ' is-win';
      document.body.classList.add( 'is-win' )
      if ( this.isMobileIe() ) {
        // document.body.className += ' is-mobile-ie';
        document.body.classList.add( 'is-mobile-ie' )
      }
    }
    if ( this.isWinPhone() ) {
      // document.body.className += ' is-win-phone';
      document.body.classList.add( 'is-win-phone' )
    }
    
    const detectIe = () => {
      const ua = window.navigator.userAgent;
      const msie = ua.indexOf( 'MSIE ' );
      if ( msie > 0 ) {
        return parseInt( ua.substring( msie + 5, ua.indexOf( '.', msie ) ), 10 );
      }
      const trident = ua.indexOf( 'Trident/' );
      if ( trident > 0 ) {
        const rv = ua.indexOf( 'rv:' );
        return parseInt( ua.substring( rv + 3, ua.indexOf( '.', rv ) ), 10 );
      }
      const edge = ua.indexOf( 'Edge/' );
      if ( edge > 0 ) {
        return parseInt( ua.substring( edge + 5, ua.indexOf( '.', edge ) ), 10 );
      }
      return false;
    }

    // detect ie gt 9
    const ieMaxVersion = 14;
    const ieVersion = detectIe();
    const isIe = ( ieVersion !== false );
    if ( isIe && ieVersion > 9 ) {
      // document.body.className += ' ie ie' + ieVersion;
      document.body.classList.add( 'ie' )
      document.body.classList.add( 'ie' + ieVersion )
      for ( let i = ieVersion; i <= ieMaxVersion; i++ ) {
        // document.body.className += ' ielte' + i;
        document.body.classList.add( 'ielte' + i )
      }
    }

    document.body.classList.add( 'browser-tested' )
    // console.log( 'browser-tested' )
  }
}


// init
const BA = new BrowserAnalytics
// test.addBodyClassNames()

export default BA


