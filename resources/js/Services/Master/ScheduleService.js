import Axios from 'axios';

export const getSchedules = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/schedules'
    });
    return Promise.resolve(request);
};
export const saveSchedules = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/schedules'
    });
    return Promise.resolve(request);
};
export const savePeserta = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/schedules/peserta'
    });
    return Promise.resolve(request);
};
export const saveCapaianKeterampilan = async (token,data) => {
    let request = Axios({
        headers: {"Authorization": "Bearer " + token, "Accept" : "application/json"},
        method: 'post', data: data,
        url: window.origin + '/api/auth/nilai'
    });
    return Promise.resolve(request);
};
export const getNilai = async (token,data) => {
    let request = Axios({
        headers: {"Authorization": "Bearer " + token, "Accept" : "application/json"},
        method: 'post', data: data,
        url: window.origin + '/api/auth/nilai'
    });
    return Promise.resolve(request);
};
export const readFilePeserta = async (token,data) => {
    let request = Axios({
        headers: {"Authorization": "Bearer " + token, "Accept" : "application/json"},
        method: 'post', data: data,
        url: window.origin + '/api/auth/master/schedules/peserta/import'
    });
    return Promise.resolve(request);
};
export const saveNilaiPengetahuan = async (token, data) => {
    let request = Axios({
        headers: {"Authorization": "Bearer " + token, "Accept" : "application/json"},
        method: 'post', data: data,
        url: window.origin + '/api/auth/nilai/pengetahuan/save'
    });
    return Promise.resolve(request);
};