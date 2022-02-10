import React from 'react';
import ReactDOM from 'react-dom';
import Swal from 'sweetalert2';
import {loginAttempt} from "../../Services/AuthService";

export default class LoginPage extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            progress : false,
            access_token : '',
            form : {
                nama_pengguna : '',
                kata_sandi : ''
            }
        };
        this.loginAttempt = this.loginAttempt.bind(this);
        this.handleInput = this.handleInput.bind(this);
    }
    handleInput(e){
        let form = this.state.form;
        form[e.target.name] = e.target.value;
        this.setState({form});
    }
    async loginAttempt(e){
        e.preventDefault();
        this.setState({progress:true});
        try {
            let response = await loginAttempt(this.state.form);
            if (response.data.params === null){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.data.message,
                });
            } else {
                localStorage.setItem('access_token',response.data.params);
                window.location.href = window.origin;
            }
            this.setState({progress:false});
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html : e.response.data.message.replaceAll("\n","<br>"),
            });
            this.setState({progress:false});
        }
    }
    render(){
        return (
            <form onSubmit={this.loginAttempt} className="login-html">
                <input id="tab-1" type="radio" name="tab" className="sign-in" checked={true} onChange={()=>null}/>
                <label htmlFor="tab-1" className="tab">Sign In</label>
                <input id="tab-2" type="radio" name="tab" className="sign-up" style={{display:'none'}} onChange={()=>null}/>
                <label htmlFor="tab-2" className="tab" style={{display:'none'}}>Sign Up</label>
                <div className="login-form">
                    <div className="sign-in-htm">
                        <div className="group">
                            <label htmlFor="user" className="label">Username</label>
                            <input name="nama_pengguna" onChange={this.handleInput} id="user" type="text" className="input" disabled={this.state.progress}/>
                        </div>
                        <div className="group">
                            <label htmlFor="pass" className="label">Password</label>
                            <input name="kata_sandi" onChange={this.handleInput} id="pass" type="password" className="input" data-type="password" disabled={this.state.progress}/>
                        </div>
                        <div className="group">
                            <button disabled={this.state.progress} className="button">Sign In</button>
                        </div>
                    </div>
                </div>
            </form>
        );
    }
}

if (document.getElementById('login-page')){
    ReactDOM.render(<LoginPage/>, document.getElementById('login-page'));
}