import { createStore } from 'vuex';

const store = createStore({
    state: {
        return: {
            userId: '', 
            accessToken: '', 
            refreshToken: ''
        }
    },
    mutations: {
        StoreToken(state, returnValue){
            state.accessToken = returnValue.access_token;
            sessionStorage.setItem('accessToken', returnValue.access_token);
            state.refreshToken = returnValue.refresh_token;
            sessionStorage.setItem('refreshToken', returnValue.refresh_token);
            if(returnValue.user && returnValue.user.id && returnValue.user.id !=''){
                state.userId = returnValue.user.id;
                sessionStorage.setItem('userId', returnValue.user.id);
            }
            console.log('success update to store')
        }
    },
    actions: {
        async CallStoreToken({commit}, form){
            commit('StoreToken', form);
            return;
        }
    }
})

export default store;