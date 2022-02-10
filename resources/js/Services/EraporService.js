import Axios from 'axios';

export const getJurusan = async (token) => {
    let request = Axios({
        headers : { "Authorization" : "Bearer " + token },
        method : 'post',
        url : process.env.MIX_API_ERAPOR + '/public/master/jurusan'
    });
    return Promise.resolve(request);
};