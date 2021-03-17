
// const makeArrayFromElemOrElems = ( elemOrElems ) => {
//   // elemOrElems might be array of elems or single elem
//   return Array.isArray( elemOrElems ) ? elemOrElems : [ elemOrElems ]
// }

// const removeArrayItemByValue = ( arr, searchVal ) => {
//   const isNotValue = ( itemVal ) => {
//     return itemVal != searchVal
//   }
//   return arr.filter( isNotValue )
// }



const hasTouch = () => { 
  return 'ontouchstart' in window || navigator.maxTouchPoints
}


const Fn = {
  hasTouch,
}

export default Fn