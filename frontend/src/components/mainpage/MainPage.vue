<template>
    <div class="main_page">
      <TextInput @updateTodo = "getNewTodo"/>
      <h3 v-if="fetchedTodo.length">已在資料庫：</h3>
      <TodoList :todoList="fetchedTodo" :isForPatch="true"/>
      <div style="width: 100%; padding-bottom: 10px; padding-top: 10px;">
        <button v-if="newTodoList.length>0 || (eventsInDb.length && JSON.stringify(fetchedTodo)==JSON.stringify(eventsInDb))" @click="sendToDb" :disabled="loading">上傳變更</button>
        <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
      </div>
      <Calendar @fetchedTodo = "getStoredTodo" :newTodo = "newTodoList"/>
    </div>
  </template>
  
  <script>
  import TextInput from './InputTodo.vue'
  import Calendar from './Calendar.vue'
  import TodoList from './TodoList.vue';
  import { UploadTodo } from '../../fetch.js';
  
  export default {
    components: {
      TextInput,
      Calendar,
      TodoList
    },
    data() {
        return {
            newTodoList: [],
            fetchedTodo: [],
            eventsInDb: [],
            errorMsg: '',
            loading: false
        };
    },
    methods: {
        getNewTodo(newAddTodo){
            this.newTodoList = newAddTodo;
        },
        getStoredTodo(returnTodo){
          if(this.eventsInDb.length==0){
            this.eventsInDb = JSON.parse(JSON.stringify(returnTodo));
          }
          this.fetchedTodo = returnTodo;
        },
        async checkIsSame(newValue, oldValue){
          let temList = [];
          if(newValue.length>0 && oldValue.length>0){
            newValue.forEach((event, index) => {
              if(JSON.stringify(event) !== JSON.stringify(oldValue[index])){
                temList.push(event);
              }
            });
          }
          return temList;
        },
        async sendToDb(){
          this.loading = true;
          let updatedList = await this.checkIsSame(this.fetchedTodo, this.eventsInDb);
          try{
            let response = await UploadTodo(this.newTodoList, updatedList);
            this.errorMsg = response;
            window.location.reload();
          }
          catch(error){
            console.log(error)
          }
          this.loading = false;
        }
      },
  }
  </script>
  
<style scoped>
  .error-message {
      color: red;
      text-align: center;
      display: inline;
      padding-left: 20px;
      font-size: 18px;
  }
  button{
      width: 20vw;
      font-size: 24px;
  }
</style>
  