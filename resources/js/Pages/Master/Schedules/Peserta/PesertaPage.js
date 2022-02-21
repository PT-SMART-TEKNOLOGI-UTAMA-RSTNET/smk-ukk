import React from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select'

import {showErrorMessage, showSuccessMessage} from "../../../../Components/Alerts";
import MainHeader from "../../../../Components/Layouts/MainHeader";
import MainSideBar from "../../../../Components/Layouts/MainSideBar";
import MainFooter from "../../../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../../../Components/Layouts/BreadCrumbs";

import {currentUser,allUsers} from "../../../../Services/AuthService";
import {savePeserta,getSchedules} from "../../../../Services/Master/ScheduleService";
import {getPackages} from "../../../../Services/Master/PackageService";

export default class PesertaPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null, siswa : [], paket_soal : [], penguji : [],
            breadcrumbs : [
                { label : 'Jadwal Ujian', url : window.origin + '/master/schedules' }
            ],
            loading : { penguji : true, peserta : true, siswa : true, paket : true, ujian : true, current_user : true },
            form : {
                _method : 'put', jadwal : '', peserta : [], deleted_peserta : [],
            },
            current_schedule : null,
            modals : {
                create : { open : false, data : null },
                update : { open : false, data : null, peserta_data : null }
            },
            button : {
                submit : { disabled : false, icon : <i className="fas fa-save"/>, text : 'Simpan' }
            }
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadSchedules = this.loadSchedules.bind(this);
        this.loadSiswa = this.loadSiswa.bind(this);
        this.loadPenguji = this.loadPenguji.bind(this);
        this.loadPaketSoal = this.loadPaketSoal.bind(this);
        this.populateForm = this.populateForm.bind(this);
        this.handleAddPeserta = this.handleAddPeserta.bind(this);
        this.handleSelect = this.handleSelect.bind(this);
        this.handleDeletePeserta = this.handleDeletePeserta.bind(this);
        this.submitSavePeserta = this.submitSavePeserta.bind(this);
        this.handleInput = this.handleInput.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    handleInput(event,index){
        let form = this.state.form;
        form.peserta[index].nopes = event.target.value;
        this.setState({form});
    }
    handleSelect(e,formName,index){
        let form = this.state.form;
        form.peserta[index][formName] = e;
        if (formName === 'siswa'){
            form.peserta[index].nis = e.meta.nis;
            form.peserta[index].nopes = e.meta.nopes === null ? '' : e.meta.nopes;
            form.peserta[index].rombel = e.meta.rombel;
        }
        this.setState({form});
    }
    handleDeletePeserta(index){
        let form = this.state.form;
        if (form.peserta[index].is_default){
            form.deleted_peserta.push(form.peserta[index]);
        }
        form.peserta.splice(index,1);
        this.setState({form});
    }
    handleAddPeserta(){
        let form = this.state.form;
        form.peserta.push({
            index : form.peserta.length, value : null, nis : null, nopes : '', rombel : null, penguji_internal : null, penguji_external : null, paket_soal : null,
            siswa : null, is_default : false
        });
        this.setState({form});
    }
    populateForm(){
        let loading = this.state.loading;
        loading.peserta = true;
        this.setState({loading});
        let form = this.state.form;
        form.peserta = [];
        let peserta = this.state.current_schedule.meta.peserta;
        peserta.map((item,index)=>{
            let pengujiInternal = null, pengujiExternal = null, paketSoal = null, siswa = null;
            let pengujiInternalIndex = this.state.penguji.findIndex((e) => e.value === item.meta.penguji.internal.value,0);
            let pengujiExternalIndex = this.state.penguji.findIndex((e) => e.value === item.meta.penguji.external.value,0);
            let paketSoalIndex = this.state.paket_soal.findIndex((e) => e.value === item.meta.paket.value,0);
            let siswaIndex = this.state.siswa.findIndex((e) => e.value === item.meta.user.value,0);
            if (pengujiInternalIndex >= 0) pengujiInternal = this.state.penguji[pengujiInternalIndex];
            if (pengujiExternalIndex >= 0) pengujiExternal = this.state.penguji[pengujiExternalIndex];
            if (paketSoalIndex >= 0) paketSoal = this.state.paket_soal[paketSoalIndex];
            if (siswaIndex >= 0) siswa = this.state.siswa[siswaIndex];
            form.peserta.push({
                index : index, value : item.value,
                nis : item.meta.user.meta.kode.nis,
                nopes : item.meta.nopes,
                rombel : item.meta.user.meta.rombel,
                penguji_internal : pengujiInternal, penguji_external : pengujiExternal, paket_soal : paketSoal, siswa : siswa, is_default : true,
            });
        });
        loading.peserta = false;
        this.setState({loading,form});
    }
    async submitSavePeserta(e){
        e.preventDefault();
        let seen = new Set(), seenNopes = new Set(), namanya = [];
        let hasDuplicate = this.state.form.peserta.some((i)=>{
            return seen.size === seen.add(i.siswa).size;
        });
        let duplicateNopes = this.state.form.peserta.some((i)=>{
            return seenNopes.size === seenNopes.add(i.nopes).size;
        });
        if (hasDuplicate) {
            seen = Array.from(seen);
            seen.map((item) => namanya.push('<b>' + item.label + '</b>'));
            showErrorMessage('Duplikat peserta terdeteksi atas nama ' + namanya.join(', '));
        } else if (duplicateNopes) {
            seenNopes = Array.from(seenNopes);
            seenNopes.map((item) => namanya.push('<b>' + item + '</b>'));
            showErrorMessage('Duplikat nomor peserta terdeteksi dengan nomor ' + namanya.join(', '));
        } else {
            let button = this.state.button;
            button.submit.disabled = true;
            button.submit.icon = <i className="fas fa-spin fa-circle-notch"/>;
            button.submit.text = 'Menyimpan';
            this.setState({button});
            try {
                let response = await savePeserta(this.state.token,this.state.form);
                if (response.data.params === null){
                    showErrorMessage(response.data.message);
                } else {
                    showSuccessMessage(response.data.message);
                    this.loadSchedules();
                }
            } catch (er) {
                if (er.response.status === 401){
                    window.location.href = window.origin + '/logout';
                    er.response.data.message = 'Waktu sesi telah habis';
                }
                showErrorMessage(er.response.data.message);
            }
            button.submit.disabled = false;
            button.submit.icon = <i className="fas fa-save"/>;
            button.submit.text = 'Simpan';
            this.setState({button});
        }
    }
    async loadSchedules(){
        let current_url = window.location.href;
        current_url = current_url.split('/');
        if (current_url.length > 5){
            if (typeof current_url[5] !== 'undefined') {
                current_url = current_url[5];
                let loading = this.state.loading;
                loading.ujian = true,loading.peserta = true, loading.paket = true, loading.siswa = true, loading.penguji = true;
                this.setState({loading,current_schedule:null});
                try {
                    let response = await getSchedules(this.state.token,{id:current_url});
                    if (response.data.params === null) {
                        showErrorMessage(response.data.message);
                    } else if (response.data.params.length === 0) {
                        showErrorMessage('Data tidak ditemukan');
                    } else {
                        let current_schedule = response.data.params[0];
                        let peserta = current_schedule.meta.peserta;
                        let form = this.state.form;
                        form.jadwal = current_schedule.value;
                        form.peserta = [];
                        this.setState({current_schedule,form});
                    }
                } catch (e) {
                    if (e.response.status === 401){
                        window.location.href = window.origin + '/logout';
                        e.response.data.message = 'Waktu sesi telah habis';
                    }
                    showErrorMessage(e.response.data.message);
                }
                loading.ujian = false;
                this.setState({loading});
            }
        }
        this.loadSiswa();
    }
    async loadPaketSoal(){
        let loading = this.state.loading;
        loading.paket = true;
        this.setState({loading,paket_soal:[]});
        try {
            let response = await getPackages(this.state.token,{jurusan:this.state.current_schedule.meta.jurusan.value});
            if (response.data.params === null){
                showErrorMessage(response.data.message);
            } else {
                let paket_soal = response.data.params;
                this.setState({paket_soal});
            }
        } catch (e) {
            if (e.response.status === 401){
                window.location.href = window.origin + '/logout';
                e.response.data.message = 'Waktu sesi telah habis';
            }
            showErrorMessage(e.response.data.message);
        }
        loading.paket = false;
        this.setState({loading});
        this.populateForm();
    }
    async loadPenguji(){
        let loading = this.state.loading;
        loading.penguji = true;
        this.setState({loading,penguji:[]});
        try {
            let response = await allUsers(this.state.token,{type:'guru'});
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let penguji = response.data.params;
                this.setState({penguji});
            }
        } catch (e) {
            if (e.response.status === 401){
                window.location.href = window.origin + '/logout';
                e.response.data.message = 'Waktu sesi telah habis';
            }
            showErrorMessage(e.response.data.message);
        }
        loading.penguji = false;
        this.setState({loading});
        this.loadPaketSoal();
    }
    async loadSiswa(){
        let loading = this.state.loading;
        loading.siswa = true;
        this.setState({loading,siswa:[]});
        try {
            let response = await allUsers(this.state.token,{type:'siswa',jurusan:this.state.current_schedule.meta.jurusan.value});
            if (response.data.params === null){
                showErrorMessage(response.data.message);
            } else {
                let siswa = response.data.params.filter((e) => e.meta.tingkat === this.state.current_schedule.meta.tingkat);
                this.setState({siswa});
            }
        } catch (e) {
            if (e.response.status === 401){
                window.location.href = window.origin + '/logout';
                e.response.data.message = 'Waktu sesi telah habis';
            }
            showErrorMessage(e.response.data.message);
        }
        loading.siswa = false;
        this.setState({loading});
        this.loadPenguji();
    }
    async loadMe(){
        let loading = this.state.loading;
        loading.current_user = true;
        this.setState({loading,current_user:null});
        try {
            let response = await currentUser(this.state.token);
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let current_user = response.data.params;
                this.setState({current_user});
            }
        } catch (e) {
            if (e.response.status === 401){
                window.location.href = window.origin + '/logout';
                e.response.data.message = 'Waktu sesi telah habis';
            }
            showErrorMessage(e.response.data.message);
        }
        loading.current_user = false;
        this.setState({loading});
        this.loadSchedules();
    }
    render(){
        return (
            <>
                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title={this.state.current_schedule === null ? 'Peserta' : this.state.current_schedule.label } breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <form onSubmit={this.submitSavePeserta} className="card">
                            <div className="card-header">
                                <h3 className="card-title">
                                    Jumlah Peserta = {this.state.form.peserta.filter((e) => e.user !== null && e.paket !== null && e.internal !== null && e.external !== null ).length}
                                </h3>
                                <div className="card-tools">
                                    <button type="button" disabled={this.state.button.submit.disabled || this.state.loading.peserta} onClick={this.submitSavePeserta} className="btn btn-outline-success btn-flat mr-1">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                                    <button type="button" disabled={this.state.loading.paket || this.state.loading.penguji || this.state.loading.siswa || this.state.button.submit.disabled} onClick={this.handleAddPeserta} className="btn btn-outline-primary btn-flat mr-1" title="Tambah Peserta"><i className="fas fa-plus-circle"/> Tambah Peserta</button>
                                    <button title="Reload Data Siswa" type="button" disabled={this.state.loading.siswa || this.state.button.submit.disabled} onClick={this.loadSiswa} className="btn btn-outline-secondary btn-flat mr-1">{this.state.loading.siswa ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                    <button title="Reload Data Paket Soal" type="button" disabled={this.state.loading.paket || this.state.button.submit.disabled} onClick={this.loadPaketSoal} className="btn btn-outline-secondary btn-flat mr-1">{this.state.loading.paket ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                    <button title="Reload Data Penguji" type="button" disabled={this.state.loading.penguji || this.state.button.submit.disabled} onClick={this.loadPenguji} className="btn btn-outline-secondary btn-flat mr-1">{this.state.loading.penguji ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                    <button title="Reload Data Peserta" type="button" disabled={this.state.loading.peserta || this.state.button.submit.disabled} onClick={this.loadSchedules} className="btn btn-outline-secondary btn-flat">{this.state.loading.peserta ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                </div>
                            </div>
                            <div className="card-body p-0">
                                <table className="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="100px" className="align-middle text-center text-xs" rowSpan={2}>NIS</th>
                                        <th width="120px" className="align-middle text-center text-xs" rowSpan={2}>No. Pes</th>
                                        <th className="align-middle text-center text-xs" rowSpan={2}>Nama Peserta</th>
                                        <th className="align-middle text-center text-xs" rowSpan={2}>Paket Soal</th>
                                        <th className="text-center text-xs" colSpan={2}>Penguji</th>
                                        <th width="50px" className="align-middle text-center text-xs" rowSpan={2}> </th>
                                    </tr>
                                    <tr>
                                        <th className="text-center text-xs">Internal</th>
                                        <th className="text-center text-xs">External</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {this.state.loading.peserta ?
                                        <tr><td colSpan={7} className="align-middle text-center"><i className="fas fa-spin fa-circle-notch"/><br/>Memuat data ...</td></tr> :
                                        this.state.form.peserta.length === 0 ?
                                            <tr><td colSpan={7} className="align-middle text-center text-warning"><i className="fas fa-exclamation-triangle"/><br/>Data tidak ditemukan</td></tr>
                                            :
                                            this.state.form.peserta.map((item,index)=>
                                            <tr key={index}>
                                                <td className="align-middle text-center text-xs">{item.nis}</td>
                                                <td className="align-middle text-center text-xs">
                                                    <input onChange={(e)=>this.handleInput(e,index)} name="nopes" value={item.nopes} className="form-control text-sm" placeholder="Kosongkan jika otomatis"/>
                                                </td>
                                                <td className="align-middle text-xs">
                                                    <Select onChange={(e)=>this.handleSelect(e,'siswa',index)} value={item.siswa} options={this.state.siswa}/>
                                                </td>
                                                <td className="align-middle text-xs">
                                                    <Select onChange={(e)=>this.handleSelect(e,'paket_soal',index)} value={item.paket_soal} options={this.state.paket_soal}/>
                                                </td>
                                                <td className="align-middle text-xs">
                                                    <Select onChange={(e)=>this.handleSelect(e,'penguji_internal',index)} value={item.penguji_internal} options={this.state.penguji.filter((e) => e.meta.penguji === 'internal')} />
                                                </td>
                                                <td className="align-middle text-xs">
                                                    <Select onChange={(e)=>this.handleSelect(e,'penguji_external',index)} value={item.penguji_external} options={this.state.penguji.filter((e) => e.meta.penguji === 'external')} />
                                                </td>
                                                <td className="align-middle text-center text-xs">
                                                    <button onClick={()=>this.handleDeletePeserta(index)} type="button" className="btn btn-block btn-danger"><i className="fas fa-trash-alt"/></button>
                                                </td>
                                            </tr>
                                    )}
                                    </tbody>
                                </table>
                            </div>
                            <div className="card-footer text-right pr-2">
                                <button type="button" disabled={this.state.button.submit.disabled || this.state.loading.peserta} onClick={this.submitSavePeserta} className="btn btn-outline-success btn-flat mr-1">{this.state.button.submit.icon} {this.state.button.submit.text}</button>
                                <button type="button" disabled={this.state.loading.paket || this.state.loading.penguji || this.state.loading.siswa || this.state.button.submit.disabled} onClick={this.handleAddPeserta} className="btn btn-outline-primary btn-flat mr-1" title="Tambah Peserta"><i className="fas fa-plus-circle"/> Tambah Peserta</button>
                                <button title="Reload Data Siswa" type="button" disabled={this.state.loading.siswa || this.state.button.submit.disabled} onClick={this.loadSiswa} className="btn btn-outline-secondary btn-flat mr-1">{this.state.loading.siswa ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                <button title="Reload Data Paket Soal" type="button" disabled={this.state.loading.paket || this.state.button.submit.disabled} onClick={this.loadPaketSoal} className="btn btn-outline-secondary btn-flat mr-1">{this.state.loading.paket ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                <button title="Reload Data Penguji" type="button" disabled={this.state.loading.penguji || this.state.button.submit.disabled} onClick={this.loadPenguji} className="btn btn-outline-secondary btn-flat mr-1">{this.state.loading.penguji ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                <button title="Reload Data Peserta" type="button" disabled={this.state.loading.peserta || this.state.button.submit.disabled} onClick={this.loadSchedules} className="btn btn-outline-secondary btn-flat">{this.state.loading.peserta ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                            </div>
                        </form>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('peserta-page')){
    ReactDOM.render(<PesertaPage/>, document.getElementById('peserta-page'));
}