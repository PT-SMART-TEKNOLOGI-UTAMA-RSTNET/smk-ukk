import React from 'react';
import ReactDOM from 'react-dom';
import DataTable from "react-data-table-component";
import Swal from "sweetalert2";
import Axios from 'axios';
import moment from 'moment';

import {compactGrid} from '../../../Components/DataTableStyles';
import {showErrorMessage, showSuccessMessage} from "../../../Components/Alerts";
import MainHeader from "../../../Components/Layouts/MainHeader";
import MainSideBar from "../../../Components/Layouts/MainSideBar";
import MainFooter from "../../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../../Components/Layouts/BreadCrumbs";

import {currentUser} from "../../../Services/AuthService";
import {getJurusan} from "../../../Services/EraporService";
import {getSchedules} from "../../../Services/Master/ScheduleService";
import CreateSchedule from "./Modals/CreateSchedule";
import UpdateSchedule from "./Modals/UpdateSchedule";

export default class SchedulePage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null, breadcrumbs : [], loading : false, jurusan : [],
            form : {}, schedules : [],
            modals : {
                create : { open : false },
                update : { open : false, data : null }
            }
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadJurusan = this.loadJurusan.bind(this);
        this.loadSchedules = this.loadSchedules.bind(this);
        this.toggleCreate = this.toggleCreate.bind(this);
        this.toggleUpdate = this.toggleUpdate.bind(this);
        this.confirmDelete = this.confirmDelete.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    confirmDelete(data){
        let message = 'Anda yakin ingin menghapus Jadwal Ujian <b>' + data.label + '</b>?';
        let formData = new FormData();
        formData.append('_method','delete');
        formData.append('id', data.value);
        Swal.fire({
            title: "Perhatian !", html: message, icon: "question", showCancelButton: true, confirmButtonColor: "#FF9800",
            confirmButtonText: "Hapus", cancelButtonText: "Batalkan", cancelButtonColor: '#ddd', closeOnConfirm : false,
            showLoaderOnConfirm: true, allowOutsideClick: () => ! Swal.isLoading(), allowEscapeKey : () => ! Swal.isLoading(),
            preConfirm : (e)=> {
                return Promise.resolve(Axios({headers : { "Authorization" : "Bearer " + this.state.token }, method : 'post', data : formData, url : window.origin + '/api/auth/master/schedules'}))
                    .then((response) => {
                        if (response.status !== 200){
                            Swal.showValidationMessage(response.data.message, true);
                            Swal.hideLoading();
                        } else if (response.data.params === null){
                            Swal.showValidationMessage(response.data.message, true);
                            Swal.hideLoading();
                        } else {
                            Swal.close();
                            this.loadSchedules();
                            showSuccessMessage(response.data.message);
                        }
                    }).catch((error)=>{
                        Swal.showValidationMessage(error.response.data.message,true);
                    });
            }
        });
    }
    toggleUpdate(data=null){
        let modals = this.state.modals;
        modals.update.open = ! this.state.modals.update.open;
        modals.update.data = data;
        this.setState({modals});
    }
    toggleCreate(){
        let modals = this.state.modals;
        modals.create.open = ! this.state.modals.create.open;
        this.setState({modals});
    }
    async loadSchedules(){
        this.setState({loading:true,schedules:[]});
        try {
            let response = await getSchedules(this.state.token,{minimal:true});
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let schedules = response.data.params;
                this.setState({schedules});
            }
        } catch (e) {
            showErrorMessage(e.response.data.message);
        }
        this.setState({loading:false});
    }
    async loadJurusan(){
        this.setState({loading:true});
        try {
            let response = await getJurusan(this.state.token);
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let jurusan = response.data.params;
                this.setState({jurusan});
            }
        } catch (error) {
            showErrorMessage(error.response.data.message);
        }
        this.setState({loading:false});
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
        this.loadJurusan();
        this.loadSchedules();
    }
    render(){
        const columns = [
            { name : 'Judul Ujian', cell : row => <span><b>{row.label}</b><br/><small>{row.meta.keterangan}</small></span>, selector : row => row.label, sortable : true },
            { name : 'Jurusan', selector : row => row.meta.jurusan.label, sortable : true, width : '120px', wrap : true },
            { name : 'Mulai', selector : row => row.meta.tanggal.mulai, cell : row => moment(row.meta.tanggal.mulai).format('DD MMMM yyyy, hh:mm'), width : '150px', sortable : true },
            { name : 'Selesai', selector : row => row.meta.tanggal.selesai, cell : row => moment(row.meta.tanggal.selesai).format('DD MMMM yyyy, hh:mm'), width : '150px', sortable : true },
            { name : 'Peserta', selector : row => row.meta.peserta.length, grow : 0, center : true, cell : row =>
                    <a href={window.origin+'/master/schedules/' + row.value + '/peserta'}>{row.meta.peserta.length} peserta</a>
            },
            { name : 'Tingkat', selector : row => row.meta.tingkat, width : '50px', center : true },
            /*{ name : 'Token', selector : row => row.meta.token.expired, cell : row => <span><b style={{display:'block'}} className="text-center">{row.meta.token.string}</b><small>{row.meta.token.expired === null ? '' : moment(row.meta.token.expired).format('DD MMMM yyyy, hh:mm')}</small></span>, width : '150px', center : true, sortable : true },*/
            { name : '', grow : 0, center : true, cell : row =>
                    <>
                        <button onClick={()=>this.toggleUpdate(row)} disabled={this.state.loading} type="button" className="btn btn-outline-primary btn-flat btn-xs mr-1"><i className="fas fa-pen"/></button>
                        <button onClick={()=>this.confirmDelete(row)} disabled={this.state.loading} type="button" className="btn btn-outline-danger btn-flat btn-xs mr-1"><i className="fas fa-trash-alt"/></button>
                        <button onClick={()=> window.location.href = window.origin + '/nilai/' + row.value } disabled={this.state.loading} type="button" className="btn btn-outline-secondary btn-flat btn-xs"><i className="fas fa-check-to-slot"/></button>
                    </>
            }
        ];
        return (
            <>
                <UpdateSchedule
                    jurusan={this.state.jurusan}
                    data={this.state.modals.update.data}
                    handleUpdate={this.loadSchedules}
                    handleClose={this.toggleUpdate}
                    open={this.state.modals.update.open}
                    token={this.state.token}/>
                <CreateSchedule
                    handleUpdate={this.loadSchedules}
                    jurusan={this.state.jurusan}
                    handleClose={this.toggleCreate}
                    open={this.state.modals.create.open}
                    token={this.state.token}/>

                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Jadwal Ujian" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <div className="card">
                            <div className="card-header">
                                <h3 className="card-title"/>
                                <div className="card-tools">
                                    {this.state.current_user === null ? null :
                                        this.state.current_user.meta.level !== 'admin' ? null :
                                            <button disabled={this.state.loading} onClick={this.toggleCreate} className="btn btn-outline-primary btn-flat mr-1" title="Tambah Data"><i className="fas fa-plus-circle"/> Tambah Data Ujian</button>
                                    }
                                    <button disabled={this.state.loading} onClick={this.loadSchedules} className="btn btn-outline-secondary btn-flat">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                </div>
                            </div>
                            <DataTable
                                customStyles={compactGrid}
                                dense={true} striped={true} columns={columns} data={this.state.schedules}
                                persistTableHead progressPending={this.state.loading}/>
                        </div>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('schedules-page')){
    ReactDOM.render(<SchedulePage/>, document.getElementById('schedules-page'));
}