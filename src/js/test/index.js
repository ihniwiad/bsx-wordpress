import $ from "jquery"
// import { sayHello } from './say-hello.js'

import ES5_UTILS from './../leg-utils/utils/utils.js'

console.log( 'TEST' )
console.log( 'ES5_UTILS.classes.animatingIn: ' + ES5_UTILS.classes.animatingIn )
ES5_UTILS.$functionElems.addClass( ES5_UTILS.classes.animatingIn )

// TEST inner elem positioned inside outer elem
// import DomData from './../dom/dom-data'
// import MakeFnElems from './../dom/function-elements'
// import DomFn from './../utilities/dom-functions'

// const KEY = 'outer'

// // init

// if ( DomData.getElems( KEY ) ) {
//   DomData.getElems( KEY ).forEach( ( outerElem ) => {
//     const innerElem = outerElem.querySelector( '[data-bsx-tg="inner"]' )
//     outerElem.setAttribute( 'data-test', ( DomFn.isPositionedInside( outerElem, innerElem ) ? 'true' : 'false' ) )
//   } )
// }
// /TEST inner elem positioned inside outer elem


// importet function
// sayHello( {
//   name: 'MyName :)',
// } );


// jquery test
$( 'h1' ).html( 'jQuery manipulated' );

