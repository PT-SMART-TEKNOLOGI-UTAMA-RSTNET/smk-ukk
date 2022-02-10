import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    iconColor: 'white',
    customClass: {
        popup: 'colored-toast'
    },
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});
export const showErrorMessage = (message,target=null) => {
    Toast.fire({icon:'error',html:message,target:document.getElementById(target)});
};
export const showSuccessMessage = (message,target=null) => {
    Toast.fire({icon:'success',html:message,target:document.getElementById(target)});
};
export const showInfoMessage = (message,target=null) => {
    Toast.fire({icon:'info',html:message,target:document.getElementById(target)});
};