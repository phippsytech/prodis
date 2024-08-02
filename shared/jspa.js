
import { get } from "svelte/store";
import { API_URL, jwt } from "./stores.js";
import AuthyClass from "./appsec/Authy.class.js";


export function jspa(endpoint, action, json_data = {}, retry = false) {

    const api_url = get(API_URL);
    const token = get(jwt);

    const versionMetaTag = document.querySelector('meta[name="version"]');
    let version = "Development"

    if (versionMetaTag) {
        version = versionMetaTag.getAttribute('content');
    }

    let headers = {
        'Content-Type': 'application/json; charset=utf-8',
        'X-App-Version': version,
    }

    if (token) headers.Authorization = 'Bearer ' + token;

    return new Promise(function (resolve, reject) {
        fetch(api_url + endpoint, {
            method: 'POST',
            body: JSON.stringify({
                action: action,
                data: json_data
            }),
            headers: headers,
            mode: 'cors'
        })
            .then(async (response) => {
                if (!response.ok) {
                    console.error(`Fetch error: ${response.status} ${response.statusText}`);
                }
                if (response.status === 204) {
                    resolve(null); // Directly resolve the Promise with null or a similar meaningful value indicating no content
                    return; // Prevent further processing
                }

                response.json()
                    .then((data) => {
                        if (response.status >= 200 && response.status < 300) {
                            resolve(data);
                        } else {
                            data.status = response.status;

                            // detect if rejected due to authentication, and attempt to reauthenticate and submit information
                            if (response.status == 401 && retry == false) {
                                let authy = new AuthyClass();

                                authy.checkAuthenticated()
                                    .then(() => {
                                        resolve(jspa(endpoint, action, json_data, true))  // reauthenticate and retry
                                    })
                                    .catch(() => {
                                        reject(data);
                                    })
                            } else {
                                reject(data);
                            }
                        }
                    })
                    .catch((error) => {
                        if (retry == true) {
                            console.log("can't recover from this error.  I give up.")
                            reject(false);
                            return;
                        }
                    })
            })
            .catch((error) => {
                // console.log('something bad happened');
                console.error('Fetch error:', error);
                reject(error);
            });
    });

}
