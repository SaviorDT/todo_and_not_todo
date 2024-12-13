<template>
    <div style="margin: 0 auto; width: 100%;">
        <textarea v-model="inputText" class="inputHolder" placeholder="請輸入TODO list:"></textarea>
        <div style="padding-bottom: 10px;">
            <button @click="AIconvert">進行AI辨識</button>
        </div>
        <h3 v-if="newTodo.length">即將新增：</h3>
        <TodoList :todoList="newTodo"/>
        <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
    </div>
</template>

<script>
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
                inputText: '',
                newTodo: [],
                errorMsg: '',
                testData: [{ title: 'Event 2' , description: 'this is a test', start_date: dayjs('2024-12-11').format('YYYY-MM-DD HH:mm'), due_date: dayjs('2024-12-12').format('YYYY-MM-DD HH:mm')}]
            }
        },
        methods: {
            AIconvert(){
                this.newTodo = this.testData;
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
        width: 20vw;
        font-size: 24px;
    }
    .error-message {
        color: red;
        text-align: center;
        margin-top: 10px;
    }
</style>