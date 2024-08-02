
export function apisockets(endpoint, action, json_data, jwt=null, retry=false){

    let headers={
        'Content-Type': 'application/json; charset=utf-8',
    }
    
    if(jwt) headers.Authorization = 'Bearer ' + jwt;
    
    return new Promise(function(resolve, reject) {    
        fetch("https://api.apisockets.com/"+endpoint, {
            method: 'POST',
            body: JSON.stringify({
                action: action,
                data: json_data
            }),
            headers: headers,
            mode: 'cors'
        })
        .then(async (response) => {
            response.json()
            .then((data) => {
                if (response.status >= 200 && response.status < 300) {
                    resolve(data);    
                }else{
                    data.status = response.status;
                    reject(data);
                }
            })
            .catch((error)=>{
                if(retry ==true){ 
                    console.log("can't recover from this error.  I give up.")
                    reject(false);
                    return;
                }

            })
        })
        .catch((error) => { 
            console.log('something bad happened');
            reject(error);
        });
    });
}
