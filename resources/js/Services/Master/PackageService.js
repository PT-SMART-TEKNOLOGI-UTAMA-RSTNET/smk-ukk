import Axios from 'axios';

export const getPackages = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages'
    });
    return Promise.resolve(request);
};
export const savePackage = async (token,data) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token },
        method : 'post', data : data,
        url : window.origin + '/api/auth/master/packages'
    });
    return Promise.resolve(request);
}