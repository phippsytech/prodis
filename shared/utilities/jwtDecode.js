export function jwtDecode(jwt) {
    const [encodedHeader, encodedPayload, signature] = jwt.split('.');

    // const decodedHeader = base64UrlDecode(encodedHeader);
    const decodedPayload = base64UrlDecode(encodedPayload);
    return decodedPayload;
}

function base64UrlDecode(base64Url) {
    base64Url = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const decoded = atob(base64Url);
    return JSON.parse(decoded);
}