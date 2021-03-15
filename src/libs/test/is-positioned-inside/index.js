
// inner elem positioned inside outer elem
import DomData from './../../../js/dom/dom-data'
import MakeFnElems from './../../../js/dom/function-elements'
import DomFn from './../../../js/utilities/dom-functions'

const KEY = 'outer'

// init

if ( DomData.getElems( KEY ) ) {
  DomData.getElems( KEY ).forEach( ( outerElem ) => {
    const innerElem = outerElem.querySelector( '[data-bsx-tg="inner"]' )
    outerElem.setAttribute( 'data-test', ( DomFn.isPositionedInside( outerElem, innerElem ) ? 'true' : 'false' ) )
    innerElem.innerHTML = ( DomFn.isPositionedInside( outerElem, innerElem ) ? 'true' : 'false' )
  } )
}