var serverAPI = ( uri, vars, method = 'POST', token, contentType = 'application/x-www-form-urlencoded') => {

  var headers = {
    "Content-Type": contentType,
    "Accept": "application/json"
  }

  if( token ){
    headers["Authorization"] = "Bearer " + token
  }

  var body = new URLSearchParams(vars)

  if( method == 'PUT'){
    body = JSON.stringify( vars )
  }

  return fetch( uri, { 
    method,
    headers,
    ...((method == 'POST' || method == 'PUT') && {body}) 
  } )
  .catch( console.log )
  .then( ( response ) => {
    if(response.ok && response.status < 300)
      return response
    else{
      throw("Error")
    }
  })
  .then( ( response ) => response.json())
}

export default serverAPI