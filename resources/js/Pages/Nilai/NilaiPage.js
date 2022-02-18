import React from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';
import DataTable from "react-data-table-component";
import moment from 'moment';

import {compactGrid} from '../../Components/DataTableStyles';
import MainHeader from "../../Components/Layouts/MainHeader";
import MainSideBar from "../../Components/Layouts/MainSideBar";
import {currentUser} from "../../Services/AuthService";
import {showErrorMessage,showSuccessMessage} from "../../Components/Alerts";
import MainFooter from "../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../Components/Layouts/BreadCrumbs";

import {getSchedules,getNilai} from "../../Services/Master/ScheduleService";
import DetailNilaiTable from "./DetailNilaiTable";
import IframePrint from "../../Components/IframePrint";

export default class NilaiPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null,
            breadcrumbs : [
                { label : 'Paket Soal', url : window.origin + '/master/schedules' }
            ],
            modals : {
                printing : { open : false, data : null }
            },
            loading : false,
            current_ujian : null, jadwal_ujian : [], peserta : []
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadPeserta = this.loadPeserta.bind(this);
        this.hitungTotalNilai = this.hitungTotalNilai.bind(this);
        this.togglePrinting = this.togglePrinting.bind(this);
        this.handlePrinting = this.handlePrinting.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    handlePrinting(){
        const iframe = document.getElementById('iframe-print');
        const iframeWindow = iframe.contentWindow || iframe;
        iframe.focus();
        iframeWindow.print();
    }
    togglePrinting(data = null){
        let modals = this.state.modals;
        modals.printing.open = ! this.state.modals.printing.open;
        modals.printing.data = data;
        this.setState({modals});
    }
    async loadPeserta(){
        this.setState({loading:true,peserta:[]});
        try {
            let formData = new FormData();
            formData.append('_method','post');
            formData.append('ujian', this.state.current_ujian.value);
            let response = await getNilai(this.state.token,formData);
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let peserta = response.data.params;
                this.setState({peserta});
            }
        } catch (er) {
            showErrorMessage(er.response.data.message);
        }
        this.setState({loading:false});
    }
    async loadUjian(){
        this.setState({loading:true});
        try {
            let current_url = window.location.href;
            current_url = current_url.split('/');
            if (current_url.length >= 4) {
                if (typeof current_url[current_url.length - 1] !== 'undefined') {
                    current_url = current_url[current_url.length - 1];
                    try {
                        let response = await getSchedules(this.state.token,{id:current_url});
                        if (response.data.params === null) {
                            showErrorMessage(response.data.message);
                        } else if (response.data.params.length === 0) {
                            showErrorMessage('Unable to get schedule');
                        } else {
                            let current_ujian = response.data.params[0];
                            window.history.pushState({},"", window.origin + '/nilai/' + current_ujian.value);
                            this.setState({current_ujian});
                        }
                    } catch (err) {
                        showErrorMessage(err.response.data.message);
                    }
                }
            }
        } catch (er) {
            showErrorMessage('Unable to get schedules');
        }
        this.setState({loading:false});
        this.loadPeserta();
    }
    async loadMe(){
        this.setState({loading:true,current_user:null});
        try {
            let response = await currentUser(this.state.token);
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let current_user = response.data.params;
                this.setState({current_user});
            }
        } catch (error) {
            if (error.response.status === 401){
                window.location.href = window.origin + '/logout';
            }
            showErrorMessage(error.response.data.message);
        }
        this.setState({loading:false});
        this.loadUjian();
    }
    hitungTotalNilai(data){
        let nilaiPengetahuan = data.meta.nilai[1].value.nilai.konversi;
        nilaiPengetahuan = ( nilaiPengetahuan * 30 ) / 100;
        let nilaiKeterampilan = data.meta.nilai[0].value.nilai.konversi;
        nilaiKeterampilan = ( nilaiKeterampilan * 70) / 100;
        let nilaiAkhir = nilaiPengetahuan + nilaiKeterampilan;
        nilaiAkhir = Math.round(nilaiAkhir);
        return nilaiAkhir;
    }
    render(){
        const detailNilai = ({data}) => <DetailNilaiTable data={data.meta.nilai}/>;
        const columns = [
            { name : 'No. Pes', width : '110px', selector : row => row.meta.nopes, center : true },
            { name : 'Nama Peserta', selector : row => row.label },
            { name : 'P', selector : row => row.meta.nilai[0].value.nilai.konversi, center : true, width : '50px' },
            { name : 'K', selector : row => row.meta.nilai[1].value.nilai.konversi, center : true, width : '50px' },
            { name : 'S', selector : row => row.meta.nilai[0].value.nilai.konversi, center : true, width : '50px' },
            { name : 'NA', width : '70px', center : true, selector : row => this.hitungTotalNilai(row) },
            { name : '', width : '70px', center : true, grow : 0,
                cell : row => <button onClick={()=>this.togglePrinting(window.origin+'/nilai/cetak/' + row.value )} disabled={this.state.loading} className="btn btn-flat btn-outline-primary btn-block btn-sm"><i className="fas fa-print"/></button>
            }
        ];
        return (
            <>
                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Nilai Ujian" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <div className="card">
                            <div className="card-header">
                                <div className="card-tools">
                                    {this.state.modals.printing.open ?
                                        <>
                                            <button onClick={this.handlePrinting} disabled={this.state.loading} className="btn btn-outline-primary btn-flat mr-1"><i className="fas fa-print"/> Cetak Dokumen</button>
                                            <button onClick={this.togglePrinting} disabled={this.state.loading} className="btn btn-outline-warning btn-flat"><i className="fas fa-xmark"/> Tutup Cetak</button>
                                        </>
                                        :
                                        <button disabled={this.state.loading} onClick={this.loadPeserta} className="btn btn-outline-secondary btn-flat">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                    }
                                </div>
                            </div>
                            {this.state.modals.printing.open ?
                                <IframePrint print_url={this.state.modals.printing.data}/>
                                :
                                <DataTable
                                    customStyles={compactGrid}
                                    dense={true} striped={true} columns={columns} data={this.state.peserta}
                                    expandableRows expandableRowsComponent={detailNilai}
                                    persistTableHead progressPending={this.state.loading}/>
                            }
                        </div>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('nilai-page')){
    ReactDOM.render(<NilaiPage/>, document.getElementById('nilai-page'));
}