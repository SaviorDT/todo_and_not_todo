<template>
    <div class="container">
      <h2>註冊用戶</h2>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="name">姓名</label>
          <input type="text" id="name" v-model="form.name" placeholder="請輸入名稱" required />
        </div>
  
        <div class="form-group">
          <label for="email">電子郵件</label>
          <input type="email" id="email" v-model="form.email" placeholder="請輸入信箱" required />
        </div>
  
        <div class="form-group">
          <label for="password">密碼</label>
          <input type="password" id="password" v-model="form.password" placeholder="請輸入密碼" required />
        </div>
  
        <RouterLink to="/">返回登入畫面</RouterLink>
        <button type="submit">送出</button>
        <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
      </form>
    </div>
  </template>
  
  <script>
  import { RegistUser } from '../../fetch.js';
  import { RouterLink } from 'vue-router';
  export default {
    data() {
      return {
        form: {
          name: '',
          email: '',
          password: ''
        },
        errorMsg: '',
      };
    },
    methods: {
      async submitForm() {
        RegistUser(this.form)
        .then(response => {
          if(response.success){
            alert('註冊成功，將回到登入頁面');
            this.$router.push('/');
            return;
          }
          this.errorMsg = response.message;
        })
        .catch(error => {
          console.log(error)
        })
      }
    }
  };
  </script>
  
  <style scoped>
  .container {
    width: 50vw;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 15px;
  }

  label {
    display: block;
    margin-bottom: 5px;
  }

  input {
    width: 100%;
    padding: 8px;
    margin: 0;
    border-radius: 4px;
    border: 1px solid #ddd;
  }

  button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
  }

  button:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
  }

  .error-message {
    color: red;
    text-align: center;
    margin-top: 10px;
  }
  
</style>
  