<template>
    <table v-if="todoList.length">
        <thead>
            <tr>
                <td>待辦事項</td>
                <td>事項內容</td>
                <td>開始時間</td>
                <td>結束時間</td>
                <td v-if="isForPatch">已完成</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(event, index) in todoList" :key="index">
                <td><input type="text" v-model="event.title" required></td>
                <td><input type="text" v-model="event.description" required></td>
                <td><input type="datetime-local" v-model="event.start_date"></td>
                <td><input type="datetime-local" v-model="event.due_date"></td>
                <td v-if="isForPatch" style="padding-left: 17px;">
                    <input type="checkbox" v-model="event.completed" :value="!!event.completed">
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    export default{
        props: ['todoList', 'isForPatch'],
        data() {
            return {
                todoList_table: []
            }
        },
        watch: {
            todoList: {
                handler(newValue){
                    this.todoList_table = newValue;
                }, 
                deep: true
            },
            todoList_table: {
                handler(newValue){
                    this.$emit('update:todoList', newValue);
                },
                deep: true
            }
        }
    }
</script>
<style>

</style>