
// import { push } from '@app/../node_modules/svelte-spa-router';
import { jwtDecode } from '@shared/utilities/jwtDecode';

// import { get } from "@app/../node_modules/svelte/store";
import { jwt } from "@shared/stores.js";

import { get } from "svelte/store";
import configStore from '@app/configStore';


export default class Authy {



    constructor() {

        const config = get(configStore);
        let endpoint = config.API_URL
        this.auth_endpoint = endpoint + "/Auth"
        jwt.subscribe((value) => { this.JWT = value });
    }

    async checkAuthenticated(retry = false) {
        try {
            await this.hasCredentials();
            await this.checkJWT();
        } catch {

            try {
                await this.authenticate();
                if (!retry) {
                    await this.checkAuthenticated(true);
                } else {
                    throw new Error("Authentication failed");
                }
            } catch (error) {
                throw new Error("Authentication failed");
            }
        }
    }

    async hasCredentials() {
        try {
            await this.getPublicKeyFromDB();
            await this.getPrivateKeyFromDB();
            return true;
        } catch {
            await this.generateKeys();
            return true;
        }
    }

    async deleteKeyDatabase() {
        return new Promise((resolve, reject) => {
            const deleteDBRequest = indexedDB.deleteDatabase("KeyDatabase");

            deleteDBRequest.onerror = (event) => {
                console.error("Error deleting database:", event.target.error);
                reject(event.target.error);
            };

            deleteDBRequest.onsuccess = (event) => {
                console.log("KeyDatabase deleted successfully.");
                resolve(true);
            };

            deleteDBRequest.onblocked = (event) => {
                console.warn(
                    "Delete database operation is blocked. Please close all other open connections to the database.",
                );
                reject(new Error("Delete database operation is blocked."));
            };
        });
    }

    async closeAndDeleteKeyDatabase() {
        // Open the database to close any connections
        const openDBRequest = indexedDB.open("KeyDatabase");

        openDBRequest.onsuccess = (event) => {
            const db = event.target.result;

            // Close the database connection
            db.close();

            // Now delete the database
            const deleteDBRequest = indexedDB.deleteDatabase("KeyDatabase");

            deleteDBRequest.onerror = (event) => {
                console.error("Error deleting database:", event.target.error);
            };

            deleteDBRequest.onsuccess = (event) => {
                console.log("KeyDatabase deleted successfully.");
            };

            deleteDBRequest.onblocked = (event) => {
                console.warn(
                    "Delete database operation is blocked. Please close all other open connections to the database.",
                );
            };
        };

        openDBRequest.onerror = (event) => {
            console.error("Error opening database:", event.target.error);
        };
    }

