import { apisockets } from "./apisockets.js";

// Validation Rules

/*
    When writing these rules, always validate values, not fields
*/


export const V = {
    
    fullName: element => {
        return apiValidate( "FullName", { tainted: element.value })
    },

    phone: element => {
        return apiValidate( "PhoneNumber", { tainted: element.value } )
    },

    email: element => {
        return apiValidate( "Email", { tainted: element.value } )
    },

    url: element => {
        return apiValidate( "Url", { tainted: element.value } )
    },

    bsb: element => {
        return apiValidate("BSB", { tainted: element.value } )
    },

    date: element => {
        return apiValidate( "Date", { tainted: element.value } )
    },


    required: element => {
        return new Promise(function(resolve,reject){
            validateRequiredField({
                field_value: element.value, 
                error_message: "'"+element.label+"' can't be blank."
            }).then(function(){
                resolve(true);
            }).catch(function(reason){
                reject(reason);
            });
        });
    },


    bankAccountNumber: element=>{
        return validateRegexField({
            field_value: element.value, 
            regex: new RegExp(/^[0-9]{6,10}$/g), 
            error_message: "Australian Bank Account Numbers must be numeric and between 6 to 10 digits long", 
            
        })
        // .then(function(element){
        //     clearValidationError(self.get('ref')+'_bank_account_number');
        //     resolve(element);
        // }).catch(function(reason){
        //     displayValidationError(reason);
        //     reject(reason);
        // });





        // return new Promise(function(resolve,reject){
        //     validateRegexField({
        //         field_value: element.value, 
        //         regex: new RegExp(/^[0-9]{4}$/g), 
        //         error_message: "Must be an Australian Postcode"
        //     }).then(function(){
        //         return apiValidate({
        //             type: "Postcode",
        //             tainted: element.value
        //         })
        //     }).catch(function(reason){
        //         reject(reason);
        //     });
        // });
    },


    postcode: element => {
        return new Promise(function(resolve,reject){
            validateRegexField({
                field_value: element.value, 
                regex: new RegExp(/^[0-9]{4}$/g), 
                error_message: "Must be an Australian Postcode"
            }).then(function(){
                return apiValidate( "Postcode", { tainted: element.value } )
            }).catch(function(reason){
                reject(reason);
            });
        });
    },

    businessName: element => {
        return  new Promise(function(resolve,reject){
            validateFunctionField({
                field_value: element.value, 
                func: ()=>{
                    term = element.value.trim();
                    blank_regex = new RegExp(/.*\S.*/);
                    if ((blank_regex.test(term))){
                        return true;    
                    } else{
                        error_message = "Business name can't be blank.";
                        return false;
                    }
                }, 
                error_message: "There was an error", //validation_error_message, 
            }).then(function(){
                resolve(true);
            }).catch(function(reason){
                reject(element);
            });
        });
    },

    street: element=> {
        return new Promise(function(resolve,reject){
            validateRegexField({
                field_value: element.value, 
                regex: new RegExp(/^\s*(.*((p|post)[-.\s]*(o|off|office)[-.\s]*(b|box|bin)[-.\s]*)|.*((p|post)[-.\s]*(o|off|office)[-.\s]*)|.*((p|post)[-.\s]*(b|box|bin)[-.\s]*)|(box|bin)[-.\s]*)(#|n|num|number)?\s*\d+/i), 
                negate_regex: true,
                reject_blanks: true, 
                error_message: "Address is required.  PO Boxes are not accepted."
            }).then(function(){
                resolve(true);
            }).catch(function(reason){
                reject(element);
            });
        });
    },

    ticked: element => {
        return new Promise(function(resolve,reject){
            if ($("#"+element.ref).is(':checked')){
                resolve(true);
            }else{
                reason = {
                    error_message: 'You must tick the checkbox.',
                };
                reject(reason);
            }
        });
    },

    execute: async function (elements) {
        const results = [];
        for (let el of elements) {
            try {
                results.push(await componentsHash[el.ref].validate(el));
            } catch(err) {
                throw err;
            }
        }    
        return results;
    }
}


function validateRegexField(validation){
    return new Promise(function(resolve, reject) {
        
        if (validation.negate_regex){
            result = !validation.regex.test(validation.field_value);
        }else{
            result = validation.regex.test(validation.field_value);
        }

        if (validation.reject_blanks){
            blank_regex =  new RegExp(/.*\S.*/);
            if(!blank_regex.test(validation.field_value)){
                result = false;
            };
        }

        
        if(
            (typeof validation.field_value !== 'undefined')
            && (result==true)
        ){
            resolve(true);
        }else{
            reject({
                error_message: validation.error_message,
            });
        }
    });
}


function validateRequiredField(validation){
    return new Promise(function(resolve, reject) {
        if(validation.field_value != null){
            blank_regex =  new RegExp(/.*\S.*/);
            if(!blank_regex.test(validation.field_value)){
                reject({
                    
                    error_message: validation.error_message
                });
            }else{
                resolve(true);
            }
        }else{
            reject({
                error_message: validation.error_message
            });
        }
    });
}

function validateFunctionField(validation){
    return new Promise(function(resolve, reject) {
        if(validation.func()){
            resolve(true);
        }else{
            reject({
                error_message: validation.error_message,
            });
        }
    });
}



function fetchApiValidate(endpoint, json_data){
    return new Promise(function(resolve, reject) {    

        // TODO: consider not POSTing to the validation API if there is no data to validate

        fetchApi(api_endpoint + endpoint, 'POST',JSON.stringify(json_data))
        .then(response => {
            resolve(response);
        }).catch(response => {
            // TODO: I don't think this is the right way to detect if fetch failed due to cors.
            if(response == "TypeError: Failed to fetch"){
                reason = {
                    error_message: "The validation api cannot be accessed for this field."
                };
            }else{
                reason = {
                    error_message: response.error_message
                };
            }
            reject(reason);
        });
    });
}






function apiValidate(action, json_data){
    
    return new Promise(function(resolve, reject) {    
        apisockets("validate", action, json_data)
        .then(response => {
            resolve(response);
        })
        .catch( error=>{
            if (error.status==401){
                reject( { error_message: "There's a problem with your access token." } )
                console.log('something bad happened');
            }else{
                reject( { error_message: error.error_message } )
            }
            
        } )
    });

}






