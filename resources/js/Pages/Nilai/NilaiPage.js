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

export default class NilaiPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null,
            breadcrumbs : [
                { label : 'Paket Soal', url : window.origin + '/master/schedules' }
            ],
            loading : false,
            current_ujian : null, jadwal_ujian : [], peserta : []
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadPeserta = this.loadPeserta.bind(this);
    }
    componentDidMount(){
        this.loadMe();
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
    render(){
        const detailNilai = ({data}) => <DetailNilaiTable data={data.meta.nilai}/>;
        const columns = [
            { name : 'No. Pes', width : '110px', selector : row => row.meta.nopes, center : true },
            { name : 'Nama Peserta', selector : row => row.label },
            { name : 'P', selector : row => row.meta.nilai[0].value.nilai.konversi, center : true, width : '50px' },
            { name : 'K', selector : row => row.meta.nilai[1].value.nilai.konversi, center : true, width : '50px' },
            { name : 'S', selector : row => row.meta.nilai[0].value.nilai.konversi, center : true, width : '50px' },
            { name : 'NA', width : '70px', center : true,
                selector : row => Math.round((row.meta.nilai[0].value.nilai.konversi + row.meta.nilai[1].value.nilai.konversi + row.meta.nilai[0].value.nilai.konversi)/3) },
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
                                    <button disabled={this.state.loading} onClick={this.loadPeserta} className="btn btn-outline-secondary">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                </div>
                            </div>
                            <DataTable
                                customStyles={compactGrid}
                                dense={true} striped={true} columns={columns} data={this.state.peserta}
                                expandableRows expandableRowsComponent={detailNilai}
                                persistTableHead progressPending={this.state.loading}/>
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