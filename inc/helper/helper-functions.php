<?php

class BSXWP_Helper_Fn {

	/**
	 *	Creates string for Meta query using Meta array (sublevel)
	 *
	 *	@param 	array $args 	Arguments containing key and value of Meta array sublevel (must contain keys 'key' and 'val')
	 *
	 *	@return string			Serialized key and value merged together
	 */
	
	public static function metaArrayQueryVal( $args ) {
		if ( ! isset( $args[ 'key' ] ) || ! isset( $args[ 'val' ] ) ) {
			return;
		}
		return serialize( $args[ 'key' ] ) . serialize( $args[ 'val' ] );
	}


	/**
	 *	Sorts multidimensional array by the values of children at index key
	 *
	 *	@param 	array $array 	Array
	 *
	 *	@return string $key		Key
	 */

	public static function sortArrayByChildKey( $array, $key ) {
	    usort( $array, function( $a, $b ) use ( $key ) {
	        return $a[ $key ] <=> $b[ $key ];
	    } );
	    return $array;
	}

}