import React from 'react';
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';

import {readFilePeserta} from "../../../../../Services/Master/ScheduleService";
import {showErrorMessage, showSuccessMessage} from "../../../../../Components/Alerts";

export default class ImportPeserta extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            file : null,
            form : {},
            button : {
                submit : { disabled : false, icon : <i className="fas fa-eye"/>, text : 'Baca File' }
            }
        };
        this.handleFileChange = this.handleFileChange.bind(this);
        this.handleReadFile = this.handleReadFile.bind(this);
    }
    handleFileChange(event){
        let file = event.target.files[0];
        this.setState({file});
    }
    async handleReadFile(){
        if (this.state.file === null) {
            showErrorMessage('Pilih file lebih dulu', 'modal-upload');
        } else {
            let button = this.state.button;
            button.submit.disabled = true;
            button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
            button.submit.text = 'Membaca File';
            this.setState({button});
            try {
                const formData = new FormData();
                formData.append('ujian', this.props.ujian.value);
                formData.append('file', this.state.file, this.state.file.name);
                try {
                    let response = await readFilePeserta(this.props.token, formData);
                    if (response.data.params === null) {
                        showErrorMessage(response.data.message, 'modal-upload');
                    } else {
                        this.setState({file:null});
                        this.props.handleClose('upload');
                        this.props.handleUpdate(response.data.params);
                        showSuccessMessage(response.data.message);
                    }
                } catch (error) {
                    showErrorMessage(error.response.data.message, 'modal-upload');
                }
            } catch (er) {
                showErrorMessage(er.toString(), 'modal-upload');
            }
            button.submit.disabled = false;
            button.submit.icon = <i className="fas fa-eye"/>;
            button.submit.text = 'Baca File';
            this.setState({button});
        }
    }
    render() {
        return (
            <Dialog
                id="modal-upload" fullWidth maxWidth="lg"
                open={this.props.open}
                onClose={()=>this.state.button.submit.disabled ? null : this.props.handleClose('upload')}
                scroll="body">
                <DialogTitle id="scroll-dialog-title">Form Import Peserta Ujian</DialogTitle>
                <DialogContent dividers={true}>
                    <div className="input-group">
                        <div className="custom-file">
                            <input onChange={this.handleFileChange} type="file" className="custom-file-input" id="exampleInputFile"/>
                            <label className="custom-file-label" htmlFor="exampleInputFile">
                                {this.state.file === null ? 'Pilih file excel' : this.state.file.name}
                            </label>
                        </div>
                    </div>
                </DialogContent>
                <DialogActions>
                    <button onClick={this.handleReadFile} disabled={this.state.button.submit.disabled} type="submit" className="btn btn-outline-primary btn-flat">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                    <button onClick={()=>this.state.button.submit.disabled ? null : this.props.handleClose('upload') } disabled={this.state.button.submit.disabled} type="button" className="btn btn-outline-secondary btn-flat"><i className="fas fa-times"/> Tutup</button>
                </DialogActions>
            </Dialog>
        );
    }
}