import axios from "axios";
import store from '@/store';
import router from "./router";
import dayjs from "dayjs";

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
        let flag = 1;
        for(let todo of postList){
            if(!todo.description)todo.description = "";
            if(todo.title && todo.title!=''){
                if(!todo.start_date) todo.start_date = null;
                if(!todo.due_date) todo.due_date = null;
                if(todo.start_date && todo.due_date && dayjs(todo.due_date).diff(dayjs(todo.start_date), 'hour') < 1)todo.due_date = dayjs(todo.start_date).add(0.5, 'hour').format('YYYY-MM-DD HH:mm');
                await api.post('/api/todo', todo);
            }
            else{
                flag = 0;
            }
        }
        for(let todo of patchList){
            if(!todo.description)todo.description = "";
            if(todo.title && todo.title!=''){
                if(dayjs(todo.due_date).diff(dayjs(todo.start_date), 'hour') < 1)todo.due_date = dayjs(todo.start_date).add(0.5, 'hour').format('YYYY-MM-DD HH:mm');
                await api.patch(`/api/todo/${todo.id}`, todo);
            }
            else{
                flag = 0;
            }
        }
        return [true, flag ? '上傳成功，將重新載入' : '有上傳為執行，請確認必填欄位是否未填寫'];
    }
    catch(error){
        console.error('上傳失敗:', error.response.data);
        return [false, error.response.data.message];
    }
}

export async function fetchFromAI(form){
    try{
        const response = await axios.get('/api/gemini', {
            params: form
        });
        let data = response.data;
        data.forEach((todo, index, array) => {
            if(todo.start_date)array[index].start_date = dayjs(todo.start_date).format('YYYY-MM-DD HH:mm')
            if(todo.due_date)array[index].due_date = dayjs(todo.due_date).format('YYYY-MM-DD HH:mm')
        });
        
        return {success: true, message: data};
    }
    catch(error){
        console.error('辨識失敗:', error.response.data);
        return {success: false, messsage: error.response.data.message};
    }
}