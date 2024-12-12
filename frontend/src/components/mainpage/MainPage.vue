<template>
    <div class="calendar-page">
      <TextInput @updateTodo = "getNewTodo"/>
      <Calendar :newTodo = "newTodoList"/>
    </div>
  </template>
  
  <script>
  import TextInput from './InputTodo.vue'
  import Calendar from './Calendar.vue'
  
  export default {
    components: {
      TextInput,
      Calendar,
    },
    data() {
        return {
            newTodoList: []
        };
    },
    methods: {
        getNewTodo(newAddTodo){
            this.newTodoList = newAddTodo;
            console.log(newAddTodo);
        }
    }
  }
  </script>
  
  <style scoped>
  </style>
  