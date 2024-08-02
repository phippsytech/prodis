export function fetchApi (endpoint, method, data) {



    // TODO:  I NEED TO CHECK IF THE API CALL RESPONDS WITH EXPIRED TOKEN.  IF IT DOES i SHOULD REFRESH THE TOKEN AND TRY AGAIN
    
    
    
        // let token=null;
        // if (ractive && ractive.get('app.token')){
        //     token = ractive.get('app.token');
        // }
    
        let headers={
            'Content-Type': 'application/json; charset=utf-8',
            // 'Authorization': 'Bearer ' + token
        }
        
        // headers.Authorization = 'Bearer ' + token;
    
        data = data || null;
    
    
    
    
        return new Promise(function(resolve, reject) {    
            fetch(endpoint, {
                method: method,
                body: data,
                
                headers: headers,
                mode: 'cors',
                // credentials: 'include'
            }).then(response => { 
                
                
                
                response.json().then((data) => {
                    
    
                if (response.status >= 200 && response.status < 300) {
                    resolve(data);    
                }else{
                    // THIS CHANGE MAY HAVE UNFORSEEN CONSEQUENCES
                    data.status = response.status
                    reject(data);
                }
            })
            
        }
            ).catch((error) => {
                reject(error);
            });
        });
    }