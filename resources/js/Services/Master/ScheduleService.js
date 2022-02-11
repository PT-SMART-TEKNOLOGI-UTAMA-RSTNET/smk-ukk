import Axios from 'axios';

export const getSchedules = async (token) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token },
        method : 'post',
        url : window.origin + '/api/auth/master/schedules'
    });
    return Promise.resolve(request);
};
export const saveSchedules = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/schedules'
    });
    return Promise.resolve(request);
};