import Axios from 'axios';

export const crudAsesor = async (token, data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/asessors'
    });
    return Promise.resolve(request);
};
export const syncErapor = async (token, data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/sync'
    });
    return Promise.resolve(request);
};