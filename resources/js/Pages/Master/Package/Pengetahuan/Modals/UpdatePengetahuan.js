import React from 'react';
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';
import Select from 'react-select'
import SunEditor,{buttonList} from 'suneditor-react';

import {saveKomponenPengetahuan} from "../../../../../Services/Master/PackageService";
import {showErrorMessage, showSuccessMessage} from "../../../../../Components/Alerts";
import {jenisSoalOptions} from "../../../../../Components/KomponenOptions";

export default class UpdatePengetahuan extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'patch', id : '', paket : '', isi_soal : '', jawaban : null,
                pilihan_jawaban : [], deleted_jawaban : [], jenis_soal : jenisSoalOptions[0],
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.submitCreateKomponen = this.submitCreateKomponen.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.handleSelect = this.handleSelect.bind(this);
        this.handleAddPilihan = this.handleAddPilihan.bind(this);
        this.handleDeletePilihan = this.handleDeletePilihan.bind(this);
        this.handleEditor = this.handleEditor.bind(this);
    }
    handleEditor(content){
        let form = this.state.form;
        form.isi_soal = content;
        this.setState({form});
    }
    componentWillReceiveProps(props){
        if (props.data !== null && props.komponen_data !== null) {
            let form = this.state.form;
            form.id = props.komponen_data.value,
            form.nomor = props.komponen_data.meta.nomor,
                form.paket = props.data.value,
                form.isi_soal = props.komponen_data.label,
                form.jenis_soal = jenisSoalOptions[jenisSoalOptions.findIndex((e) => e.value === props.komponen_data.meta.type)],
                form.deleted_jawaban = [],
                form.pilihan_jawaban = [];
            if (props.komponen_data.meta.indikator.length > 0) {
                props.komponen_data.meta.indikator.map((item,index)=>{
                    form.pilihan_jawaban.push({
                        value : item.value, label : item.label, nomor : item.meta.nomor, is_default : true,
                    });
                    if (item.value === props.komponen_data.meta.answer.id) {
                        form.jawaban = form.pilihan_jawaban[index];
                    }
                });
            }
            this.setState({form});
        }
    }
    handleAddPilihan(){
        let form = this.state.form;
        if (form.jenis_soal === null) {
            showErrorMessage('Pilih jenis soal lebih dulu','modal-update');
        } else {
            if (form.jenis_soal.value === 'essay' && form.pilihan_jawaban.length > 1){
                showErrorMessage('Pilihan jawaban untuk Jenis soal isian tidak boleh lebih dari 1 (satu)','modal-update');
            } else {
                form.pilihan_jawaban.push({
                    value : form.pilihan_jawaban.length + 1, label : '', nomor : form.pilihan_jawaban.length + 1, is_default : false
                });
                this.setState({form});
            }
        }
    }
    handleDeletePilihan(index){
        let form = this.state.form;
        if (form.jawaban !== null){
            if (form.jawaban.value === form.pilihan_jawaban[index].value){
                form.jawaban = null;
            }
        }
        if (form.pilihan_jawaban[index].is_default){
            form.deleted_jawaban.push(form.pilihan_jawaban[index]);
        }
        form.pilihan_jawaban.splice(index,1);
        this.setState({form});
    }
    handleSelect(e,formName){
        let form = this.state.form;
        form[formName] = e;
        this.setState({form});
    }
    handleInput(e,formName,index){
        let form = this.state.form;
        if (typeof index === 'undefined'){
            form[e.target.name] = e.target.value;
        } else {
            form.pilihan_jawaban[index].label = e.target.value;
        }
        this.setState({form});
    }
    async submitCreateKomponen(e){
        e.preventDefault();
        let button = this.state.button;
        button.submit.disabled = true;
        button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
        button.submit.text = 'Menyimpan';
        this.setState({button});
        let form = this.state.form;
        try {
            form.pilihan_jawaban.map((item,index)=>{
                if (! item.is_default){
                    form.pilihan_jawaban[index].value = null;
                }
            });
            this.setState({form});
            let response = await saveKomponenPengetahuan(this.props.token,form);
            if (response.data.params === null) {
                showErrorMessage(response.data.message,'modal-update');
                form.pilihan_jawaban.map((item,index)=>{
                    if (! item.is_default){
                        form.pilihan_jawaban[index].value = index;
                    }
                });
                this.setState({form});
            } else {
                form = {
                    _method : 'patch', paket : '', isi_soal : '', jawaban : null,
                    pilihan_jawaban : [], deleted_jawaban : [], jenis_soal : jenisSoalOptions[0],
                };
                this.setState({form});
                this.props.handleUpdate(response.data.params);
                this.props.handleClose();
                showSuccessMessage(response.data.message);
            }
        } catch (er) {
            form.pilihan_jawaban.map((item,index)=>{
                if (! item.is_default){
                    form.pilihan_jawaban[index].value = index;
                }
            });
            this.setState({form});
            showErrorMessage(er.response.data.message,'modal-update');
        }
        button.submit.disabled = false;
        button.submit.icon = <i className="fas fa-save"/>;
        button.submit.text = 'Simpan';
        this.setState({button});
    }
    render(){
        return (
            <Dialog
                id="modal-update" fullWidth maxWidth="lg"
                open={this.props.open}
                onClose={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null)}
                scroll="body">
                <form onSubmit={this.submitCreateKomponen}>
                    <DialogTitle id="scroll-dialog-title">Form Ubah Soal</DialogTitle>
                    <DialogContent dividers={true}>
                        <SunEditor
                            setOptions={{height:'200px',buttonList: buttonList.complex }}
                            defaultValue={this.state.form.isi_soal}
                            onChange={this.handleEditor}/>
                        <div className="mb-3"/>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Jenis Soal</label>
                            <div className="col-md-4">
                                <Select onChange={(e)=>this.handleSelect(e,'jenis_soal')} value={this.state.form.jenis_soal} options={jenisSoalOptions}/>
                            </div>
                            <label className="col-form-label col-md-2">Jawaban Soal</label>
                            <div className="col-md-4">
                                <Select onChange={(e)=>this.handleSelect(e,'jawaban')} value={this.state.form.jawaban} options={this.state.form.pilihan_jawaban}/>
                            </div>
                        </div>
                        <table className="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th width="50px">#</th>
                                <th>Isi Jawaban</th>
                                <th width="50px"> </th>
                            </tr>
                            </thead>
                            <tbody>
                            {this.state.form.pilihan_jawaban.map((item,index)=>
                                <tr key={index}>
                                    <td className="align-middle text-center">{item.nomor}</td>
                                    <td className="align-middle text-center">
                                        <input onChange={(e)=>this.handleInput(e,'label',index)} value={item.label} className="form-control"/>
                                    </td>
                                    <td className="align-middle text-center">
                                        <button type="button" onClick={()=>this.handleDeletePilihan(index)} className="btn btn-outline-danger btn-block"><i className="fas fa-trash-alt"/></button>
                                    </td>
                                </tr>
                            )}
                            </tbody>
                        </table>
                    </DialogContent>
                    <DialogActions>
                        <button onClick={this.handleAddPilihan} disabled={this.state.button.submit.disabled} type="button" className="btn btn-primary"><i className="fas fa-plus-circle"/> Tambah Indikator</button>
                        <button disabled={this.state.button.submit.disabled} type="submit" className="btn btn-primary">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                        <button onClick={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null)} disabled={this.state.button.submit.disabled} type="button" className="btn btn-secondary"><i className="fas fa-times"/> Tutup</button>
                    </DialogActions>
                </form>
            </Dialog>
        );
    }
}