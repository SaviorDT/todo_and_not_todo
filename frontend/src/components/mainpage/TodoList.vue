<template>
    <div v-if="isForPatch && todoList.length" >
        <input type="checkbox" name="showCompletedSwitch" v-model="showCompleted">
        <label for="showCompletedSwitch" style="padding-left: 10px;">顯示已完成</label>
    </div>
    <table v-if="todoList.length">
        <thead>
            <tr>
                <td>待辦事項<span style="color: red;">*</span></td>
                <td>事項內容</td>
                <td>開始時間</td>
                <td>結束時間</td>
                <td v-if="isForPatch">已完成</td>
            </tr>
        </thead>
        <tbody v-for="(event, index) in todoList" :key="index">
            <tr v-if="showCompleted || !event.completed">
                <td><input type="text" v-model="event.title" required></td>
                <td><input type="text" v-model="event.description" required></td>
                <td><input type="datetime-local" v-model="event.start_date"></td>
                <td><input type="datetime-local" v-model="event.due_date" :min="getMinDate(event.start_date)"></td>
                <td v-if="isForPatch" style="padding-left: 17px;"><input type="checkbox" v-model="event.completed"></td>
                <td v-if="!isForPatch"><button @click="deleteIndex(index)">刪除</button></td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import dayjs from 'dayjs';

    export default{
        props: ['todoList', 'isForPatch', 'isShowCompleted'],
        data() {
            return {
                todoList_table: [],
                showCompleted: this.isShowCompleted
            }
        },
        methods: {
            getMinDate(start_date){
                return dayjs(start_date).add(0.5, 'hour').format('YYYY-MM-DD HH:mm');
            },
            deleteIndex(index){
                this.todoList_table.splice(index, 1);
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
            },
            showCompleted: {
                handler(newValue){
                    this.$emit('update:isShowCompleted', newValue);
                }
            }
        }
    }
</script>
<style>

</style>