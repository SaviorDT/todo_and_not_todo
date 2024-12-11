<template>
    <div class="calendar-page">
      <TextInput />
      <Calendar />
    </div>
  </template>
  
  <script>
  import TextInput from './InputTodo.vue'
  import Calendar from './Calendar.vue'
  
  export default {
    components: {
      TextInput,
      Calendar,
    }
  }
  </script>
  
  <style scoped>
  </style>
  