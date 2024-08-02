export function convertFieldsToBoolean(data, fieldsToConvert) {
    // Optionally enable detailed logging during development or debugging
    // console.log(data, typeof data);

    if (Array.isArray(data)) {
        return data.map(item => processItemImmutably(item, fieldsToConvert));
    } else if (isNonNullObject(data)) {
        return processItemImmutably(data, fieldsToConvert);
    } else {
        // Handling unexpected data types gracefully
        console.error('Invalid data type for conversion:', typeof data);
        return data; // Consider how to handle this case, perhaps throwing an error
    }
}

function isNonNullObject(data) {
    return typeof data === 'object' && data !== null;
}

function processItemImmutably(item, fieldsToConvert) {
    const newItem = { ...item }; // Create a shallow copy to avoid mutating the original item
    fieldsToConvert.forEach(field => {
        if (newItem.hasOwnProperty(field)) {
            newItem[field] = newItem[field] === "1" || newItem[field] === 1; // Convert to boolean
        }
    });
    return newItem;
}