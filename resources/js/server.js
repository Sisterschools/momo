var postServer = ( uri, vars, token ) => {

  var headers = {
    "Content-Type": "application/x-www-form-urlencoded",
  }

  if( token ){
    headers["Autorization"] = "Bearer " + token
  }

  var body = new URLSearchParams(vars)
  
  return fetch( uri, { 
    method: 'POST',
    headers,
    body
  } )
  .catch( console.log )
}

export default postServer