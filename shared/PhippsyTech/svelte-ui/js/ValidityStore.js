import { writable } from 'svelte/store';
 
// invoke writable like this.  We are setting the initial default items of the store
const ValidityStore = writable([]);
 
// now we just need to export the store
export default ValidityStore;