export function isDate(value) {
    return !isNaN(new Date(value).getTime());
}

export function isEmail(value) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(value);
}

export function isRequired(value) {
    const re = new RegExp(/.*\S.*/);
    return value != null && re.test(value);
}