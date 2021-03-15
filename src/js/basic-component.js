import DomData from './dom/dom-data'

const VERSION = '0.0.1'

class BasicComponent {
  constructor(element) {
    if (!element) {
      return
    }

    this._element = element
    Data.setData(element, this.constructor.DATA_KEY, this)
  }

  dispose() {
    Data.removeData(this._element, this.constructor.DATA_KEY)
    this._element = null
  }

  /** Static */

  static getInstance(element) {
    return Data.getData(element, this.DATA_KEY)
  }

  static get VERSION() {
    return VERSION
  }
}

export default BasicComponent