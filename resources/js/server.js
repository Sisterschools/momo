var serverAPI = ( uri, vars, method = 'POST', token ) => {

  var headers = {
    "Content-Type": "application/x-www-form-urlencoded",
  }

  if( token ){
    headers["Authorization"] = "Bearer " + token
  }

  var body = new URLSearchParams(vars)
  
  return fetch( uri, { 
    method,
    headers,
    ...(method == 'POST' && {body}) 
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