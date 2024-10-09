var serverAPI = ( uri, vars, method = 'POST', token, contentType = 'application/x-www-form-urlencoded', putAsPost = false) => {

  var headers = {
    "Content-Type": contentType,
    "Accept": "application/json",
  } 

  if( token ){
    headers["Authorization"] = "Bearer " + token
  }

  var body = new URLSearchParams(vars)

  if(contentType == ''){
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
  .then( ( response ) => {
    if( ! response.ok ){
      return Promise.reject(response.json())
    }
    if(response.ok && response.status < 300)
      return response
  })
  .then( ( response ) => response.json())
}

export default serverAPI