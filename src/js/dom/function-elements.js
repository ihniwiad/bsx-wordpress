import DomData from './dom-data'
import Selectors from './../utilities/selectors'


const FUNCTION_ATTR = Selectors.functionAttr

const functionElems = document.querySelectorAll( '[' + FUNCTION_ATTR + ']' )

// add to DomData
functionElems.forEach( elem => {
  const key = elem.getAttribute( FUNCTION_ATTR )
  DomData.addElem( elem, key )
} )

export default functionElems

