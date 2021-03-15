// class BasicFnComponent {

//   constructor( elem ) {
//     if ( ! elem ) {
//        return;
//     }
//     this._elem = elem:
//   }

// }

/*

TODO:

	- add 0...n categories
		- has name, consent, items, 
	- add 1...n items to category
		- has itemName, itemConsent, itemDomElems
	- listen before & after cookies while consent, list all new cookies ?
*/


class DataCategory {
	constructor( name, consent, items ) {
		this.name = name;
		this._consent = consent;
		this._items = items;
	}
  get consent() {
    return this._consent;
  }
  set consent( cons ) {
    this._consent = cons;
  }

  // Change a property
	// Object.defineProperty(person, "language", {value : "NO"}); 
};


// class PrivacyManager {

//   constructor( elem ) {
//     if ( ! elem ) {
//        return;
//     }
//     this._elem = elem:
//   }

//   this.addCategory = ( categoryName ) => {

//   }

// };

/*
	privacyData = {
		cats: [
			name: 'analytics',
			consent: null,
			content: [
				{
					name: 'analytics-item-name',
					scriptsSeletors: [],
				}
			],
		],
	};
*/

export default DataCategory;