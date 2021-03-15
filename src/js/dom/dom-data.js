const elemMap = ( () => {
  const elemStorage = {}
  let id = 0
  return {
    set( elem, key ) {
      if ( typeof elem.bsxData === 'undefined' ) {
        // add elem if not already exists
        // add data
        elem.bsxData = {
          key,
          id
        }
        if ( typeof elemStorage[ key ] === 'undefined' ) {
          // add list if not already exists
          elemStorage[ key ] = []
        }
        // add to list
        elemStorage[ key ].push( elem )
        id++
      }
    },
    get( key ) {
      if ( ! key || typeof elemStorage[ key ] === 'undefined' ) {
        return null
      }
      return elemStorage[ key ]
    },
    remove( elem, key ) {
      if ( typeof elem.bsxData === 'undefined' ) {
        return
      }
      if ( elem.bsxData.key === key ) {
        // delete elem data
        delete elem.bsxData
        // remove from list
        const currentElems = elemStorage[ key ]
        for ( let i = 0; i < currentElems.length; i++ ) { 
          if ( currentElems[ i ] === elem ) { 
              currentElems.splice( i, 1 );
          }
        }
        elemStorage[ key ] = currentElems
      }
    }
  }
} )()

const DomData = {
  addElem( instance, key ) {
    elemMap.set( instance, key )
  },
  getElems( key ) {
    return elemMap.get( key )
  },
  removeElem( instance, key ) {
    elemMap.remove( instance, key )
  }
}

export default DomData

