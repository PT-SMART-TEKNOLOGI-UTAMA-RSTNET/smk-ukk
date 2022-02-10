import React from 'react';
import DataTable from "react-data-table-component";
import Select from 'react-select'
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';
import NumberFormat from 'react-number-format';


import {saveKomponenKeterampilan} from "../../../../../Services/Master/PackageService";
import {compactGrid} from '../../../../../Components/DataTableStyles';
import {showErrorMessage, showSuccessMessage} from "../../../../../Components/Alerts";
import {komponenOptions,pengujiTypeOptions} from "../../../../../Components/KomponenOptions";

export default class CreateKomponen extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'put', paket : '', komponen : null, sub_komponen : '', nomor : 0,
                indikator : [], penguji : null,
                nilai_sangat_baik : 0, nilai_baik : 0, nilai_cukup : 0, nilai_tidak : 0
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.submitCreateKomponen = this.submitCreateKomponen.bind(this);
        this.handleSelect = this.handleSelect.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.handleAddIndikator = this.handleAddIndikator.bind(this);
    }
    componentWillReceiveProps(props){
        if (props.data !== null) {
            let form = this.state.form;
            form.paket = props.data.value;
            form.nomor = props.data.meta.komponen.keterampilan.length + 1;
            form.komponen = null,
                form.nilai_tidak = 0, form.nilai_baik = 0,
                form.nilai_cukup = 0, form.nilai_sangat_baik = 0,
                form.sub_komponen = '',
                form.indikator = [],
                form.penguji = null;
            this.setState({form});
        }
    }
    handleAddIndikator(){
        let form = this.state.form;
        form.indikator.push({value:form.indikator.length,label:'',nomor:form.indikator.length+1});
        this.setState({form});
    }
    handleSelect(e,formName){
        let form = this.state.form;
        form[formName] = e;
        this.setState({form});
    }
    handleInput(e,data){
        let form = this.state.form;
        if (typeof data === 'undefined'){
            form[e.target.name] = e.target.value;
        } else {
            let indikatorIndex = form.indikator.findIndex((i) => i.value === data.value,0);
            if (indikatorIndex >= 0) {
                form.indikator[indikatorIndex].label = e.target.value;
            }
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
        try {
            let response = await saveKomponenKeterampilan(this.props.token,this.state.form);
            if (response.data.params === null) {
                showErrorMessage(response.data.message,'modal-create');
            } else {
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
        const columns = [
            { name : '#', cell : row => row.nomor, width : '50px', center : true },
            { name : 'Kriteria Unjuk Kerja / Indikator', cell : row => <input name="indikator" value={row.label} onChange={(e)=>this.handleInput(e,row)} className="form-control m-1 custom-control-sm"/> }
        ];
        return (
            <Dialog
                id="modal-create" fullWidth maxWidth="lg"
                open={this.props.open}
                onClose={this.state.button.submit.disabled ? null : this.props.handleClose}
                scroll="body">
                <form onSubmit={this.submitCreateKomponen}>
                    <DialogTitle id="scroll-dialog-title">Form Komponen Keterampilan</DialogTitle>
                    <DialogContent dividers={true}>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Komponen / Sub Komp.</label>
                            <div className="col-md-3">
                                <Select
                                    onChange={(e)=>this.handleSelect(e,'komponen')}
                                    value={this.state.form.komponen}
                                    options={komponenOptions}/>
                            </div>
                            <div className="col-md-7">
                                <input name="sub_komponen" value={this.state.form.sub_komponen} onChange={this.handleInput} className="form-control"/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-2">Diuji Oleh</label>
                            <div className="col-md-3">
                                <Select
                                    onChange={(e)=>this.handleSelect(e,'penguji')} value={this.state.form.penguji} options={pengujiTypeOptions}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-4">Jumlah indikator untuk nilai <b>Sangat Baik</b></label>
                            <div className="col-md-2">
                                <NumberFormat
                                    name="nilai_sangat_baik"
                                    className="form-control"
                                    isAllowed={({floatValue})=> floatValue <= this.state.form.indikator.length}
                                    onChange={this.handleInput}
                                    value={this.state.form.nilai_sangat_baik}
                                    displayType={'input'} thousandSeparator={true} decimalSeparator={"."} decimalScale={2}/>
                            </div>
                            <label className="col-form-label col-md-4">Jumlah indikator untuk nilai <b>Baik</b></label>
                            <div className="col-md-2">
                                <NumberFormat
                                    name="nilai_baik"
                                    className="form-control"
                                    isAllowed={({floatValue})=> floatValue <= this.state.form.indikator.length}
                                    onChange={this.handleInput}
                                    value={this.state.form.nilai_baik}
                                    displayType={'input'} thousandSeparator={true} decimalSeparator={"."} decimalScale={2}/>
                            </div>
                        </div>
                        <div className="form-group row">
                            <label className="col-form-label col-md-4">Jumlah indikator untuk nilai <b>Cukup</b></label>
                            <div className="col-md-2">
                                <NumberFormat
                                    name="nilai_cukup"
                                    className="form-control"
                                    isAllowed={({floatValue})=> floatValue <= this.state.form.indikator.length}
                                    onChange={this.handleInput}
                                    value={this.state.form.nilai_cukup}
                                    displayType={'input'} thousandSeparator={true} decimalSeparator={"."} decimalScale={2}/>
                            </div>
                            <label className="col-form-label col-md-4">Jumlah indikator untuk nilai <b>Tidak Lulus</b></label>
                            <div className="col-md-2">
                                <NumberFormat
                                    name="nilai_tidak"
                                    className="form-control"
                                    isAllowed={({floatValue})=> floatValue <= this.state.form.indikator.length}
                                    onChange={this.handleInput}
                                    value={this.state.form.nilai_tidak}
                                    displayType={'input'} thousandSeparator={true} decimalSeparator={"."} decimalScale={2}/>
                            </div>
                        </div>
                        <DataTable
                            customStyles={compactGrid}
                            dense={true} striped={true} columns={columns} data={this.state.form.indikator}
                            persistTableHead progressPending={this.state.loading}/>
                    </DialogContent>
                    <DialogActions>
                        <button onClick={this.handleAddIndikator} disabled={this.state.button.submit.disabled} type="button" className="btn btn-primary"><i className="fas fa-plus-circle"/> Tambah Indikator</button>
                        <button disabled={this.state.button.submit.disabled} type="submit" className="btn btn-primary">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                        <button onClick={this.state.button.submit.disabled ? null : this.props.handleClose } disabled={this.state.button.submit.disabled} type="button" className="btn btn-secondary"><i className="fas fa-times"/> Tutup</button>
                    </DialogActions>
                </form>
            </Dialog>
        );
    }
}