    async openKeyDatabase() {
        return new Promise((resolve, reject) => {
            const openDBRequest = indexedDB.open("KeyDatabase", 1);

            openDBRequest.onerror = (event) => reject(event.target.error);

            openDBRequest.onsuccess = (event) => {
                const db = event.target.result;
                resolve(db);
            };

            openDBRequest.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains("keys")) {
                    db.createObjectStore("keys", { keyPath: "id" });
                }
            };
        });
    }

    async exportPublicCryptoKey(publicKey) {
        const publicKeyPem = await window.crypto.subtle.exportKey(
            "spki",
            publicKey,
        );

        // Convert the exported key to PEM format
        const b64 = btoa(
            String.fromCharCode.apply(null, new Uint8Array(publicKeyPem)),
        );
        const pem = `-----BEGIN PUBLIC KEY-----\n${b64.match(/.{1,64}/g).join("\n")}\n-----END PUBLIC KEY-----`;
        return pem;
    }

    async generateKeys() {
        try {
            // Generate a new key pair to sign and verify data
            const keyPair = await window.crypto.subtle.generateKey(
                {
                    name: "RSASSA-PKCS1-v1_5",
                    modulusLength: 2048,
                    publicExponent: new Uint8Array([1, 0, 1]),
                    hash: { name: "SHA-256" },
                },
                false,
                ["sign", "verify"],
            );

            const publicKeyPem = await this.exportPublicCryptoKey(keyPair.publicKey);

            // Store the keys in IndexedDB
            const db = await this.openKeyDatabase();
            const transaction = db.transaction("keys", "readwrite");
            transaction
                .objectStore("keys")
                .add({ id: "private", key: keyPair.privateKey });
            transaction
                .objectStore("keys")
                .add({ id: "public", key: publicKeyPem });
        } catch (error) {
            throw new Error(
                "Error Generating Keys: " + error.message || "Unknown",
            );
        }
    }

    async deleteKeys() {
        // Delete the keys from IndexedDB
        const db = await this.openKeyDatabase();
        const transaction = db.transaction("keys", "readwrite");
        transaction.objectStore("keys").delete("private");
        transaction.objectStore("keys").delete("public");
    }

    async getPublicKeyFromDB() {
        const db = await this.openKeyDatabase();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction("keys", "readonly");
            const objectStore = transaction.objectStore("keys");
            const getRequest = objectStore.get("public");

            getRequest.onerror = (event) => reject(event.target.error);
            getRequest.onsuccess = (event) => {
                if (event.target.result) {
                    resolve(event.target.result.key);
                } else {
                    reject(new Error("No public key found"));
                }
            };
        });
    }

    async getPrivateKeyFromDB() {
        const db = await this.openKeyDatabase();
        return new Promise((resolve, reject) => {
            const transaction = db.transaction("keys", "readonly");
            const objectStore = transaction.objectStore("keys");
            const getRequest = objectStore.get("private");

            getRequest.onerror = (event) => reject(event.target.error);
            getRequest.onsuccess = (event) => {
                if (event.target.result) {
                    resolve(event.target.result.key);
                } else {
                    reject(new Error("No private key found"));
                }
            };
        });
    }

    async signData(data) {
        const privateKey = await this.getPrivateKeyFromDB();
        const signature = await window.crypto.subtle.sign(
            {
                name: "RSASSA-PKCS1-v1_5",
                saltLength: 32,
            },
            privateKey,
            data,
        );
        return signature;
    }

    async signString(dataString) {
        const data = new TextEncoder().encode(dataString);
        const signature = await this.signData(data);
        const base64Signature = this.arrayBufferToBase64(signature);
        return base64Signature;
    }

    async authenticate() {
        try {
            const challenge = await this.requestChallenge();
            const signature = await this.signString(challenge);
            const response = await this.doAPICall("authenticate", {
                challenge: challenge,
                signature: signature,
            });
            jwt.set(response.result.jwt);
            return this.JWT;
        } catch (error) {
            throw error;
        }
    }

    async register(registrationToken) {
        try {
            const public_key = await this.getPublicKeyFromDB();
            const response = this.doAPICall(
                "register",
                { public_key: public_key },
                registrationToken,
            );
            return true;
        } catch (error) {
            throw error;
        }
    }

    async requestChallenge() {
        try {
            const public_key = await this.getPublicKeyFromDB();
            const response = await this.doAPICall("requestChallenge", {
                public_key: public_key,
            });
            return response.result.challenge;
        } catch (error) {
            throw error;
        }
    }

    async requestRegistration(email) {
        try {
            const public_key = await this.getPublicKeyFromDB();
            const response = this.doAPICall("requestRegistration", {
                email: email,
                domain: window.location.hostname,
                public_key: public_key,
            });
            return;
        } catch (error) {
            throw error;
        }
    }

    arrayBufferToBase64(buffer) {
        let binary = "";
        const bytes = new Uint8Array(buffer);
        const len = bytes.byteLength;
        for (let i = 0; i < len; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return window.btoa(binary);
    }

    deauthenticate() {
        jwt.set("");
        this.deleteKeys();
        // force a page reload which drops the token
        // push('/');
        window.location.reload();
    }

    async checkJWT() {
        if (this.JWT && this.JWT != "") {
            const decoded = jwtDecode(this.JWT);
            const now = new Date();
            if (decoded.exp > now.getTime() / 1000) {
                return true;
            } else {
                throw false;
            }
        } else {
            throw false;
        }
    }

    async doAPICall(action, data, token = null) {
        const url = this.auth_endpoint;
        let headers = {
            "Content-Type": "application/json; charset=utf-8",
        };
        if (token) headers.Authorization = "Bearer " + token;
        const options = {
            method: "POST",
            headers: headers,
            body: JSON.stringify({
                action: action,
                data: data,
            }),
            mode: "cors",
        };
        try {
            const response = await fetch(url, options);
            const responseData = await response.json();
            responseData["http_code"] = response.status;
            if (response.status >= 200 && response.status < 300) {
                return responseData;
            } else {
                throw responseData;
            }
        } catch (error) {
            throw error;
        }
    }

}
