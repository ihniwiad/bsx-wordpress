/*

MARKUP:


<form class="bsx-tgf-form" data-bsx="tgf" data-tgf-conf="{ bsxTarget: 'tgf-tar-1', filterLogic: 'AND' }">

  <ul class="list-inline">
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-1" type="checkbox" value="1" data-tgf-tri><label class="bsx-tgf-label" for="tgf-1-1">Tag 1</label></li>
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-2" type="checkbox" value="2" data-tgf-tri><label class="bsx-tgf-label" for="tgf-1-2">Tag 2</label></li>
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-3" type="checkbox" value="3" data-tgf-tri><label class="bsx-tgf-label" for="tgf-1-3">Tag 3</label></li>
    <li class="list-inline-item"><input class="bsx-tgf-reset btn btn-outline-primary" type="reset" value="Reset"></li>
    <li class="list-inline-item"><input class="bsx-tgf-submit" type="submit" value="Submit"></li>
  </ul>

</form>

<!-- multiple tags within `data-tgf-id="..."` must be space-separated -->
<ul class="bsx-tgf-target list-unstyled" data-bsx="tgf-tar-1">
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="1">Tag 1 content a</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="2">Tag 2 content a</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="3">Tag 3 content a</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="1 3">Tag 1 &amp; 3 content b</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="1">Tag 1 content b</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="2">Tag 2 content b</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="2">Tag 2 content c</li>
</ul>


*/


import DomData from './../../js/dom/dom-data'
import MakeFnElems from './../../js/dom/function-elements'
import DomFn from './../../js/utilities/dom-functions'


// params

const KEY = 'tgf'
const DEFAULT_TARGET_ACTIVE_CLASS = 'active'
const DEFAULT_TARGET_INACTIVE_CLASS = 'inactive'
// const DEFAULT_TRIGGER_ACTIVE_CLASS = ''
// const DEFAULT_TRIGGER_INACTIVE_CLASS = ''
const DEFAULT_SUBMIT_ON_CHANGE = true
const DEFAULT_SHOW_ALL_IF_NOTHING_SELECTED = true
const DEFALT_FILTER_LOGIC = 'OR'



// class

class TagFilter {

  constructor( form ) {
    this.form = form
    this.conf = DomFn.getConfigFromAttr( this.form, KEY )
    this.triggers = this.form.querySelectorAll( '[data-tgf-tri]' )
    this.submit = this.form.querySelector( '[type="submit"]' )
    this.reset = this.form.querySelector( '[type="reset"]' )
    this.target = ( typeof this.conf.bsxTarget !== 'undefined' && DomData.getElems( this.conf.bsxTarget ) != null ) ? DomData.getElems( this.conf.bsxTarget )[ 0 ] : undefined // use key for `data-bsx` attr, get first elem of array
    this.targetItems = typeof this.target === 'object' ? this.target.querySelectorAll( '[data-tgf-id]' ) : []
    this.TARGET_ACTIVE_CLASS = ( this.conf != null && typeof this.conf.targetActiveClass ) !== 'undefined' ? this.conf.targetActiveClass : DEFAULT_TARGET_ACTIVE_CLASS
    this.TARGET_INACTIVE_CLASS = ( this.conf != null && typeof this.conf.targetInactiveClass ) !== 'undefined' ? this.conf.targetInactiveClass : DEFAULT_TARGET_INACTIVE_CLASS
    // this.TRIGGER_ACTIVE_CLASS = ( this.conf != null && typeof this.conf.triggerActiveClass ) !== 'undefined' ? this.conf.triggerActiveClass : DEFAULT_TRIGGER_ACTIVE_CLASS
    // this.TRIGGER_INACTIVE_CLASS = ( this.conf != null && typeof this.conf.triggerInactiveClass ) !== 'undefined' ? this.conf.triggerInactiveClass : DEFAULT_TRIGGER_INACTIVE_CLASS
    this.SUBMIT_ON_CHANGE = ( this.conf != null && typeof this.conf.submitOnChange ) !== 'undefined' ? this.conf.submitOnChange : DEFAULT_SUBMIT_ON_CHANGE
    this.SHOW_ALL_IF_NOTHING_SELECTED = ( this.conf != null && typeof this.conf.showAllIfNothingSelected ) !== 'undefined' ? this.conf.showAllIfNothingSelected : DEFAULT_SHOW_ALL_IF_NOTHING_SELECTED
    this.FILTER_LOGIC = ( this.conf != null && typeof this.conf.filterLogic ) !== 'undefined' ? this.conf.filterLogic : DEFALT_FILTER_LOGIC
  }

  _activate( targetItem ) {
    if ( this.TARGET_ACTIVE_CLASS ) {
      targetItem.classList.add( this.TARGET_ACTIVE_CLASS )
    }
    if ( this.TARGET_INACTIVE_CLASS ) {
      targetItem.classList.remove( this.TARGET_INACTIVE_CLASS )
    }
    // trigger window scroll to trigger lazyload on appearing filter elems
    window.dispatchEvent( new CustomEvent( 'scroll' ) )
  }

  _deactivate( targetItem ) {
    if ( this.TARGET_ACTIVE_CLASS ) {
      targetItem.classList.remove( this.TARGET_ACTIVE_CLASS )
    }
    if ( this.TARGET_INACTIVE_CLASS ) {
      targetItem.classList.add( this.TARGET_INACTIVE_CLASS )
    }
  }

  _isNothingSelected() {
    let selectionFound = false
    for ( let trigger of this.triggers ) {
      if ( trigger.checked ) {
        selectionFound = true
        break
      }
    }
    return ! selectionFound
  }

