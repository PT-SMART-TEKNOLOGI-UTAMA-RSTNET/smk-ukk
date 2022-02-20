import React from 'react';
import Select from 'react-select'
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';

import {crudAsesor} from "../../../../Services/Master/AsessorService";
import {showErrorMessage, showSuccessMessage} from "../../../../Components/Alerts";
import {pengujiTypeOptions} from "../../../../Components/KomponenOptions";

export default class CreateAsessor extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'put', nama_asessor : '', jenis_asessor : pengujiTypeOptions[0], email_asessor : '',
                kata_sandi : '', kata_sandi_confirmation : ''
            },
            visibility : {
                kata_sandi : { icon : <i className="fas fa-eye"/>, type : 'password' },
                kata_sandi_confirmation : { icon : <i className="fas fa-eye"/>, type : 'password' },
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.handleSelect = this.handleSelect.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.submitCreateAsessor = this.submitCreateAsessor.bind(this);
        this.handleVisibility = this.handleVisibility.bind(this);
    }
    handleVisibility(what){
        let visibility = this.state.visibility;
        if (visibility[what].type === 'password') {
            visibility[what].type = 'text';
            visibility[what].icon = <i className="fas fa-eye-slash"/>;
        } else {
            visibility[what].type = 'password';
            visibility[what].icon = <i className="fas fa-eye"/>;
        }
        this.setState({visibility});
    }
    handleSelect(event){
        let form = this.state.form;
        form.jenis_asessor = event;
        this.setState({form});
    }
    handleInput(event){
        let form = this.state.form;
        form[event.target.name] = event.target.value;
        this.setState({form});
    }
    async submitCreateAsessor(e){
        e.preventDefault();
        let button = this.state.button;
        button.submit.disabled = true;
        button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
        button.submit.text = 'Menyimpan';
        this.setState({button});
        try {
            let response = await crudAsesor(this.props.token,this.state.form);
            if (response.data.params === null) {
                showErrorMessage(response.data.message,'modal-create');
            } else {
                this.props.handleUpdate(response.data.params);
                this.props.handleClose('create');
                this.setState({form:{ _method : 'put', nama_asessor : '', jenis_asessor : pengujiTypeOptions[0], email_asessor : '',
                        kata_sandi : '', kata_sandi_confirmation : ''}});
                showSuccessMessage(response.data.message);
            }
        } catch (er) {
            showErrorMessage(er.response.data.message,'modal-create');
        }
        button.submit.disabled = false;
        button.submit.icon = <i className="fas fa-save"/>;
        button.submit.text = 'Simpan';
        this.setState({button});
    }
    render(){
        return (
            <Dialog
                id="modal-create" fullWidth maxWidth="lg"
                open={this.props.open}
                onClose={()=>this.state.button.submit.disabled ? null : this.props.handleClose('create')}
                scroll="body">
                <form onSubmit={this.submitCreateAsessor}>
                    <DialogTitle id="scroll-dialog-title">Form Tambah Asessor</DialogTitle>
                    <DialogContent dividers={true}>
                        <div className="form-group row">
                            <label className="col-md-2 col-form-label">Nama Asessor</label>
                            <div className="col-md-10">
                                <input name="nama_asessor" value={this.state.form.nama_asessor} onChange={this.handleInput} className="form-control"/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-md-2 col-form-label">Jenis Asessor</label>
                            <div className="col-md-4">
                                <Select onChange={this.handleSelect} value={this.state.form.jenis_asessor} options={pengujiTypeOptions}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-md-2 col-form-label">Email Asessor</label>
                            <div className="col-md-4">
                                <input className="form-control" type="email" name="email_asessor" value={this.state.form.email_asessor} onChange={this.handleInput}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-md-2 col-form-label">Kata Sandi</label>
                            <div className="col-md-4">
                                <div className="input-group">
                                    <input className="form-control" type={this.state.visibility.kata_sandi.type} name="kata_sandi" value={this.state.form.kata_sandi} onChange={this.handleInput}/>
                                    <span className="input-group-append">
                                        <button onClick={()=>this.handleVisibility('kata_sandi')} type="button" className="btn btn-info btn-flat">{this.state.visibility.kata_sandi.icon}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-md-2 col-form-label">Konfirmasi Kata Sandi</label>
                            <div className="col-md-4">
                                <div className="input-group">
                                    <input className="form-control" type={this.state.visibility.kata_sandi_confirmation.type} name="kata_sandi_confirmation" value={this.state.form.kata_sandi_confirmation} onChange={this.handleInput}/>
                                    <span className="input-group-append">
                                        <button onClick={()=>this.handleVisibility('kata_sandi_confirmation')} type="button" className="btn btn-info btn-flat">{this.state.visibility.kata_sandi_confirmation.icon}</button>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </DialogContent>
                    <DialogActions>
                        <button disabled={this.state.button.submit.disabled} type="submit" className="btn btn-primary">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                        <button onClick={()=>this.state.button.submit.disabled ? null : this.props.handleClose('create') } disabled={this.state.button.submit.disabled} type="button" className="btn btn-secondary"><i className="fas fa-times"/> Tutup</button>
                    </DialogActions>
                </form>
            </Dialog>
        );
    }
}