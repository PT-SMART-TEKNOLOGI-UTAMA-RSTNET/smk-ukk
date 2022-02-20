import React from 'react';
import ReactDOM from 'react-dom';
import DataTable from "react-data-table-component";
import Swal from "sweetalert2";
import Select from 'react-select';
import Axios from 'axios';

import {compactGrid} from '../../../Components/DataTableStyles';
import {showErrorMessage, showSuccessMessage} from "../../../Components/Alerts";
import MainHeader from "../../../Components/Layouts/MainHeader";
import MainSideBar from "../../../Components/Layouts/MainSideBar";
import MainFooter from "../../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../../Components/Layouts/BreadCrumbs";

import {pengujiTypeOptions} from "../../../Components/KomponenOptions";
import {currentUser} from "../../../Services/AuthService";
import {crudAsesor,syncErapor} from "../../../Services/Master/AsessorService";
import CreateAsessor from "./Modals/CreateAsessor";
import UpdateAsessor from "./Modals/UpdateAsessor";

export default class AsessorPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null, breadcrumbs : [], loading : false,
            form : {
                type : null, keyword : ''
            },
            data_asessor : [],
            modals : {
                create : { open : false },
                update : { open : false, data : null }
            }
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadAsessors = this.loadAsessors.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.handleSearch = this.handleSearch.bind(this);
        this.confirmDelete = this.confirmDelete.bind(this);
        this.handleSelect = this.handleSelect.bind(this);
        this.syncErapor = this.syncErapor.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    handleSelect(event){
        let form = this.state.form;
        form.type = event;
        this.setState({form});
        this.loadAsessors();
    }
    handleSearch(keyword){
        let form = this.state.form;
        form.keyword = keyword;
        this.setState({form});
        this.loadAsessors();
    }
    confirmDelete(data){
        let message = 'Anda yakin ingin menghapus Asessor <b>' + data.label + '</b>?';
        let formData = new FormData();
        formData.append('_method','delete');
        formData.append('id', data.value);
        Swal.fire({
            title: "Perhatian !", html: message, icon: "question", showCancelButton: true, confirmButtonColor: "#FF9800",
            confirmButtonText: "Hapus", cancelButtonText: "Batalkan", cancelButtonColor: '#ddd', closeOnConfirm : false,
            showLoaderOnConfirm: true, allowOutsideClick: () => ! Swal.isLoading(), allowEscapeKey : () => ! Swal.isLoading(),
            preConfirm : (e)=> {
                return Promise.resolve(Axios({headers : { "Authorization" : "Bearer " + this.state.token }, method : 'post', data : formData, url : window.origin + '/api/auth/master/asessors'}))
                    .then((response) => {
                        if (response.status !== 200){
                            Swal.showValidationMessage(response.data.message, true);
                            Swal.hideLoading();
                        } else if (response.data.params === null){
                            Swal.showValidationMessage(response.data.message, true);
                            Swal.hideLoading();
                        } else {
                            Swal.close();
                            this.loadAsessors();
                            showSuccessMessage(response.data.message);
                        }
                    }).catch((error)=>{
                        Swal.showValidationMessage(error.response.data.message,true);
                    });
            }
        });
    }
    toggleModal(what,data=null){
        let modals = this.state.modals;
        modals[what].open = ! this.state.modals[what].open;
        if (data !== null){
            modals[what].data = data;
        }
        this.setState({modals});
    }
    async syncErapor(){
        this.setState({loading:true});
        try {
            let response = await syncErapor(this.state.token,{});
            if (response.data.params === null) {
                showErrorMessage(response.data.message);
            } else {
                this.loadAsessors();
            }
        } catch (er) {
            showErrorMessage(er.response.data.message);
        }
        this.setState({loading:false});
    }
    async loadAsessors(){
        this.setState({loading:true,data_asessor:[]});
        try {
            let formData = new FormData();
            if (this.state.form.type !== null) {
                formData.append('type', this.state.form.type.value);
            }
            try {
                let response = await crudAsesor(this.state.token, formData);
                if (response.data.params === null) {
                    showErrorMessage(response.data.message);
                } else {
                    let data_asessor = response.data.params;
                    if (this.state.form.type !== null) {
                        data_asessor = data_asessor.filter((i) => i.meta.penguji.type === this.state.form.type.value);
                    }
                    if (this.state.form.keyword.length > 0) {
                        data_asessor = data_asessor.filter((i) => i.label.toLowerCase().includes(this.state.form.keyword.toLowerCase()) || i.meta.email.toLowerCase().includes(this.state.form.keyword.toLowerCase()));
                    }
                    this.setState({data_asessor});
                }
            } catch (error) {
                console.log(error);
                if (error.response.status === 401){
                    window.location.href = window.origin + '/logout';
                    error.response.data.message = 'Sesi login telah habis';
                }
                showErrorMessage(error.response.data.message);
            }
        } catch (erForm) {
            showErrorMessage('Unknown error');
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
                error.response.data.message = 'Sesi login telah habis';
            }
            showErrorMessage(error.response.data.message);
        }
        this.setState({loading:false});
        this.loadAsessors();
    }
    render(){
        const columns = [
            { name : 'Nama Asessor', selector : row => row.label, sortable : true },
            { name : 'Email / Username', selector : row => row.meta.email, sortable : true },
            { name : 'Jenis Asessor', selector : row => row.meta.penguji.type, sortable : true, width : '150px' },
            { name : '', width : '100px', center : true, grow : 0, selector : row =>
                    this.state.current_user === null ? null :
                        this.state.current_user.meta.level !== 'admin' ? null :
                            <>
                                <button onClick={()=>this.toggleModal('update',row)} title="Rubah data" disabled={this.state.loading} className="btn btn-xs btn-outline-secondary btn-flat mr-1"><i className="fas fa-pen"/></button>
                                <button onClick={()=>this.confirmDelete(row)} title="Hapus data" disabled={this.state.loading} className="btn btn-xs btn-outline-danger btn-flat"><i className="fas fa-trash-alt"/></button>
                            </>
            }
        ];
        return (
            <>
                <UpdateAsessor
                    handleUpdate={this.loadAsessors}
                    handleClose={this.toggleModal}
                    data={this.state.modals.update.data}
                    open={this.state.modals.update.open}
                    token={this.state.token}/>
                <CreateAsessor
                    handleUpdate={this.loadAsessors}
                    handleClose={this.toggleModal}
                    open={this.state.modals.create.open}
                    token={this.state.token}/>

                <MainHeader handleSearch={this.handleSearch} searching={true} current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Asessor" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <div className="card">
                            <div className="card-header">
                                <div className="float-left">
                                    <Select isClearable={true} isLoading={this.state.loading} isDisabled={this.state.loading} onChange={this.handleSelect} value={this.state.form.type} options={pengujiTypeOptions}/>
                                </div>
                                <div className="card-tools">
                                    {this.state.current_user === null ? null :
                                        this.state.current_user.meta.level !== 'admin' ? null :
                                            <>
                                                <button disabled={this.state.loading} onClick={this.syncErapor} className="btn btn-flat btn-outline-info mr-1" title="Sync Erapor">
                                                    <i className="fas fa-link"/> Sync Erapor
                                                </button>
                                                <button disabled={this.state.loading} onClick={()=>this.toggleModal('create')} className="btn btn-outline-primary btn-flat mr-1" title="Tambah Data">
                                                    <i className="fas fa-plus"/> Tambah Data
                                                </button>
                                            </>
                                    }
                                    <button disabled={this.state.loading} onClick={this.loadAsessors} className="btn btn-outline-secondary btn-flat">{this.state.loading ? <i className="fas fa-spin fa-circle-notch"/> : <i className="fas fa-refresh"/>}</button>
                                </div>
                            </div>
                            <DataTable
                                customStyles={compactGrid}
                                dense={true} striped={true} columns={columns} data={this.state.data_asessor}
                                persistTableHead progressPending={this.state.loading}/>
                        </div>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('asessors-page')){
    ReactDOM.render(<AsessorPage/>, document.getElementById('asessors-page'));
}