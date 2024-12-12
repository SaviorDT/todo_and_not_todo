<template>
    <textarea v-model="inputText" class="inputHolder" placeholder="請輸入TODO list:"></textarea>
    <button @click="AIconvert">進行AI辨識</button>
    <button @click="sendUpdateList">新增到行事曆</button>
    <table>
        <tr>
            <td>
                {{ newTodo }}
            </td>
        </tr>
        <!-- <tr v-for="(row, index) in data" :key="index">
          <td v-for="(value, key) in row" :key="key">{{ value }}</td>
        </tr> -->
    </table>
</template>

<script>
    import axios from 'axios';
    export default {
        emits: ['updateTodo'],
        data() {
            return {
                //variable name
                inputText: '',
                newTodo: []
            }
        },
        methods: {
            AIconvert(){
                this.newTodo.push(this.inputText);
                this.$emit('updateTodo', this.newTodo);
            },
            sendUpdateList(){
                //should pudh to
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