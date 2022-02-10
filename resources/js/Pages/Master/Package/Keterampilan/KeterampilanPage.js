import React from 'react';
import ReactDOM from 'react-dom';
import DataTable from "react-data-table-component";
import Swal from "sweetalert2";
import Axios from 'axios';

import {compactGrid} from '../../../../Components/DataTableStyles';
import {showErrorMessage, showSuccessMessage} from "../../../../Components/Alerts";
import MainHeader from "../../../../Components/Layouts/MainHeader";
import MainSideBar from "../../../../Components/Layouts/MainSideBar";
import MainFooter from "../../../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../../../Components/Layouts/BreadCrumbs";
import {komponenOptions} from "../../../../Components/KomponenOptions";

import {currentUser} from "../../../../Services/AuthService";
import {getPackages} from "../../../../Services/Master/PackageService";
import CreateKomponen from "./Modals/CreateKomponen";
import IndikatorTable from "./IndikatorTable";

export default class KeterampilanPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null, breadcrumbs : [
                { label : 'Paket Soal', url : window.origin + '/master/packages' }
            ], loading : false,
            form : {}, current_package : null, komponent_keterampilan : [],
            modals : {
                create : { open : false, data : null },
                update : { open : false, data : null }
            }
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadPackages = this.loadPackages.bind(this);
        this.toggleCreate = this.toggleCreate.bind(this);
    }
    componentDidMount(){
        this.loadMe();
    }
    toggleCreate(){
        let modals = this.state.modals;
        modals.create.data = this.state.current_package;
        modals.create.open = ! this.state.modals.create.open;
        this.setState({modals});
    }
    async loadPackages(){
        this.setState({loading:true,current_package:null,komponent_keterampilan:[]});
        let current_url = window.location.href;
        current_url = current_url.split('/');
        if (current_url.length >= 5){
            if (typeof current_url[5] !== 'undefined'){
                current_url = current_url[5];
                try {
                    let response = await getPackages(this.state.token,{id:current_url});
                    if (response.data.params === null) {
                        showErrorMessage(response.data.message);
                    } else if (response.data.params.length === 0) {
                        showErrorMessage('Data tidak ditemukan');
                    } else {
                        let current_package = response.data.params[0];
                        let komponent_keterampilan = [];
                        if (current_package.meta.komponen.keterampilan.length > 0){
                            komponent_keterampilan = current_package.meta.komponen.keterampilan;
                        }
                        this.setState({current_package,komponent_keterampilan});
                    }
                } catch (error) {
                    showErrorMessage(error.response.data.message);
                }
            }
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
        this.loadPackages();
    }
    render(){
        const columns = [
            { name : '#', selector : row => row.meta.nomor, sortable : true, width : '70px', center : true },
            { name : 'Komponen', selector : row => row.meta.komponen, sortable : true, width : '150px' },
            { name : 'Sub Komponen', selector : row => row.label, sortable : true },
            { name : 'SB', selector : row => row.meta.nilai.sb, sortable : true, width : '70px', center : true },
            { name : 'B', selector : row => row.meta.nilai.b, sortable : true, width : '70px', center : true },
            { name : 'C', selector : row => row.meta.nilai.c, sortable : true, width : '70px', center : true },
            { name : 'T', selector : row => row.meta.nilai.t, sortable : true, width : '70px', center : true },
        ];
        const indikatorTable = ({data}) => <IndikatorTable data={data.meta.indikator}/>
        return (
            <>

                <CreateKomponen
                    data={this.state.modals.create.data}
                    handleUpdate={this.loadPackages}
                    handleClose={this.toggleCreate}
                    open={this.state.modals.create.open}
                    token={this.state.token}/>

                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title={this.state.current_package === null ? 'Komponen Keterampilan' : this.state.current_package.label } breadcrumbs={this.state.breadcrumbs}/>
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
                                dense={true} striped={true} columns={columns} data={this.state.komponent_keterampilan}
                                expandableRows expandableRowsComponent={indikatorTable}
                                persistTableHead progressPending={this.state.loading}/>
                        </div>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('component-keterampilan-page')){
    ReactDOM.render(<KeterampilanPage/>, document.getElementById('component-keterampilan-page'));
}