import React from 'react';
import DataTable from "react-data-table-component";
import Select from 'react-select'
import {Dialog,DialogTitle,DialogContent,DialogActions} from '@material-ui/core';
import NumberFormat from 'react-number-format';


import {saveKomponenKeterampilan} from "../../../../../Services/Master/PackageService";
import {compactGrid} from '../../../../../Components/DataTableStyles';
import {showErrorMessage, showSuccessMessage} from "../../../../../Components/Alerts";
import {komponenOptions,pengujiTypeOptions} from "../../../../../Components/KomponenOptions";

export default class UpdateKomponen extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            form : {
                _method : 'patch', paket : '', komponen : null, sub_komponen : '', nomor : 0,
                indikator : [], penguji : null, indikator_deleted : [],
                nilai_sangat_baik : 0, nilai_baik : 0, nilai_cukup : 0, nilai_tidak : 0
            },
            button : {
                submit : {
                    disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan'
                }
            }
        };
        this.submitUpdateKomponen = this.submitUpdateKomponen.bind(this);
        this.handleSelect = this.handleSelect.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.handleAddIndikator = this.handleAddIndikator.bind(this);
        this.handleDeleteIndikator = this.handleDeleteIndikator.bind(this);
    }
    componentWillReceiveProps(props){
        if (props.data !== null && props.komponen_data !== null) {
            let form = this.state.form;
            form.id = props.komponen_data.value,
                form.paket = props.data.value,
                form.nomor = props.komponen_data.meta.nomor,
                form.komponen = komponenOptions[komponenOptions.findIndex((i) => i.value === props.komponen_data.meta.komponen)],
                form.nilai_tidak = props.komponen_data.meta.nilai.t, form.nilai_baik = props.komponen_data.meta.nilai.b,
                form.nilai_cukup = props.komponen_data.meta.nilai.c, form.nilai_sangat_baik = props.komponen_data.meta.nilai.sb,
                form.sub_komponen = props.komponen_data.label,
                form.indikator = [], form.indikator_deleted = [],
                form.penguji = pengujiTypeOptions[pengujiTypeOptions.findIndex((i) => i.value === props.komponen_data.meta.type)];
            props.komponen_data.meta.indikator.map((item,index)=>{
                form.indikator.push({
                    value : item.id, label : item.indikator, nomor : item.nomor, index : index, is_default : true,
                });
            });
            this.setState({form});
        }
    }
    handleDeleteIndikator(data){
        let form = this.state.form;
        form.indikator.splice(data.index,1);
        if (data.is_default) {
            form.indikator_deleted.push(data);
        }
        if (form.indikator.length > 0){
            form.indikator.map((item,index)=>{
                form.indikator[index].index = index;
                form.indikator[index].nomor = index + 1;
            });
        }
        form.nilai_sangat_baik = form.indikator.length;
        if (form.nilai_sangat_baik > 1) form.nilai_baik = form.nilai_sangat_baik - 1;
        if (form.nilai_baik > 1) form.nilai_cukup = 1;
        this.setState({form});
    }
    handleAddIndikator(){
        let form = this.state.form;
        form.indikator.push({index:form.indikator.length,value:null,label:'',nomor:form.indikator.length+1,is_default:false});
        form.nilai_sangat_baik = form.indikator.length;
        if (form.nilai_sangat_baik > 1) form.nilai_baik = form.nilai_sangat_baik - 1;
        if (form.nilai_baik > 1) form.nilai_cukup = 1;
        this.setState({form});
    }
    handleSelect(e,formName){
        let form = this.state.form;
        form[formName] = e;
        if (typeof form.komponen.value !== 'undefined'){
            let sorted = [];
            switch (form.komponen.value){
                case 'persiapan' :
                    sorted = this.props.data.meta.komponen.keterampilan.filter((a) => a.meta.komponen === 'persiapan');
                    break;
                case 'pelaksanaan' :
                    sorted = this.props.data.meta.komponen.keterampilan.filter((a) => a.meta.komponen === 'pelaksanaan');
                    break;
                case 'hasil' :
                    sorted = this.props.data.meta.komponen.keterampilan.filter((a) => a.meta.komponen === 'hasil');
                    break;
            }
            form.nomor = sorted.length + 1;
        }
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
    async submitUpdateKomponen(e){
        e.preventDefault();
        let button = this.state.button;
        button.submit.disabled = true;
        button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
        button.submit.text = 'Menyimpan';
        this.setState({button});
        try {
            let response = await saveKomponenKeterampilan(this.props.token,this.state.form);
            if (response.data.params === null) {
                showErrorMessage(response.data.message,'modal-update');
            } else {
                this.props.handleUpdate(response.data.params);
                this.props.handleClose();
                showSuccessMessage(response.data.message);
            }
        } catch (er) {
            showErrorMessage(er.response.data.message,'modal-update');
        }
        button.submit.disabled = false;
        button.submit.icon = <i className="fas fa-save"/>;
        button.submit.text = 'Simpan';
        this.setState({button});
    }
    render(){
        const columns = [
            { name : '#', cell : row => row.nomor, width : '50px', center : true },
            { name : 'Kriteria Unjuk Kerja / Indikator', cell : row => <input name="indikator" value={row.label} onChange={(e)=>this.handleInput(e,row)} className="form-control m-1 custom-control-sm"/> },
            { name : '', cell : row => <button onClick={()=>this.handleDeleteIndikator(row)} className="btn btn-xs btn-block btn-danger" type="button"><i className="fas fa-trash-alt"/></button>, center : true, grow : 0 },
        ];
        return (
            <Dialog
                id="modal-update" fullWidth maxWidth="lg"
                open={this.props.open}
                onClose={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null)}
                scroll="body">
                <form onSubmit={this.submitUpdateKomponen}>
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
                        <button onClick={()=>this.state.button.submit.disabled ? null : this.props.handleClose(null) } disabled={this.state.button.submit.disabled} type="button" className="btn btn-secondary"><i className="fas fa-times"/> Tutup</button>
                    </DialogActions>
                </form>
            </Dialog>
        );
    }
}