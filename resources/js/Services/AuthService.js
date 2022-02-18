import Axios from 'axios';

export const loginAttempt = async (data) => {
    let request = Axios({
        method : 'post',
        data : data,
        url : window.origin + '/login'
    });
    return Promise.resolve(request);
};
export const currentUser = async (token) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'get',
        url : window.origin + '/api/auth/me'
    });
    return Promise.resolve(request);
};
export const allUsers = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/users'
    });
    return Promise.resolve(request);
};