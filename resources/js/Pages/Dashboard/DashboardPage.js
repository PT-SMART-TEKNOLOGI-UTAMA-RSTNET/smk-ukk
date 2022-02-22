import React from 'react';
import ReactDOM from 'react-dom';
import DataTable from "react-data-table-component";
import moment from 'moment';

import {compactGrid} from '../../Components/DataTableStyles';
import MainHeader from "../../Components/Layouts/MainHeader";
import MainSideBar from "../../Components/Layouts/MainSideBar";
import {currentUser} from "../../Services/AuthService";
import {showErrorMessage,showSuccessMessage} from "../../Components/Alerts";
import MainFooter from "../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../Components/Layouts/BreadCrumbs";

import {getSchedules} from "../../Services/Master/ScheduleService";

export default class DashboardPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null,
            breadcrumbs : [],
            loading : false, showHide : true,
            table : {
               columns : [], data : [],
            },
        };
        this.loadMe = this.loadMe.bind(this);
        this.populateDashboard = this.populateDashboard.bind(this);
        this.loadJadwalUjian = this.loadJadwalUjian.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    async loadJadwalUjian(){
        this.setState({loading:true});
        let table = this.state.table;
        table.data = [];
        try {
            let response = await getSchedules(this.state.token,{jenis_penguji:this.state.current_user.meta.penguji.type,id_penguji:this.state.current_user.value});
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                table.data = response.data.params;
                this.setState({table});
            }
        } catch (er) {
            showErrorMessage(er.response.data.message);
        }
        this.setState({loading:false});
    }
    populateDashboard(){
        let table = this.state.table;
        table.columns = [], table.data = [];
        if (this.state.current_user.meta.penguji.type !== null) {
            this.loadJadwalUjian();
        }
        this.setState({table});
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
        this.populateDashboard();
    }
    render(){
        const columns = [
            { name : 'Judul Ujian', selector : row => row.label },
            { name : 'Jadwal', width : '200px', wrap : true,
                cell : row => moment(row.meta.tanggal.mulai).format('DD MMMM yyyy, hh:mm') + ' - ' + moment(row.meta.tanggal.selesai).format('DD MMMM yyyy, hh:mm'),
                selector : row => row.meta.tanggal.mulai },
            { name : 'Aksi', grow : 0, center : true, cell : row =>
                    <a href={window.origin+'/mulai-ujian/' + row.value } title="Mulai Ujian" className="btn btn-outline-success"><i className="fas fa-play"/></a>
            },
        ];
        return (
            <>
                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Dashboard" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        {this.state.showHide ? null :
                            <div className="row">
                                <div className="col-md-3 col-sm-6 col-12">
                                    <div className="info-box bg-info">
                                        <span className="info-box-icon"><i className="fa-solid fa-user-graduate"/></span>
                                        <div className="info-box-content">
                                            <span className="info-box-text">Peserta</span>
                                            <span className="info-box-number">41,410</span>
                                            <div className="progress"><div className="progress-bar" style={{width:'100%'}}/></div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-3 col-sm-6 col-12">
                                    <div className="info-box bg-info">
                                        <span className="info-box-icon"><i className="fa-solid fa-user-tie"/></span>
                                        <div className="info-box-content">
                                            <span className="info-box-text">Penguji</span>
                                            <span className="info-box-number">41,410</span>
                                            <div className="progress"><div className="progress-bar" style={{width:'100%'}}/></div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-3 col-sm-6 col-12">
                                    <div className="info-box bg-info">
                                        <span className="info-box-icon"><i className="fa-solid fa-calendar-day"/></span>
                                        <div className="info-box-content">
                                            <span className="info-box-text">Jadwal Ujian</span>
                                            <span className="info-box-number">41,410</span>
                                            <div className="progress"><div className="progress-bar" style={{width:'100%'}}/></div>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-3 col-sm-6 col-12">
                                    <div className="info-box bg-info">
                                        <span className="info-box-icon"><i className="fa-solid fa-book"/></span>
                                        <div className="info-box-content">
                                            <span className="info-box-text">Paket Soal</span>
                                            <span className="info-box-number">41,410</span>
                                            <div className="progress"><div className="progress-bar" style={{width:'100%'}}/></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        }
                        {this.state.current_user === null ? null :
                            this.state.current_user.meta.penguji.type === null ? null :
                                <div className="card">
                                    <div className="card-header">
                                        <div className="card-tools">
                                            <button disabled={this.state.loading} onClick={this.loadJadwalUjian} className="btn btn-sm btn-outline-secondary">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                        </div>
                                    </div>
                                    <DataTable customStyles={compactGrid} dense={true} striped={true}
                                               persistTableHead progressPending={this.state.loading}
                                               data={this.state.table.data} columns={columns}/>
                                </div>
                        }
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('dashboard-page')){
    ReactDOM.render(<DashboardPage/>, document.getElementById('dashboard-page'));
}