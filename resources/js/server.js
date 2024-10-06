var serverAPI = ( uri, vars, method = 'POST', token, contentType = 'application/x-www-form-urlencoded', putAsPost = false) => {

  var headers = {
    "Content-Type": contentType,
    "Accept": "application/json",
  } 

  if( token ){
    headers["Authorization"] = "Bearer " + token
  }

  var body = new URLSearchParams(vars)

  if(contentType == 'multipart/form-data'){
    body = new FormData()
    for(var i in vars){
      body.append(i, vars[i])
    }
    if(putAsPost)
      body.append('_method', 'PUT')
  }

  if( (method == 'POST' || method == 'PUT') && contentType == 'application/json'){
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
      return Promise.reject()
    }
  })
  .then( ( response ) => response.json())
}

export default serverAPI