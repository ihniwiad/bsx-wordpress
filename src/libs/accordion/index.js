import DomData from './../../js/dom/dom-data'
import MakeFnElems from './../../js/dom/function-elements'
import DomFn from './../../js/utilities/dom-functions'


// params

const KEY = 'acc'
const DEFAULT_TARGET_OPENED_CLASS = 'open'
const DEFAULT_TRIGGER_OPENED_CLASS = 'open'
const DEFAULT_ALLOW_MULTI_OPEN = true
const TRANSITION_TOLERANCE_DELAY = 10 // required to enable clean css transition, e.g. add class containing transition, then change style triggering transition


// class

class Accordion {
  constructor( trigger ) {
    this.trigger = trigger
    this.acc = this.trigger.closest( '[data-acc]' )
    this.conf = DomFn.getConfigFromAttr( this.acc, KEY )
    this.ALLOW_MULTI_OPEN = ( this.conf != null && typeof this.conf.multipleOpen ) !== 'undefined' ? this.conf.multipleOpen : DEFAULT_ALLOW_MULTI_OPEN
    this.TARGET_OPENED_CLASS = ( this.conf != null && typeof this.conf.targetOpenedClass ) !== 'undefined' ? this.conf.targetOpenedClass : DEFAULT_TARGET_OPENED_CLASS
    this.TRIGGER_OPENED_CLASS = ( this.conf != null && typeof this.conf.triggerOpenedClass ) !== 'undefined' ? this.conf.triggerOpenedClass : DEFAULT_TRIGGER_OPENED_CLASS
  }
  _open( trigger ) {
    trigger.setAttribute( 'aria-expanded', 'true' )
    // DomFn.ariaExpanded( trigger, true )
    trigger.classList.add( this.TRIGGER_OPENED_CLASS )
    const target = DomFn.getTargetByAriaControls( trigger, trigger.closest( '[data-acc-itm]' ) )
    const targetInner = target.querySelector( '[acc-cnt-inr]' )
    const targetInnerHeight = targetInner.offsetHeight
    target.classList.add( this.TARGET_OPENED_CLASS )
    target.style.height = targetInnerHeight + 'px'
    // remove height after transition ended
    const transitionDuration = DomFn.getTransitionDuration( target ) + TRANSITION_TOLERANCE_DELAY
    setTimeout( () => {
      target.style.height = ''
      DomFn.triggerEvent( window, 'scroll' )
      // TODO: trigger update event to all data-bsx elems within target (e.g. appear)
    }, transitionDuration )

    if ( ! this.ALLOW_MULTI_OPEN ) {
      // disable clicked since must stay open
      trigger.setAttribute( 'aria-disabled', 'true' )
      if ( typeof this.acc.bsxData !== 'undefined' && typeof this.acc.bsxData.recentTrigger !== 'undefined' ) {
        // remove disabled from recent
        this.acc.bsxData.recentTrigger.removeAttribute( 'aria-disabled' )
        // close open (not clicked) item
        this._close( this.acc.bsxData.recentTrigger )
      }
      // remember clicked item
      this.acc.bsxData = { recentTrigger: trigger }
    }
  }
  // use trigger param (not this.trigger) since function will close multiple accordion items with one click event
  _close( trigger ) {
    if ( ! trigger.hasAttribute( 'aria-disabled' ) || trigger.getAttribute( 'aria-disabled' ) === 'false' ) {
      trigger.setAttribute( 'aria-expanded', 'false' )
      trigger.classList.remove( this.TRIGGER_OPENED_CLASS )
      const target = DomFn.getTargetByAriaControls( trigger, trigger.closest( '[data-acc-itm]' ) )
      const targetInner = target.querySelector( '[acc-cnt-inr]' )
      const targetInnerHeight = targetInner.offsetHeight
      const transitionDuration = DomFn.getTransitionDuration( target ) + TRANSITION_TOLERANCE_DELAY
      // set height before remove opened class
      target.style.height = targetInnerHeight + 'px'
      setTimeout( () => {
        // remove opened class
        target.classList.remove( this.TARGET_OPENED_CLASS )
        setTimeout( () => {
          // remove height to init transition
          target.style.height = ''
          setTimeout( () => {
            DomFn.triggerEvent( window, 'scroll' )
          }, transitionDuration )
        }, TRANSITION_TOLERANCE_DELAY )
      }, TRANSITION_TOLERANCE_DELAY )
    }
  }
  init() {
    // console.log( 'init: ' + this.trigger.bsxData.id )
    if ( ! this.ALLOW_MULTI_OPEN && this.trigger.getAttribute( 'aria-expanded' ) === 'true' ) {
      // remember initial open status item
      this.acc.bsxData = { recentTrigger: this.trigger }
      // set disabled (might not be initially set but opened)
      this.trigger.setAttribute( 'aria-disabled', 'true' )
    }

    // set event listener
    this.trigger.addEventListener( 'click', ( event ) => {
      event.preventDefault()
      // decide wether open or close
      if ( this.trigger.getAttribute( 'aria-expanded' ) === 'false' ) {
        this._open( this.trigger )
      } else {
        this._close( this.trigger )
      }
    }, false )
  }
}


// init

if ( DomData.getElems( KEY ) ) {
  DomData.getElems( KEY ).forEach( ( trigger ) => {
    const currentTrigger = new Accordion( trigger )
    currentTrigger.init()
  } )
}

