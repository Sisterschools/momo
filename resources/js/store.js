import { reactive } from 'vue'

export const store = reactive({
  error: false,
  token: null,
  userData: null,
  isListComponent: false,
  router: null,
  addNew: ''
})