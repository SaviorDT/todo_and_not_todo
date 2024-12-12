import { createStore } from 'vuex';
import axios from 'axios';

const store = createStore({
    state: {
        return: {
            user: '', 
            accessToken: '', 
            refreshToken: ''
        }
    },
    mutations: {
        StoreToken(state, returnValue){
            state.accessToken = returnValue.access_token;
            state.refreshToken = returnValue.refresh_token;
            state.user = returnValue.user.user_name;
            console.log('success update to store')
        }
    },
    actions: {
        async RegistUser({ commit }, form){
            try {
                const response = await axios.post('/api/auth/register', form);
                return {success: true, message: "註冊成功"};
            } catch (error) {
                console.error('註冊失敗:', error.response.data);
                return {success: false, message: error.response.data.message};
            }
        },
        async LoginUser({commit}, form){
            try {
                const response = await axios.post('/api/auth/login', form);
                console.log(response.data);
                commit('StoreToken', response.data);
                return {success: true, message: '登入成功'};
            }
            catch(error){
                console.error('登入失敗:', error.response.data);
                return {success: false, message: error.response.data.message};
            }
        }
    }
})

export default store;