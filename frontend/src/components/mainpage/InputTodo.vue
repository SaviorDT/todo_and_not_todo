<template>
    <!-- <input v-model="variable" placeholder="提示文字"> -->
    <textarea v-model="inputText" class="inputHolder" placeholder="請輸入TODO list:"></textarea>
    <button @click="sendPost">進行AI辨識</button>
    <table>
        <tr>
            <td>
                {{ output }}
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
        data() {
            return {
                //variable name
                inputText: '',
                output: ''
            }
        },
        methods: {
            //function, use this.variable = ...
            sendPost(){
                this.output = this.inputText;
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