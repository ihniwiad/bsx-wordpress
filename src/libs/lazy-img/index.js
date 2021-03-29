
import $ from "jquery"
import Utils from './../../js/leg-utils/utils/utils'


import * as lazyload from './lazyload'
import * as initLazyload from './init-lazyload'


Utils.$functionElems.filter( '[' + Utils.attributes.functionElement + '~="lazyload"]' ).initLazyload();

