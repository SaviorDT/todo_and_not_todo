import axios from "axios";
import store from '@/store';
import router from "./router";
import dayjs from "dayjs";
import { day } from "vue-cal/dist/i18n/ar.cjs";

const api = axios.create();
api.interceptors.request.use(
    (config) => {
        config.headers['Authorization'] = `Bearer ${store.state.accessToken}`
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
)

api.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        const originalRequest = error.config;

        if (error.response && error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                const refresh_token = store.state.refreshToken ? store.state.refreshToken : (sessionStorage.getItem('refreshToken') && sessionStorage.getItem('refreshToken')!='undefined' ? sessionStorage.getItem('refreshToken') : '');
                const refreshResponse = await api.post('/api/auth/refresh', {
                    refresh_token: refresh_token,
                });

                const newAccessToken = refreshResponse.data.access_token;
                await store.dispatch('CallStoreToken', refreshResponse.data);

                originalRequest.headers['Authorization'] = `Bearer ${newAccessToken}`;
                return api(originalRequest);
            } catch (refreshError) {
                console.error('Token refresh failed:', refreshError);
                sessionStorage.clear();
                alert('離線過久，將重新登入');
                router.push('/');
                return Promise.reject(refreshError);
            }
        }
        return Promise.reject(error);
    }
);

export async function RegistUser(form){
    try {
        const response = await axios.post('/api/auth/register', form);
        return {success: true, message: "註冊成功"};
    } catch (error) {
        console.error('註冊失敗:', error.response.data);
        return {success: false, message: error.response.data.message};
    }
}

export async function LoginUser(form){
    try {
        const response = await axios.post('/api/auth/login', form);
        console.log(response.data);
        await store.dispatch('CallStoreToken', response.data);
        return {success: true, message: '登入成功'};
    }
    catch(error){
        console.error('登入失敗:', error.response.data);
        return {success: false, message: error.response.data.message};
    }
}

export async function GetTodoFromDb() {
    // /api/todo
    try {
        const response = await api.get('/api/todo');
        // console.log(response.data)
        return response.data;
    }
    catch(error){
        console.error('fetch失敗:', error.response.data);
        return [];
    }
}

export async function UploadTodo(postList, patchList) {
    try{
        for(let todo of postList){
            if(todo.title && todo.title!='' && todo.description && todo.description!=''){
                await api.post('/api/todo', todo);
            }
        }
        for(let todo of patchList){
            if(todo.title && todo.title!='' && todo.description && todo.description!=''){
                if(dayjs(todo.due_date).diff(dayjs(todo.start_date), 'hour') < 1)todo.due_date = dayjs(todo.start_date).add(1, 'hour').format('YYYY-MM-DDThh:mm');
                await api.patch(`/api/todo/${todo.id}`, todo);
            }
        }
        return 'update successsfully';
    }
    catch(error){
        console.error('上傳失敗:', error.response.data);
        return error.response.data.message;
    }
}