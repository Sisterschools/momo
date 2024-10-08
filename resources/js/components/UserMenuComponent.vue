<script setup>
  defineProps({
    user: {type: Object, default: () => { return { name: '', role: ''}}},
    okText: {type: String, default: 'Ok'}
  })
</script>

<script>
export default{
  methods:{
    expand(){
      this.$el.querySelector('.dropdown').classList.toggle('hide')
      this.$el.querySelector('.expandable').classList.toggle('expanded')
    }
  }
}
</script>

<template>
  <ul :class="'clr-border-left-' + user.role">
    <li 
      class="name"
      @click.stop="expand"
    >
      <span class="expandable"/>
      {{ user.name }}
    </li>
    <li>
      <ul class="dropdown hide">
        <slot />
      </ul>
    </li>
  </ul>
</template>

<style scoped>
.dropdown{
  position: absolute;
  z-index: 99;
  background-color: var(--momo-white);
}
</style>