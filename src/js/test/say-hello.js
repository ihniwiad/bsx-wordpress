export function sayHello( params ) {
 const {
   name,
 } = params;
 // console.log( `Hello from importet function ${ name ? name : 'unknown' }` );
 console.log( `Hello ${name}` );
}