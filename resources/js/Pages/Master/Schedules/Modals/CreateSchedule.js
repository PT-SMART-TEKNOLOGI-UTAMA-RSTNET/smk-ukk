import React from 'react';
import Select from 'react-select';
import moment from 'moment';
import TextField from '@mui/material/TextField';
import AdapterDateFns from '@mui/lab/AdapterDateFns';
import LocalizationProvider from '@mui/lab/LocalizationProvider';
import DateTimePicker from '@mui/lab/DateTimePicker';
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';

import {tingkatOptions} from "../../../../Components/KomponenOptions";
import {showErrorMessage, showSuccessMessage} from "../../../../Components/Alerts";
import {saveSchedules} from "../../../../Services/Master/ScheduleService";

export default class CreateSchedule extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'put', jurusan : null, judul_ujian : '', keterangan_ujian : '', tingkat : tingkatOptions[0],
                tanggal_mulai : moment().toDate(), tanggal_selesai : moment().toDate()
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.handleSelect = this.handleSelect.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.submitCreateSchedule = this.submitCreateSchedule.bind(this);
    }
    handleSelect(e,formName){
        let form = this.state.form;
        form[formName] = e;
        this.setState({form});
    }
    handleInput(e){
        let form = this.state.form;
        form[e.target.name] = e.target.value;
        this.setState({form});
    }
    async submitCreateSchedule(e){
        e.preventDefault();
        let button = this.state.button;
        button.submit.disabled = true;
        button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
        button.submit.text = 'Menyimpan';
        this.setState({button});
        try {
            let formData = new FormData();
            formData.append('_method','put');
            if (this.state.form.jurusan !== null) formData.append('jurusan', this.state.form.jurusan.value);
            formData.append('judul_ujian', this.state.form.judul_ujian);
            formData.append('keterangan_ujian', this.state.form.keterangan_ujian);
            formData.append('tanggal_mulai', moment(this.state.form.tanggal_mulai).format('yyyy-MM-DD hh:mm:ss'));
            formData.append('tanggal_selesai', moment(this.state.form.tanggal_selesai).format('yyyy-MM-DD hh:mm:ss'));
            formData.append('tingkat', this.state.form.tingkat.value);
            try {
                let response = await saveSchedules(this.props.token,formData);
                if (response.data.params === null) {
                    showErrorMessage(response.data.message,'modal-create');
                } else {
                    this.setState({form:{_method : 'put', jurusan : null, judul_ujian : '', keterangan_ujian : '',tanggal_mulai : moment().toDate(), tanggal_selesai : moment().toDate()}});
                    this.props.handleUpdate();
                    this.props.handleClose();
                    showSuccessMessage(response.data.message);
                }
            } catch (er) {
                showErrorMessage(er.response.data.message,'modal-create');
            }
        } catch (err) {
            showErrorMessage('Unable to create form','modal-create');
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
                onClose={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null)}
                scroll="body">
                <form onSubmit={this.submitCreateSchedule}>
                    <DialogTitle id="scroll-dialog-title">Form Tambah Jadwal Ujian</DialogTitle>
                    <DialogContent dividers={true}>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Jurusan</label>
                            <div className="col-md-4">
                                <Select onChange={(e)=>this.handleSelect(e,'jurusan')} value={this.state.form.jurusan} options={this.props.jurusan}/>
                            </div>
                            <label className="col-form-label col-md-2">Tingkat</label>
                            <div className="col-md-4">
                                <Select onChange={(e)=>this.handleSelect(e,'tingkat')} value={this.state.form.tingkat} options={tingkatOptions}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Judul Ujian</label>
                            <div className="col-md-10">
                                <input value={this.state.form.judul_ujian} onChange={this.handleInput} className="form-control" name="judul_ujian"/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-md-2 col-form-label">Keterangan Ujian</label>
                            <div className="col-md-10">
                                <textarea value={this.state.form.keterangan_ujian} onChange={this.handleInput} className="form-control" name="keterangan_ujian" style={{resize:'none'}}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Jadwal</label>
                            <div className="col-md-4">
                                <LocalizationProvider dateAdapter={AdapterDateFns}>
                                    <DateTimePicker
                                        renderInput={(props) => <TextField {...props} />}
                                        label="Tanggal Mulai"
                                        value={this.state.form.tanggal_mulai}
                                        onChange={(newValue) => {
                                            let form = this.state.form;
                                            form.tanggal_mulai = newValue;
                                            this.setState({form});
                                        }}
                                    />
                                </LocalizationProvider>
                            </div>
                            <div className="col-md-4">
                                <LocalizationProvider dateAdapter={AdapterDateFns}>
                                    <DateTimePicker
                                        renderInput={(props) => <TextField {...props} />}
                                        label="Tanggal Selesai"
                                        value={this.state.form.tanggal_selesai}
                                        onChange={(newValue) => {
                                            let form = this.state.form;
                                            form.tanggal_selesai = newValue;
                                            this.setState({form});
                                        }}
                                    />
                                </LocalizationProvider>
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