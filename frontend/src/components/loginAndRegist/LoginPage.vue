<template>
    <div class="login-container">
        <h2>登入</h2>
        <form @submit.prevent="handleLogin">
          <div class="form-group">
              <label for="useremail">電子信箱</label>
              <input type="email" id="useremail" v-model="form.email" placeholder="請輸入註冊時的電子信箱" required />
          </div>
          
          <div class="form-group">
              <label for="password">密碼</label>
              <input type="password" id="password" v-model="form.password" placeholder="請輸入密碼" required />
          </div>
          
          <RouterLink to="/regist">註冊</RouterLink>
          <button type="submit" :disabled="loading">登入</button>
          <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
        </form>
    </div>
</template>

<script>
import { RouterLink } from 'vue-router';
import { LoginUser } from '../../fetch.js';

export default {
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      errorMsg: '',
      loading: false
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      const response = await LoginUser(this.form);
      if(response.success){
        this.$router.push('/main_page');
      }
      else{
        this.errorMsg = response.message;
      }
      this.loading = false;
    }
  }
};
</script>

<style scoped>

.login-container {
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