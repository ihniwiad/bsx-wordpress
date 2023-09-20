/*

MARKUP:


<form class="bsx-tgf-form" data-bsx="tgf" data-tgf-conf="{ bsxTarget: 'tgf-tar-1', filterLogic: 'AND', activeItemsCount: 'tgf-cnt-1' }">

  <ul class="list-inline">

    <!-- default -->
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-1" type="checkbox" value="1" data-tgf-tri><label class="bsx-tgf-label" for="tgf-1-1">Tag 1</label></li>
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-2" type="checkbox" value="2" data-tgf-tri><label class="bsx-tgf-label" for="tgf-1-2">Tag 2</label></li>
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-3" type="checkbox" value="3" data-tgf-tri><label class="bsx-tgf-label" for="tgf-1-3">Tag 3</label></li>
    
    <!-- use radio behaviour for selected inputs, add e.g. data-tgf-tri-radio="foo" -->
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-4" type="checkbox" value="4" data-tgf-tri data-tgf-tri-radio="foo"><label class="bsx-tgf-label" for="tgf-1-4">Tag 4</label></li>
    <li class="list-inline-item"><input class="bsx-tgf-trigger" id="tgf-1-5" type="checkbox" value="5" data-tgf-tri data-tgf-tri-radio="foo"><label class="bsx-tgf-label" for="tgf-1-5">Tag 5</label></li>
    
    <!-- reset button -->
    <input class="bsx-tgf-reset btn btn-outline-primary" type="reset" value="Reset">
    <!-- submit button (default hidden since submitted on change) -->
    <input class="bsx-tgf-submit" type="submit" value="Submit">
  </ul>

</form>

<div data-bsx="tgf-cnt-1">
  <span data-tgf-cnt-lab="" style="display: block;"><span data-tgf-cnt-num="">2</span> Results</span>
  <span data-tgf-cnt-lab-sgl="" style="display: none;" aria-hidden="true"><span data-tgf-cnt-num="">2</span> Result</span>
  <span data-tgf-uni-cnt-lab="" style="display: block;"><span data-tgf-uni-cnt-num="">2</span> Unique items</span>
  <span data-tgf-uni-cnt-lab-sgl="" style="display: none;" aria-hidden="true"><span data-tgf-uni-cnt-num="">2</span> Unique items</span>
</div>

<!-- multiple tags within `data-tgf-id="..."` must be space-separated -->
<ul class="bsx-tgf-target list-unstyled" data-bsx="tgf-tar-1">

  <!-- use class names is-grayscale or is-toggle -->
  <!-- use data-tgf-cms-id="..." with item specific content when using duplicates in different groups -->

  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="1" data-tgf-cms-id="4001">Tag 1 content a</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="2" data-tgf-cms-id="4002">Tag 2 content a</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="3" data-tgf-cms-id="4003">Tag 3 content a</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="1 3" data-tgf-cms-id="4005">Tag 1 &amp; 3 content b</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="1" data-tgf-cms-id="4006">Tag 1 content b</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="2" data-tgf-cms-id="4007">Tag 2 content b</li>
  <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="2" data-tgf-cms-id="4008">Tag 2 content c</li>

  <!-- items groups -->
  <li class="bsx-tgf-target-item-group is-toggle" data-tgf-tar-grp>
    <ul>
      <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="4" data-tgf-cms-id="4009">Tag 4 content a</li>
      <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="5" data-tgf-cms-id="4001">Tag 5 content a</li>
    </ul>
  </li>
  <li class="bsx-tgf-target-item-group is-toggle" data-tgf-tar-grp>
    <ul>
      <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="4" data-tgf-cms-id="4001">Tag 4 content b</li>
      <li class="bsx-tgf-target-item is-grayscale" data-tgf-id="5" data-tgf-cms-id="4010">Tag 5 content b</li>
    </ul>
  </li>
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
const DEFAULT_FILTER_LOGIC = 'OR'
const DEFAULT_TRIGGER_EVENT_ON_CHANGE = 'scroll'



// class

class TagFilter {

  constructor( form ) {
    this.form = form
    this.conf = DomFn.getConfigFromAttr( this.form, KEY )
    this.triggers = this.form.querySelectorAll( '[data-tgf-tri]' )
    this.submit = this.form.querySelector( '[type="submit"]' )
    this.reset = this.form.querySelector( '[type="reset"]' )
    this.target = ( typeof this.conf.bsxTarget !== 'undefined' && DomData.getElems( this.conf.bsxTarget ) != null ) ? DomData.getElems( this.conf.bsxTarget )[ 0 ] : undefined // use key for `data-bsx` attr, get first elem of array
    this.targetItemGroups = typeof this.target === 'object' ? this.target.querySelectorAll( '[data-tgf-tar-grp]' ) : []
    this.activeItemsCount = ( typeof this.conf.activeItemsCount !== 'undefined' && DomData.getElems( this.conf.activeItemsCount ) != null ) ? DomData.getElems( this.conf.activeItemsCount ) : [] // use key for `data-bsx` attr, get all elems
    this.targetItems = typeof this.target === 'object' ? this.target.querySelectorAll( '[data-tgf-id]' ) : []
    this.TARGET_ACTIVE_CLASS = ( this.conf != null && typeof this.conf.targetActiveClass ) !== 'undefined' ? this.conf.targetActiveClass : DEFAULT_TARGET_ACTIVE_CLASS
    this.TARGET_INACTIVE_CLASS = ( this.conf != null && typeof this.conf.targetInactiveClass ) !== 'undefined' ? this.conf.targetInactiveClass : DEFAULT_TARGET_INACTIVE_CLASS
    // this.TRIGGER_ACTIVE_CLASS = ( this.conf != null && typeof this.conf.triggerActiveClass ) !== 'undefined' ? this.conf.triggerActiveClass : DEFAULT_TRIGGER_ACTIVE_CLASS
    // this.TRIGGER_INACTIVE_CLASS = ( this.conf != null && typeof this.conf.triggerInactiveClass ) !== 'undefined' ? this.conf.triggerInactiveClass : DEFAULT_TRIGGER_INACTIVE_CLASS
    this.SUBMIT_ON_CHANGE = ( this.conf != null && typeof this.conf.submitOnChange ) !== 'undefined' ? this.conf.submitOnChange : DEFAULT_SUBMIT_ON_CHANGE
    this.SHOW_ALL_IF_NOTHING_SELECTED = ( this.conf != null && typeof this.conf.showAllIfNothingSelected ) !== 'undefined' ? this.conf.showAllIfNothingSelected : DEFAULT_SHOW_ALL_IF_NOTHING_SELECTED
    this.FILTER_LOGIC = ( this.conf != null && typeof this.conf.filterLogic ) !== 'undefined' ? this.conf.filterLogic : DEFAULT_FILTER_LOGIC
    this.TRIGGER_EVENT_ON_CHANGE = ( this.conf != null && typeof this.conf.eventOnChange ) !== 'undefined' ? this.conf.eventOnChange : DEFAULT_TRIGGER_EVENT_ON_CHANGE
  }

  _activate( targetItem ) {
    if ( this.TARGET_ACTIVE_CLASS ) {
      targetItem.classList.add( this.TARGET_ACTIVE_CLASS )
    }
    if ( this.TARGET_INACTIVE_CLASS ) {
      targetItem.classList.remove( this.TARGET_INACTIVE_CLASS )
    }
    targetItem.setAttribute( 'aria-live', 'polite' )
  }

  _deactivate( targetItem ) {
    if ( this.TARGET_ACTIVE_CLASS ) {
      targetItem.classList.remove( this.TARGET_ACTIVE_CLASS )
    }
    if ( this.TARGET_INACTIVE_CLASS ) {
      targetItem.classList.add( this.TARGET_INACTIVE_CLASS )
    }
    targetItem.setAttribute( 'aria-live', 'off' )
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

  _updateActiveItemsCount( activeItemsCount, activeItemsCountVal, activeUniqueItemsCountVal ) {
    // update multiple count elems
    activeItemsCount.forEach( ( countItem ) => {

      // update count number
      countItem.querySelectorAll( '[data-tgf-cnt-num]' ).forEach( ( countItemNumber ) => {
        countItemNumber.innerHTML = activeItemsCountVal
      } )
      // manage singular/plural labels
      if ( activeItemsCountVal == 1 ) {
        DomFn.hide( countItem.querySelectorAll( '[data-tgf-cnt-lab]' ) )
        DomFn.show( countItem.querySelectorAll( '[data-tgf-cnt-lab-sgl]' ) )
      }
      else {
        DomFn.hide( countItem.querySelectorAll( '[data-tgf-cnt-lab-sgl]' ) )
        DomFn.show( countItem.querySelectorAll( '[data-tgf-cnt-lab]' ) )
      }

      if ( activeUniqueItemsCountVal !== null ) {
        // update unique count number
        countItem.querySelectorAll( '[data-tgf-uni-cnt-num]' ).forEach( ( countItemNumber ) => {
          countItemNumber.innerHTML = activeUniqueItemsCountVal
        } )
        // manage singular/plural labels
        if ( activeUniqueItemsCountVal == 1 ) {
          DomFn.hide( countItem.querySelectorAll( '[data-tgf-uni-cnt-lab]' ) )
          DomFn.show( countItem.querySelectorAll( '[data-tgf-uni-cnt-lab-sgl]' ) )
        }
        else {
          DomFn.hide( countItem.querySelectorAll( '[data-tgf-uni-cnt-lab-sgl]' ) )
          DomFn.show( countItem.querySelectorAll( '[data-tgf-uni-cnt-lab]' ) )
        }
      }

    } )
  }

  _updateTargetItemGroups( triggersConfig ) {
    this.targetItemGroups.forEach( ( targetItemGroup ) => {
      const groupActiveItemsVal = targetItemGroup.querySelectorAll( '[data-tgf-id].' + this.TARGET_ACTIVE_CLASS ).length
      if ( groupActiveItemsVal > 0 ) {
        // has active items
        targetItemGroup.classList.add( this.TARGET_ACTIVE_CLASS );
        targetItemGroup.classList.remove( this.TARGET_INACTIVE_CLASS );
      }
      else {
        // doesn’t have active items
        targetItemGroup.classList.add( this.TARGET_INACTIVE_CLASS );
        targetItemGroup.classList.remove( this.TARGET_ACTIVE_CLASS );
      }
      // targetItemGroup.setAttribute( 'data-tgf-itm-cnt', groupActiveItemsVal )
      const groupActiveItemsCount = targetItemGroup.querySelectorAll( '[data-tgf-grp-cnt]' )
      if ( groupActiveItemsCount.length > 0 ) {
        // target group will not have item diplicates, so there is only one items count
        this._updateActiveItemsCount( groupActiveItemsCount, groupActiveItemsVal, null )
      }
    } )
  }

  _updateTargetItems( targetItems, triggersConfig ) {

    const isNothingSelected = this._isNothingSelected()
    let activeItemsCountVal = 0
    let activeItemsIds = [] // collect ids (e.g. Post IDs) to avoid duplicates in counting

    if ( isNothingSelected ) {
      if ( this.SHOW_ALL_IF_NOTHING_SELECTED ) {
        // show all while nothing is filtered
        targetItems.forEach( ( targetItem ) => {
          this._activate( targetItem )
          activeItemsCountVal++
          const itemCmsId = targetItem.getAttribute( 'data-tgf-cms-id' )
          if ( itemCmsId && ! activeItemsIds.includes( itemCmsId ) ) {
            // add if not already contained
            activeItemsIds.push( itemCmsId )
          }
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

        // separate FILTER_LOGIC == 'OR' (one or more must match) / 'AND' (all must match)

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


          // console.log( 'test item: activeFilterKeys: ' + JSON.stringify( activeFilterKeys, null, 2 ) )
          // console.log( 'test item: itemKeyList: ' + JSON.stringify( itemKeyList, null, 2 ) )

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
          activeItemsCountVal++
          const itemCmsId = targetItem.getAttribute( 'data-tgf-cms-id' )
          if ( itemCmsId && ! activeItemsIds.includes( itemCmsId ) ) {
            // add if not already contained
            activeItemsIds.push( itemCmsId )
          }
        }
        else {
          this._deactivate( targetItem )
        }

      } )
    }

    // update count(s) – all active items (may habe duplicates) and unique active items (no duplicates)
    this._updateActiveItemsCount( this.activeItemsCount, activeItemsCountVal, activeItemsIds.length )

    this._updateTargetItemGroups( triggersConfig )

    // check trigger event on change
    if ( this.TRIGGER_EVENT_ON_CHANGE ) {
      window.dispatchEvent( new Event( this.TRIGGER_EVENT_ON_CHANGE ) );
    }

  }

  _uncheckOtherRadioInputs( currentTrigger, radioName ) {
    this.triggers.forEach( ( trigger ) => {
      if ( 
        trigger.getAttribute( 'data-tgf-tri-radio' ) === radioName 
        && trigger !== currentTrigger 
        && trigger.checked 
      ) {
        // uncheck active input of equal radio name
        trigger.checked = false
      }
    } )
  }

  init() {

    if ( ! this.target ) {
      console.log( 'Target `' + this.conf.target + '` missing on tag filter' )
    }


    // set submit event listener
    this.form.addEventListener( 'submit', ( event ) => {
      event.preventDefault()

      const triggersConfig = this._getConfig( this.triggers )

      // console.log( 'triggersConfig: ' + JSON.stringify( triggersConfig, null, 2 ) )

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

          // check if multiple triggers have radio behaviour, e.g: data-tgf-tri-radio="foo"
          if ( trigger.checked ) {
            // uncheck others of same radio group
            const radioName = trigger.getAttribute( 'data-tgf-tri-radio' )
            if ( radioName !== null ) {
              this._uncheckOtherRadioInputs( trigger, radioName )
            }
          }

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
    else {
      // check if multiple triggers have radio behaviour, e.g: data-tgf-tri-radio="foo"

      // this.triggers.forEach( ( trigger ) => {
      //   const radioName = trigger.getAttribute( 'data-tgf-tri-radio' )
      //   if ( trigger.getAttribute( 'data-tgf-tri-radio' ) !== null ) {
      //     trigger.addEventListener( 'change', ( event ) => {
      //       if ( event.target.checked ) {
      //         // uncheck others of same radio group
      //         if ( radioName !== null ) {
      //           this._uncheckOtherRadioInputs( event.target, radioName )
      //         }
      //       }
      //     }, false )
      //   }
      // } )
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

