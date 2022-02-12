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
            loading : false,
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
            table.columns = [
                { name : 'Judul Ujian', selector : row => row.label },
                { name : 'Jadwal', width : '200px', wrap : true,
                    cell : row => moment(row.meta.tanggal.mulai).format('DD MMMM yyyy, hh:mm') + ' - ' + moment(row.meta.tanggal.selesai).format('DD MMMM yyyy, hh:mm'),
                    selector : row => row.meta.tanggal.mulai },
                { name : 'Aksi', grow : 0, center : true, cell : row =>
                        <a href={window.origin+'/mulai-ujian/' + row.value } title="Mulai Ujian" className="btn btn-outline-success"><i className="fas fa-play"/></a>
                },
            ];
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
        return (
            <>
                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Dashboard" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <div className="card">
                            <div className="card-header">
                                <div className="card-tools">
                                    <button disabled={this.state.loading} onClick={this.loadJadwalUjian} className="btn btn-sm btn-outline-secondary">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                </div>
                            </div>
                            <DataTable customStyles={compactGrid} dense={true} striped={true}
                                       persistTableHead progressPending={this.state.loading}
                                       data={this.state.table.data} columns={this.state.table.columns}/>
                        </div>
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