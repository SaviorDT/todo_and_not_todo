<template>
    <button style="position: absolute; right: 0; top: 0; width: auto;" @click="logout">登出</button>
    <div class="main_page">
      <TextInput @updateTodo = "getNewTodo"/>
      <h3 v-if="fetchedTodo.length">已在資料庫：</h3>
      <TodoList :todoList="fetchedTodo" :isForPatch="true" v-model:isShowCompleted="showCompleted"/>
      <div style="width: 100%; padding-bottom: 10px; padding-top: 10px;">
        <button v-if="newTodoList.length>0 || (eventsInDb.length && JSON.stringify(fetchedTodo)!==JSON.stringify(eventsInDb))" @click="sendToDb" :disabled="loading">上傳變更</button>
        <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
      </div>
      <Calendar @fetchedTodo = "getStoredTodo" :newTodo = "newTodoList" :showCompleted="showCompleted"/>
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
            loading: false,
            showCompleted: false
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
          this.errorMsg = '';
          let updatedList = await this.checkIsSame(this.fetchedTodo, this.eventsInDb);
          try{
            let response = await UploadTodo(this.newTodoList, updatedList);
            if(response[0]){
              alert(response[1])
              window.location.reload();
            }
            else{
              this.errorMsg = response[1];
            }
          }
          catch(error){
            console.log(error)
          }
          this.loading = false;
        },
        logout(){
          alert("即將登出");
          sessionStorage.clear();
          this.$router.push('/');
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
  