  _getConfig( triggers ) {
    let triggersConfig = []

    triggers.forEach( ( trigger, index ) => {
      if ( trigger.hasAttribute( 'value' ) ) {
        triggersConfig.push( { 
          'id': trigger.getAttribute( 'value' ), 
          'status': trigger.checked 
        } )
      }
      else {
        console.log( 'Attribute `value` missing on tag filter trigger' )
      }
    } )

    return triggersConfig
  }

  _getActiveFilterKeys( triggersConfig ) {
    const activeKeys = []
    triggersConfig.forEach( ( triggersConfigItem ) => {
      if ( triggersConfigItem.status == true ) {
        activeKeys.push( triggersConfigItem.id )
      }
    } )
    return activeKeys
  }

  _getConfigIndex( triggersConfig, itemKey ) {
    const configIndex = triggersConfig.findIndex( item => {
      if ( item.id === itemKey ) {
        return true;
      }
    } );
    return configIndex
  }

  _updateTargetItems( targetItems, triggersConfig ) {

    const isNothingSelected = this._isNothingSelected()

    if ( isNothingSelected ) {
      if ( this.SHOW_ALL_IF_NOTHING_SELECTED ) {
        // show all while nothing is filtered
        targetItems.forEach( ( targetItem ) => {
          this._activate( targetItem )
        } )
      }
    }
    else {

      // console.log( 'triggersConfig: ' + JSON.stringify( triggersConfig, null, 2 ) )

      // show selected
      targetItems.forEach( ( targetItem ) => {
        // allow multiple tagging (e.g. data-tgf-id="foo bar"), get list
        const itemKey = targetItem.getAttribute( 'data-tgf-id' )
        let itemKeyList = []
        if ( itemKey.indexOf( ' ' ) > -1 ) {
          // has multiple tags
          itemKeyList = itemKey.split( ' ' )
        }
        else {
          itemKeyList.push( itemKey )
        }

        // check config for current item key(s)
        let itemIsActive = false

        // TODO: separate FILTER_LOGIC == 'OR' (one or more must match) / 'AND' (all must match)

        if ( this.FILTER_LOGIC.toLowerCase() == 'or' ) {
          // filter item must match one or more keys

          if ( itemKeyList.length > 0 ) {
            // has murtiple tags, check for 1st active
            for ( let itemKey of itemKeyList ) {
              const configIndex = this._getConfigIndex( triggersConfig, itemKey )

              // console.log( 'configIndex: ' + JSON.stringify( configIndex, null, 2 ) )
              // console.log( 'itemKey: ' + itemKey )
              
              // configIndex might be -1 if post has category not containend in triggers config
              if ( typeof triggersConfig[ configIndex ] !== 'undefined' && triggersConfig[ configIndex ].status === true ) {
                // first match found
                itemIsActive = true
                break
              }
            }
          }
          else {
            // has one tag, check if active
            const configIndex = this._getConfigIndex( triggersConfig, itemKey )
            if ( triggersConfig[ configIndex ].status === true ) {
              itemIsActive = true
            }
          }
        }
        else {
          // filter item must match all keys

          itemIsActive = true

          // iterate all selectet filter items, remember keys
          // then iterate target items & check all keys

          const activeFilterKeys = this._getActiveFilterKeys( triggersConfig )
          // console.log( 'activeFilterKeys: ' + JSON.stringify( activeFilterKeys, null, 2 ) )


          // console.log( 'test item: activeFilterKeys: ' + JSON.stringify( activeFilterKeys, null, null ) )
          // console.log( 'test item: itemKeyList: ' + JSON.stringify( itemKeyList, null, null ) )

          if ( itemKeyList.length > 0 ) {
            for ( let activeFilterKey of activeFilterKeys ) {

              // console.log( '-----> activeFilterKey: ' + activeFilterKey )

              if ( itemKeyList.indexOf( activeFilterKey ) == -1 ) {
                // 
                itemIsActive = false
              }
            }
          }
          else {
            itemIsActive = false
          }

        }


        if ( itemIsActive ) {
          this._activate( targetItem )
        }
        else {
          this._deactivate( targetItem )
        }

      } )
    }


  }

  init() {

    if ( ! this.target ) {
      console.log( 'Target `' + this.conf.target + '` missing on tag filter' )
    }


    // set submit event listener
    this.form.addEventListener( 'submit', ( event ) => {
      event.preventDefault()

      const triggersConfig = this._getConfig( this.triggers )

      if ( this.targetItems ) {
        this._updateTargetItems( this.targetItems, triggersConfig )
      }
      
      return false;
    }, false )


    // set change listeners to inputs to submit automatically (init after submit event listener has been added)
    if ( this.SUBMIT_ON_CHANGE ) {
      // each trigger change triggering submit
      this.triggers.forEach( ( trigger ) => {
        trigger.addEventListener( 'change', () => {
          // this.form.submit()
          this.submit.click()
        }, false )
      } )

      // reset triggering submit
      this.reset.addEventListener( 'click', () => {
        // wait for inputs to be resetted before tiggering submit
        setTimeout( () => {
          this.submit.click()
        } )
      }, false )
    }


    // initial update

    const triggersConfig = this._getConfig( this.triggers )
    if ( this.targetItems ) {
      this._updateTargetItems( this.targetItems, triggersConfig )
    }

  }
}


// init

const elems = DomData.getElems( KEY )

if ( elems ) {
  elems.forEach( ( form ) => {
    const currentForm = new TagFilter( form )
    currentForm.init()
  } )
}

