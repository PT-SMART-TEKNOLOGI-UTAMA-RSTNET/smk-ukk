import React from 'react';
import ReactDOM from 'react-dom';
import DataTable from "react-data-table-component";
import Swal from "sweetalert2";
import Axios from 'axios';

import {compactGrid} from '../../../Components/DataTableStyles';
import {showErrorMessage, showSuccessMessage} from "../../../Components/Alerts";
import MainHeader from "../../../Components/Layouts/MainHeader";
import MainSideBar from "../../../Components/Layouts/MainSideBar";
import MainFooter from "../../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../../Components/Layouts/BreadCrumbs";

import {currentUser} from "../../../Services/AuthService";
import {getPackages} from "../../../Services/Master/PackageService";
import {getJurusan} from "../../../Services/EraporService";
import CreatePackage from "./Modals/CreatePackage";
import UpdatePackage from "./Modals/UpdatePackage";

export default class PackagePage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null, breadcrumbs : [], loading : false, jurusan : [],
            form : {}, packages : [],
            modals : {
                create : { open : false },
                update : { open : false, data : null }
            }
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadJurusan = this.loadJurusan.bind(this);
        this.loadPackages = this.loadPackages.bind(this);
        this.toggleCreate = this.toggleCreate.bind(this);
        this.toggleUpdate = this.toggleUpdate.bind(this);
        this.confirmDelete = this.confirmDelete.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    confirmDelete(data){
        let message = 'Anda yakin ingin menghapus Paket Soal <b>' + data.label + '</b>?';
        let formData = new FormData();
        formData.append('_method','delete');
        formData.append('id', data.value);
        Swal.fire({
            title: "Perhatian !", html: message, icon: "question", showCancelButton: true, confirmButtonColor: "#FF9800",
            confirmButtonText: "Hapus", cancelButtonText: "Batalkan", cancelButtonColor: '#ddd', closeOnConfirm : false,
            showLoaderOnConfirm: true, allowOutsideClick: () => ! Swal.isLoading(), allowEscapeKey : () => ! Swal.isLoading(),
            preConfirm : (e)=> {
                return Promise.resolve(Axios({headers : { "Authorization" : "Bearer " + this.state.token }, method : 'post', data : formData, url : window.origin + '/api/auth/master/packages'}))
                    .then((response) => {
                        if (response.status !== 200){
                            Swal.showValidationMessage(response.data.message, true);
                            Swal.hideLoading();
                        } else if (response.data.params === null){
                            Swal.showValidationMessage(response.data.message, true);
                            Swal.hideLoading();
                        } else {
                            Swal.close();
                            this.loadPackages();
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
    async loadPackages(){
        this.setState({loading:true,packages:[]});
        try {
            let response = await getPackages(this.state.token,this.state.form);
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                let packages = response.data.params;
                this.setState({packages});
            }
        } catch (error) {
            showErrorMessage(error.response.data.message);
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
        this.loadPackages();
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
    }
    render(){
        const columns = [
            { name : 'Nama Paket Soal', selector : row => row.label, sortable : true },
            { name : 'Jurusan', selector : row => row.meta.jurusan.label, sortable : true },
            { name : 'Keterampilan', selector : row => <a href={window.origin+'/master/packages/' + row.value + '/keterampilan'}>{row.meta.komponen.keterampilan.length} Komponen</a>, sortable : true, center : true, grow : 0 },
            { name : 'Pengetahuan', selector : row => <a href={window.origin+'/master/packages/' + row.value + '/pengetahuan'}>{row.meta.komponen.pengetahuan.length} Soal</a>, sortable : true, center : true, grow : 0 },
            { name : 'Sikap', selector : row => <a href={window.origin+'/master/packages/' + row.value + '/sikap'}>{row.meta.komponen.sikap.length} Komponen</a> , sortable : true, center : true, grow : 0 },
            { name : '', width : '100px', center : true, selector : row =>
                this.state.current_user === null ? null :
                    this.state.current_user.meta.level !== 'admin' ? null :
                        <>
                            <button onClick={()=>this.toggleUpdate(row)} title="Rubah data" disabled={this.state.loading} className="btn btn-xs btn-default mr-1"><i className="fas fa-pen"/></button>
                            <button onClick={()=>this.confirmDelete(row)} title="Hapus data" disabled={this.state.loading} className="btn btn-xs btn-danger"><i className="fas fa-trash-alt"/></button>
                        </>
            }
        ];
        return (
            <>
                <UpdatePackage
                    data={this.state.modals.update.data}
                    jurusan={this.state.jurusan}
                    handleUpdate={this.loadPackages}
                    handleClose={this.toggleUpdate}
                    open={this.state.modals.update.open}
                    token={this.state.token}/>
                <CreatePackage
                    jurusan={this.state.jurusan}
                    handleUpdate={this.loadPackages}
                    handleClose={this.toggleCreate}
                    open={this.state.modals.create.open}
                    token={this.state.token}/>

                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Dashboard" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <div className="card">
                            <div className="card-header">
                                <h3 className="card-title"/>
                                <div className="card-tools">
                                    <button disabled={this.state.loading} onClick={this.toggleCreate} className="btn btn-primary btn-sm mr-1" title="Tambah Data">
                                        <i className="fas fa-plus-circle"/> Tambah Data
                                    </button>
                                    <button disabled={this.state.loading} onClick={this.loadPackages} className="btn mr-1">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                </div>
                            </div>
                            <DataTable
                                customStyles={compactGrid}
                                dense={true} striped={true} columns={columns} data={this.state.packages}
                                persistTableHead progressPending={this.state.loading}/>
                        </div>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('package-page')){
    ReactDOM.render(<PackagePage/>, document.getElementById('package-page'));
}