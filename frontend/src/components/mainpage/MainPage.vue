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
            // this.newTodoList = newAddTodo;
            console.log('get new todo')
            this.newTodoList = [{ start: '2024-12-11', end: '2024-12-11', title: 'Event 2' }];
        }
    }
  }
  </script>
  
  <style scoped>
  </style>
  