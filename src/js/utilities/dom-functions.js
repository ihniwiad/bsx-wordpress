import Selectors from './../utilities/selectors'
import ClassNames from './../utilities/class-names'


const FUNCTION_ATTR = Selectors.functionAttr


const getConfigFromAttr = ( elem, key ) => {
  if ( ! elem ) {
    return
  }

  let conf
  if ( key ) {
    conf = elem.getAttribute( `data-${key}-conf` )
  }
  else {
    conf = elem.getAttribute( `data-conf` )
  }

  if ( typeof conf !== 'undefined' && conf != null  ) {
    return ( new Function( 'return ' + conf ) )()
  }
  else {
    return {}
  }
}

const getTargetByAriaControls = ( trigger, closest ) => {
  const wrapper = typeof closest !== 'undefined' ? closest : document
  const targetId = trigger.getAttribute( 'aria-controls' )
  return wrapper.querySelector( `#${targetId}` )
}

const getTransitionDuration = ( elem ) => {
  let { transitionDuration } = window.getComputedStyle( elem )
  const floatTransitionDuration = Number.parseFloat( transitionDuration )
  if ( ! floatTransitionDuration ) {
    floatTransitionDuration = 0
  }
  return floatTransitionDuration * 1000
}

const getTriggerFromEvent = ( event ) => {
  return event.target.getAttribute( FUNCTION_ATTR ) != null ? event.target : event.target.closest( '[' + FUNCTION_ATTR + ']' )
}

const addClassNames = ( elem, classNames ) => {
  if ( classNames.indexOf( ' ' ) ) {
    const classNameList = classNames.split( ' ' )
    classNameList.forEach( ( className ) => {
      elem.classList.add( className )
    } )
  }
  else {
    elem.classList.add( classNames )
  }
}

const removeClassNames = ( elem, classNames ) => {
  if ( classNames.indexOf( ' ' ) ) {
    const classNameList = classNames.split( ' ' )
    classNameList.forEach( ( className ) => {
      elem.classList.remove( className )
    } )
  }
  else {
    elem.classList.remove( classNames )
  }
}

const triggerEvent = ( elem, eventName ) => {
    const event = document.createEvent( 'Event' )
    event.initEvent( eventName, true, true)
    elem.dispatchEvent( event )
}


// check if innerElem is positiones inside outerElem
const isPositionedInside = ( outerElem, innerElem ) => {

  // innerElem has to be positiones absolute in outerElem relative to outerElem

  const outerElemStyle = getComputedStyle( outerElem )

   return ( 
    innerElem.offsetLeft + parseInt( outerElemStyle.borderLeftWidth ) >= 0
    && ( innerElem.offsetLeft + parseInt( outerElemStyle.borderLeftWidth ) + innerElem.offsetWidth ) <= outerElem.offsetWidth
    && innerElem.offsetTop + parseInt( outerElemStyle.borderTopWidth ) >= 0
    && ( innerElem.offsetTop + parseInt( outerElemStyle.borderTopWidth ) + innerElem.offsetHeight ) <= outerElem.offsetHeight
  )
}


// add animating class name, remove after transition finished
const setRemoveAnimationClassName = ( elem, animatingClassName ) => {
  const ANIMATING_CLASS_NAME = ( typeof animatingClassName != 'undefined' ) ? animatingClassName : ClassNames.animating
  const TRANSITION_DURATION = getTransitionDuration( elem )
  if ( transitionDuration > 0 ) {
    addClassNames( elem, ANIMATING_CLASS_NAME )
    setTimeout( () => {
      removeClassNames( elem, ANIMATING_CLASS_NAME )
    }, TRANSITION_DURATION )
  }
}


// convert type (e.g. make true from 'true')
const convertType = ( value ) => {
  try {
    value = JSON.parse( value )
    return value
  } catch( e ) {
    // 'value' is not a json string
    return value
  }
}


// aria expanded – gets or sets aria-expanded
const ariaExpanded = ( elem, value ) => {
  if ( typeof value !== 'undefined' ) {
    elem.setAttribute( 'aria-expanded', value )
    return value
  }
  return convertType( elem.getAttribute( 'aria-expanded' ) )
}


const DomFn = {
  addClassNames,
  convertType,
  getConfigFromAttr,
  getTargetByAriaControls,
  getTransitionDuration,
  getTriggerFromEvent,
  isPositionedInside,
  removeClassNames,
  setRemoveAnimationClassName,
  triggerEvent,
}

export default DomFn