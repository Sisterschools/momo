<script setup>
  defineProps({
    caption: {type: String, default: 'Please fill out this form'},
    okText: {type: String, default: 'Ok'}
  })
</script>

<script>
export default{
  emits: ['form-submitted'],
  methods: {
    submit() {
      this.$emit('form-submitted', this.email)
    }
  }
}
</script>

<template>
  <form @submit.prevent="submit">
    <div>{{ caption }}</div>
    <slot />
    <button>
      {{ okText }}
    </button>
  </form>
</template>  

<style scoped>
button{
  float: right;
}
</style>