import React from 'react';
import Select from 'react-select'
import {showErrorMessage, showSuccessMessage} from "../../../../Components/Alerts";
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';
import {savePackage} from "../../../../Services/Master/PackageService";

export default class UpdatePackage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'put', id : '', jurusan : null, judul_paket_soal : '', judul_paket_soal_inggris : '', keterangan : ''
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.handleSelect = this.handleSelect.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.submitUpdatePackage = this.submitUpdatePackage.bind(this);
    }
    componentWillReceiveProps(props){
        if (props.data !== null && props.jurusan !== null && props.jurusan.length > 0){
            let form = this.state.form;
            form.id = props.data.value;
            let jurusanIndex = props.jurusan.findIndex((e) => e.value === props.data.meta.jurusan.id,0);
            if (jurusanIndex >= 0) form.jurusan = props.jurusan[jurusanIndex];
            form.judul_paket_soal = props.data.label;
            form.judul_paket_soal_inggris = props.data.meta.eng;
            form.keterangan = props.data.meta.keterangan;
            this.setState({form});
        }
    }
    handleSelect(e){
        let form = this.state.form;
        form.jurusan = e;
        this.setState({form});
    }
    handleInput(e){
        let form = this.state.form;
        form[e.target.name] = e.target.value;
        this.setState({form});
    }
    async submitUpdatePackage(e){
        e.preventDefault();
        let button = this.state.button;
        button.submit.disabled = true;
        button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
        button.submit.text = 'Menyimpan';
        this.setState({button});
        try {
            let formData = new FormData();
            formData.append('_method','patch');
            formData.append('id', this.state.form.id);
            if (typeof this.state.form.jurusan.value !== 'undefined') formData.append('jurusan', this.state.form.jurusan.value);
            formData.append('judul_paket_soal',this.state.form.judul_paket_soal);
            formData.append('judul_paket_soal_inggris', this.state.form.judul_paket_soal_inggris);
            formData.append('keterangan', this.state.form.keterangan);
            try {
                let response = await savePackage(this.props.token, formData);
                if (response.data.params === null) {
                    showErrorMessage(response.data.message,'modal-create');
                } else {
                    this.props.handleClose();
                    this.props.handleUpdate();
                    this.setState({form : {_method : 'put', jurusan : null, judul_paket_soal : '', judul_paket_soal_inggris : '', keterangan : ''}});
                    showSuccessMessage(response.data.message);
                }
            } catch (er) {
                showErrorMessage(er.response.data.message,'modal-create');
            }
        } catch (error) {
            showErrorMessage('Unable to create form data','modal-create');
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
                onClose={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null) }
                scroll="body">
                <form onSubmit={this.submitUpdatePackage}>
                    <DialogTitle id="scroll-dialog-title">Form Ubah Paket Soal</DialogTitle>
                    <DialogContent dividers={true}>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Jurusan</label>
                            <div className="col-md-4">
                                <Select
                                    onChange={this.handleSelect}
                                    value={this.state.form.jurusan}
                                    options={this.props.jurusan}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Judul Paket</label>
                            <div className="col-md-10">
                                <input name="judul_paket_soal" value={this.state.form.judul_paket_soal} onChange={this.handleInput} className="form-control"/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Judul Paket (Inggris)</label>
                            <div className="col-md-10">
                                <input name="judul_paket_soal_inggris" value={this.state.form.judul_paket_soal_inggris} onChange={this.handleInput} className="form-control"/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Keterangan Paket</label>
                            <div className="col-md-10">
                                <textarea name="keterangan" value={this.state.form.keterangan} onChange={this.handleInput} className="form-control"/>
                            </div>
                        </div>
                    </DialogContent>
                    <DialogActions>
                        <button disabled={this.state.button.submit.disabled} type="submit" className="btn btn-primary">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                        <button onClick={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null) } disabled={this.state.button.submit.disabled} type="button" className="btn btn-secondary"><i className="fas fa-times"/> Tutup</button>
                    </DialogActions>
                </form>
            </Dialog>
        );
    }
}