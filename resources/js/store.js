import { reactive } from 'vue'

export const store = reactive({
  error: false,
  errorMsgHeader: '',
  errorMsgTxt: '',
  token: null,
  userData: null,
  isListComponent: false,
  router: null,
  addNew: ''
})