import DomData from './../../js/dom/dom-data'
import MakeFnElems from './../../js/dom/function-elements'
import DomFn from './../../js/utilities/dom-functions'


// params

const KEY = 'ape'
const DEFAULT_APPEARED_CLASS = 'appeared'
const DEFAULT_NON_APPEARED_CLASS = 'non-appeared'
const DEFAULT_ADD_CLASS_DELAY = 200
const TRANSITION_TOLERANCE_DELAY = 10 // required to enable clean css transition, e.g. add class containing transition, then change style triggering transition


// class

class AppearEffects {

  init() {

    // console.log( 'init: ' + elem.bsxData.id )
    const appearUpdate = () => {

      if ( DomData.getElems( KEY ) ) {

        const elems = DomData.getElems( KEY )

        elems.forEach( ( elem ) => {

          const conf = DomFn.getConfigFromAttr( elem, KEY )
          const APPEARED_CLASS = ( conf != null && typeof conf.appearedClass !== 'undefined' ) ? conf.appearedClass : DEFAULT_APPEARED_CLASS
          const NON_APPEARED_CLASS = ( conf != null && typeof conf.nonAppearedClass !== 'undefined' ) ? conf.nonAppearedClass : DEFAULT_NON_APPEARED_CLASS
          const ADD_CLASS_DELAY = ( conf != null && typeof conf.addClassDelay !== 'undefined' ) ? conf.addClassDelay : DEFAULT_ADD_CLASS_DELAY
        
          // TODO: get elems from dom data each event
          // TODO: remove done elems from dom data
          const elemY = elem.offsetTop
          const elemX = elem.offsetLeft
          const elemHeight = elem.offsetHeight
          const elemWidth = elem.offsetWidth
          const windowScrollY = window.pageYOffset// document.documentElement.scrollTop
          const windowScrollX = window.pageXOffset// document.documentElement.scrollLeft
          const windowHeight = window.innerHeight
          const windowWidth = window.innerWidth
          // check scrollTop / Left and window height / width
          // console.log( 'id: ' + elem.bsxData.id + ' – top: ' + elemY + ' – left: ' + elemX )

          const aboveTheFold = () => {
            return elemY + elemHeight <= windowScrollY
          }
          const belowTheFold = () => {
            return elemY > windowScrollY + windowHeight
          }
          const leftTheFold = () => {
            return elemX + elemWidth <= windowScrollX
          }
          const rightTheFold = () => {
            return elemX > windowScrollX + windowWidth
          }
          if ( 
            ! ( aboveTheFold() || belowTheFold() ) 
            && ! ( rightTheFold() || rightTheFold() ) 
            && typeof elem.bsxData !== 'undefined' 
          ) {
            // on screen

            if ( ! elem.bsxData.appeared ) {
              // not already appeared – appear

              console.log( 'id: ' + elem.bsxData.id + ' on screen' )

              setTimeout( () => {
                DomFn.removeClassNames( elem, NON_APPEARED_CLASS )
                DomFn.addClassNames( elem, APPEARED_CLASS )
              }, ADD_CLASS_DELAY )

              elem.bsxData.appeared = true
   
              if ( ! ( conf != null && typeof conf.repeat !== 'undefined' && conf.repeat === true ) ) {
                // remove from domData
                console.log( 'DomData.removeElem( elem, KEY )' )
                DomData.removeElem( elem, KEY )
              }

            }
            else {
              // already appeared and not disappeared again – do nothing
            }

          }
          else {
            // not on screen

            if ( elem.bsxData.appeared ) {
              // already appeared – disappear

              console.log( 'id: ' + elem.bsxData.id + ' off screen' )
              elem.bsxData.appeared = false

              setTimeout( () => {
                DomFn.removeClassNames( elem, APPEARED_CLASS )
                DomFn.addClassNames( elem, NON_APPEARED_CLASS )
              }, ADD_CLASS_DELAY )

            }
            else {
              // already disappeared and not appeared again – do nothing
            }

          }

        } )

      } // /if

    } // /appearUpdate()

    window.addEventListener( 'scroll', appearUpdate, false )
    window.addEventListener( 'touchmove', appearUpdate, false )
    window.addEventListener( 'resize', appearUpdate, false )

    // check initial state
    appearUpdate()
  }
}


// init

const effects = new AppearEffects()
effects.init()






// // class

// class AppearEffects {
//   constructor( elem ) {
//     elem = elem
//     conf = DomFn.getConfigFromAttr( elem, KEY )
//     APPEARED_CLASS = ( conf != null && typeof conf.appearedClass ) !== 'undefined' ? conf.appearedClass : DEFAULT_APPEARED_CLASS
//     ADD_CLASS_DELAY = ( conf != null && typeof conf.addClassDelay ) !== 'undefined' ? conf.addClassDelay : DEFAULT_ADD_CLASS_DELAY
//   }
//   init() {
//     // console.log( 'init: ' + elem.bsxData.id )
//     const appearUpdate = () => {
//       // TODO: get elems from dom data each event
//       // TODO: remove done elems from dom data
//       const elemY = elem.offsetTop
//       const elemX = elem.offsetLeft
//       const elemHeight = elem.offsetHeight
//       const elemWidth = elem.offsetWidth
//       const windowScrollY = window.pageYOffset// document.documentElement.scrollTop
//       const windowScrollX = window.pageXOffset// document.documentElement.scrollLeft
//       const windowHeight = window.innerHeight
//       const windowWidth = window.innerWidth
//       // check scrollTop / Left and window height / width
//       // console.log( 'id: ' + elem.bsxData.id + ' – top: ' + elemY + ' – left: ' + elemX )

//       const aboveTheFold = () => {
//         return elemY + elemHeight <= windowScrollY
//       }
//       const belowTheFold = () => {
//         return elemY > windowScrollY + windowHeight
//       }
//       const leftTheFold = () => {
//         return elemX + elemWidth <= windowScrollX
//       }
//       const rightTheFold = () => {
//         return elemX > windowScrollX + windowWidth
//       }
//       if ( 
//         ! ( aboveTheFold() || belowTheFold() ) 
//         && ! ( rightTheFold() || rightTheFold() ) 
//         && typeof elem.bsxData !== 'undefined' 
//         && ! elem.bsxData.appeared
//       ) {
//         console.log( 'id: ' + elem.bsxData.id + ' on screen' )
//         elem.bsxData.appeared = true

//         setTimeout( () => {
//           elem.classList.add( APPEARED_CLASS )
//         }, ADD_CLASS_DELAY )
//       }
//     }

//     window.addEventListener( 'scroll', appearUpdate )
//     window.addEventListener( 'resize', appearUpdate )

//     // check initial state
//     appearUpdate()
//   }
// }


// // init

// if ( DomData.getElems( KEY ) ) {
//   DomData.getElems( KEY ).forEach( ( elem ) => {
//     const currentElem = new AppearEffects( elem )
//     currentElem.init()
//   } )
// }

