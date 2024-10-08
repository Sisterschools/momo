import { store } from './store.js';

var makeErrorMsg = (err) => {
  if(err && err.then){
    err.then( ( e ) => {
      var txt = ''
      store.errorMsgHeader = e.message
      for(var t in e.errors )
        txt += e.errors[t]
      store.errorMsgTxt = txt
      store.error = true
    })
  }
  else
    store.errorMsgHeader = 'No Internet ?'
}

export default makeErrorMsg