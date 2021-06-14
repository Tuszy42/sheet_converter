import axios from "axios";
import { setErrorMessage } from './utils'

axios.interceptors.response.use(
    response => {
        return response;
    },
    async ({ response }) => {
        debugger;
        if(response.status === 400){
            const responseBody = await response.data.text();
            setErrorMessage(JSON.parse(responseBody).messages);
        }
        return Promise.reject(response)
    }
);