<template>
    <table>
        <thead>
            <tr>
                <td>待辦事項</td>
                <td>事項內容</td>
                <td>開始時間</td>
                <td>結束時間</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(event, index) in todoList" :key="index">
                <td><input type="text" v-model="event.title" required></td>
                <td><input type="text" v-model="event.content" required></td>
                <td><input type="datetime-local" v-model="event.start"></td>
                <td><input type="datetime-local" v-model="event.end"></td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import { Cal2Db } from '../../convertTodo';
    export default{
        props: ['todoList'],
        data() {
            return {
                todoList_table: []
            }
        },
        watch: {
            todoList: {
                handler(newValue){
                    this.todoList_table = newValue;
                    console.log(newValue)
                }, 
                deep: true
            },
            todoList_table: {
                handler(newValue){
                    const temList = Cal2Db(newValue);
                    this.$emit('update:todoList', temList);
                },
                deep: true
            }
        }
    }
</script>
<style>

</style>