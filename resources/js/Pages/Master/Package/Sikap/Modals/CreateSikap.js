import React from 'react';
import DataTable from "react-data-table-component";
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';


import {saveKomponenSikap} from "../../../../../Services/Master/PackageService";
import {showErrorMessage, showSuccessMessage} from "../../../../../Components/Alerts";

export default class CreateSikap extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'put', paket : '', indikator_sikap : ''
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.submitCreateSikap = this.submitCreateSikap.bind(this);
        this.handleInput = this.handleInput.bind(this);
    }
    componentWillReceiveProps(props){
        if (props.data !== null) {
            let form = this.state.form;
            form.paket = props.data.value;
            this.setState({form});
        }
    }
    handleInput(e){
        let form = this.state.form;
        form[e.target.name] = e.target.value;
        this.setState({form});
    }
    async submitCreateSikap(e){
        e.preventDefault();
        let button = this.state.button;
        button.submit.disabled = true;
        button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
        button.submit.text = 'Menyimpan';
        this.setState({button});
        try {
            let response = await saveKomponenSikap(this.props.token,this.state.form);
            if (response.data.params === null) {
                showErrorMessage(response.data.message,'modal-create');
            } else {
                this.setState({form:{_method : 'put', paket : '', indikator_sikap : ''}});
                this.props.handleUpdate(response.data.params);
                this.props.handleClose();
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
                onClose={this.state.button.submit.disabled ? null : this.props.handleClose}
                scroll="body">
                <form onSubmit={this.submitCreateSikap}>
                    <DialogTitle id="scroll-dialog-title">Form Indikator Sikap</DialogTitle>
                    <DialogContent dividers={true}>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Indikator Sikap</label>
                            <div className="col-md-10">
                                <input name="indikator_sikap" className="form-control" value={this.state.form.indikator_sikap} onChange={this.handleInput}/>
                            </div>
                        </div>
                    </DialogContent>
                    <DialogActions>
                        <button disabled={this.state.button.submit.disabled} type="submit" className="btn btn-primary">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                        <button onClick={this.state.button.submit.disabled ? null : this.props.handleClose } disabled={this.state.button.submit.disabled} type="button" className="btn btn-secondary"><i className="fas fa-times"/> Tutup</button>
                    </DialogActions>
                </form>
            </Dialog>
        );
    }
}