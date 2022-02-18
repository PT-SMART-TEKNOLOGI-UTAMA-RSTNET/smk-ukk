import Axios from 'axios';

export const getPackages = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages'
    });
    return Promise.resolve(request);
};
export const savePackage = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages'
    });
    return Promise.resolve(request);
};
export const saveKomponenKeterampilan = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages/komponen/keterampilan'
    });
    return Promise.resolve(request);
};
export const saveKomponenPengetahuan = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages/komponen/pengetahuan'
    });
    return Promise.resolve(request);
};
export const saveKomponenSikap = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages/komponen/sikap'
    });
    return Promise.resolve(request);
};