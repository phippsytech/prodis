 export function makeUniqueId() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
}

export function updateValidity(ValidityStore, id, state){
    ValidityStore.update((currentData)=>{
        currentData[id]=state;
        return currentData; 
    });
    return state;
}

export function isEmpty(string) {
    if(string === '' || string == null) return true;
    return false;
}