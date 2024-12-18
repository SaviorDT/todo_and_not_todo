<template>
    <div style="margin: 0 auto; width: 100%;">
        <textarea v-model="forAI.userinput" class="inputHolder" placeholder="請輸入TODO list:"></textarea>
        <input type="password" id="password" v-model="forAI.api_key" placeholder="請輸入api key" required style="width: 600px; font-size: 24px; margin-bottom: 5px;"/>
        <div style="padding-bottom: 10px;">
            <button @click="AIconvert" :disabled="isLoading">進行AI辨識</button>
            <button @click="addTodo">手動新增todo</button>
        </div>
        <h3 v-if="newTodo.length">即將新增：</h3>
        <TodoList :todoList="newTodo"/>
        <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
    </div>
</template>

<script>
    import { watch } from 'vue';
    import { fetchFromAI } from '../../fetch';
    import TodoList from './TodoList.vue';
    import dayjs from 'dayjs';

    export default {
        emits: ['updateTodo'],
        components: {
            TodoList
        },
        data() {
            return {
                //variable name
                forAI: {
                    userinput: '',
                    api_key: ''
                },
                newTodo: [],
                errorMsg: '',
                isLoading: false
            }
        },
        methods: {
            async AIconvert(){
                this.isLoading = true;
                let AItodo = await fetchFromAI(this.forAI);
                if(AItodo.success)this.newTodo = this.newTodo.concat(AItodo.message);
                else this.errorMsg = AItodo.message;
                this.isLoading = false;
            },
            addTodo(){
                let defaultData= { title: '' , description: '', start_date: '', due_date: ''};
                this.newTodo.push(defaultData);
            },
            async sendToMain(){
                // console.log(this.newTodo)
                this.$emit('updateTodo', this.newTodo);
            }
        },
        watch: {
            newTodo: {
                handler(newValue){
                    this.sendToMain()
                }, 
                deep: true
            },
            'forAI.api_key': {
                handler(newValue){
                    sessionStorage.setItem('api_key', newValue);
                }
            }
        },
        mounted(){
            const storedApiKey = sessionStorage.getItem('api_key');
            if(storedApiKey && storedApiKey!=undefined && storedApiKey!=''){
                this.forAI.api_key = storedApiKey;
            }
        }
  }
</script>

<style scoped>
    .inputHolder{
        width: 90vw;
        height: 50vh;
        font-size: 24px;
    }
    button{
        width: auto;
        font-size: 24px;
        margin-right: 10px;
    }
    .error-message {
        color: red;
        text-align: center;
        margin-top: 10px;
    }
    button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
</style>