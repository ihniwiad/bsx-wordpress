// Imports


// import DataCategory from './test/test-class.js'
// import Test from './test.js'

// import DomData from './dom/dom-data'
// import DomFn from './utilities/dom-functions'
// import Selectors from './utilities/selectors'


// const FUNCTION_ATTR = Selectors.functionAttr()

// TEST – TODO: remove
// test elem data
// const functionElems = document.querySelectorAll( '[' + FUNCTION_ATTR + ']' )

// $( functionElems ).addClass( 'TEST' )


// add to DomData
// functionElems.forEach( elem => {
//   const key = elem.getAttribute( FUNCTION_ATTR )
//   // console.log( 'key: ' + key )
//   DomData.addElem( elem, key )

//   // TEST – TODO: remove – show id on doubleclick
// //   elem.addEventListener( 'dblclick', ( event ) => {
// //     alert( `id: ${ event.target.bsxData.id }
// // key: '${ event.target.bsxData.key }'` )
// //   } )
// } )


// files

// import './test/index.js';
import './test/index.js';
import './../libs/test/index.js';

// import './utilities/utils.js';
// import './browser-analytics/index.js';
import './../libs/accordion/index.js';
import './../libs/appear-effects/index.js';
import './../libs/nav/multilevel-dropdown.js';
import './../libs/toggle/toggle.js';
import './leg-utils/remote-event/remote-event.js';




// const KEY = 'acc'
// const DEFAULT_TARGET_OPENED_CLASS = 'open'
// const DEFAULT_TRIGGER_OPENED_CLASS = 'open'
// const DEFAULT_ALLOW_MULTI_OPEN = true

// // TODO: childNodes, get height

// DomData.getElems( KEY ).forEach( ( trigger ) => {

//   // get accordion
//   const acc = trigger.closest( '[data-acc]' )

//   // get config
//   const conf = DomFn.getConfigFromAttr( acc, KEY )
//   console.log( 'conf: ' + JSON.stringify( conf ) )
//   const ALLOW_MULTI_OPEN = ( conf != null && typeof conf.multipleOpen ) !== 'undefined' ? conf.multipleOpen : DEFAULT_ALLOW_MULTI_OPEN
//   const TARGET_OPENED_CLASS = ( conf != null && typeof conf.targetOpenedClass ) !== 'undefined' ? conf.targetOpenedClass : DEFAULT_TARGET_OPENED_CLASS
//   const TRIGGER_OPENED_CLASS = ( conf != null && typeof conf.triggerOpenedClass ) !== 'undefined' ? conf.triggerOpenedClass : DEFAULT_TRIGGER_OPENED_CLASS

//   const transitionTolerance = 10

//   // init
//   if ( ! ALLOW_MULTI_OPEN && trigger.getAttribute( 'aria-expanded' ) === 'true' ) {
//     // remember initial open status item
//     acc.bsxData = { recentTrigger: trigger }
//     // set disabled (might no be already set)
//     trigger.setAttribute( 'aria-disabled', 'true' )
//   }

//   const getTargetFromTrigger = ( trigger ) => {
//     const item = trigger.closest( '[data-acc-itm]' )
//     const targetId = trigger.getAttribute( 'aria-controls' )
//     return item.querySelector( `#${targetId}` )
//   }
//   const getTriggerFromEvent = ( event ) => {
//     return event.target.getAttribute( FUNCTION_ATTR ) != null ? event.target : event.target.closest( '[' + FUNCTION_ATTR + ']' )
//   }
//   const close = ( trigger ) => {
//     if ( ! trigger.hasAttribute( 'aria-disabled' ) || trigger.getAttribute( 'aria-disabled' ) === 'false' ) {
//       trigger.setAttribute( 'aria-expanded', 'false' )
//       trigger.classList.remove( TRIGGER_OPENED_CLASS )
//       const target = getTargetFromTrigger( trigger )
//       const targetInner = target.querySelector( '[acc-cnt-inr]' )
//       const targetInnerHeight = targetInner.offsetHeight
//       // set height before remove opened class
//       target.style.height = targetInnerHeight + 'px'
//       setTimeout( () => {
//         // remove opened class
//         target.classList.remove( TARGET_OPENED_CLASS )
//         setTimeout( () => {
//           // remove height to init transition
//           target.style.height = ''
//         }, transitionTolerance )
//       }, transitionTolerance )
//     }
//   }
//   const open = ( trigger ) => {
//     trigger.setAttribute( 'aria-expanded', 'true' )
//     trigger.classList.add( TRIGGER_OPENED_CLASS )
//     const target = getTargetFromTrigger( trigger )
//     const targetInner = target.querySelector( '[acc-cnt-inr]' )
//     const targetInnerHeight = targetInner.offsetHeight
//     target.classList.add( TARGET_OPENED_CLASS )
//     target.style.height = targetInnerHeight + 'px'
//     // remove height after transition ended
//     const transitionDuration = DomFn.getTransitionDuration( target ) + transitionTolerance
//     setTimeout( () => {
//       target.style.height = ''
//     }, transitionDuration )

//     if ( ! ALLOW_MULTI_OPEN ) {
//       // disable clicked
//       trigger.setAttribute( 'aria-disabled', 'true' )
//       if ( typeof acc.bsxData !== 'undefined' && typeof acc.bsxData.recentTrigger !== 'undefined' ) {
//         // remove disabled from recent
//         acc.bsxData.recentTrigger.removeAttribute( 'aria-disabled' )
//         // close open (not clicked) item
//         close( acc.bsxData.recentTrigger )
//       }
//       // remember clicked item
//       acc.bsxData = { recentTrigger: trigger }
//     }
//   }

//   // set event listener
//   // .${trigger.bsxData.key}.${trigger.bsxData.id}
//   trigger.addEventListener( 'click', ( event ) => {
//     // console.log( 'click' )
//     // event target might be trigger intself or triggers child
//     const trigger = getTriggerFromEvent( event )

//     // decide wether open or close
//     if ( trigger.getAttribute( 'aria-expanded' ) === 'false' ) {
//       open( trigger )
//     } else {
//       close( trigger )
//     }

//   } )

// } )







// const testClass = 'HELLO-TEST'

// // get from DomData by key
// DomData.getElems( 'key_1' ).forEach( ( elem ) => {
//   // DomFn.addClass( elem, testClass )
//   elem.classList.add( testClass )
// } )

// // TEST – TODO: remove – remove class
// DomData.getElems( 'key_1' ).forEach( ( elem ) => {
//   elem.addEventListener( 'click', ( event ) => {
//     // DomFn.removeClass( event.target, testClass )
//     event.target.classList.remove( testClass )
//   } )
// } )

// get from DomData by key
// DomData.getElems( 'key_5' ).forEach( ( elem ) => {
//   DomFn.addClass( elem, testClass + '-5' )
// } )





// file imports

// import './another-file.js';


// all the rest

// const test = 'test';

// console.log( `Hello from ${test} :)` );





// const functionElems = document.querySelectorAll( '[data-bsx]' ); 

// functionElems.forEach( ( item, index ) => {
//  console.log( index );
// } );

