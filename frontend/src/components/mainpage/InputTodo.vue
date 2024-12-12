<template>
    <textarea v-model="inputText" class="inputHolder" placeholder="請輸入TODO list:"></textarea>
    <button @click="AIconvert">進行AI辨識</button>
    <button @click="sendUpdateList">新增到行事曆</button>
    <h3>未上傳：</h3>
    <TodoList :todoList="newTodo" />
</template>

<script>
    import TodoList from './TodoList.vue';
    import { Db2Cal } from '../../convertTodo';
    export default {
        emits: ['updateTodo'],
        components: {
            TodoList
        },
        data() {
            return {
                //variable name
                inputText: '',
                newTodo: []
            }
        },
        methods: {
            AIconvert(){
                // this.newTodo.push(this.inputText);
                this.newTodo = Db2Cal([{ title: 'Event 2' , description: '', start_date: '2024-12-11', due_date: '2024-12-12'}]);
            },
            sendUpdateList(){
                //should push to
                this.$emit('updateTodo', this.newTodo);
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
</style>