import Axios from 'axios';

export const getJurusan = async (token) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token, "Accept" : "application/json" },
        method : 'post',
        url : process.env.MIX_API_ERAPOR + '/public/master/jurusan'
    });
    return Promise.resolve(request);
};