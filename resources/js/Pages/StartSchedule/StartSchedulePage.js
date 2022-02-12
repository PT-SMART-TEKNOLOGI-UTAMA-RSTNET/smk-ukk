import React from 'react';
import ReactDOM from 'react-dom';
import Select from 'react-select';
import DataTable from "react-data-table-component";

import {compactGrid} from '../../Components/DataTableStyles';
import MainHeader from "../../Components/Layouts/MainHeader";
import MainSideBar from "../../Components/Layouts/MainSideBar";
import {showErrorMessage,showSuccessMessage} from "../../Components/Alerts";
import MainFooter from "../../Components/Layouts/MainFooter";
import ScheduleIndikatorTable from './ScheduleIndikatorTable';

import {currentUser} from "../../Services/AuthService";
import {getSchedules} from "../../Services/Master/ScheduleService";

export default class StartSchedulePage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null,
            loading : false, peserta : [],
            current_schedule : null, current_peserta : null,
            columns : {
                komponen : []
            },
            komponen : {
                keterampilan : {
                    persiapan : [],
                    pelaksanaan : [],
                    hasil : []
                }
            }
        };
        this.loadMe = this.loadMe.bind(this);
        this.loadSchedules = this.loadSchedules.bind(this);
        this.handleSelectPeserta = this.handleSelectPeserta.bind(this);
    }
    componentDidMount(){
        this.loadMe();
        let columns = this.state.columns;
        columns.komponen = [
            { name : '#', grow : 0, width : '50px', center : true, sortable : true, selector : row => row.meta.nomor },
            { name : 'Sub Komponen', selector : row => row.label, sortable : true }
        ];
    }
    handleSelectPeserta(e){
        let current_peserta = this.state.current_peserta;
        current_peserta = e;
        if (current_peserta.meta.paket.komponen.keterampilan.length > 0) {
            let komponen = this.state.komponen;
            komponen.keterampilan.persiapan = current_peserta.meta.paket.komponen.keterampilan.filter((i) => i.meta.type === this.state.current_user.meta.penguji.type && i.meta.komponen === 'persiapan');
            komponen.keterampilan.pelaksanaan = current_peserta.meta.paket.komponen.keterampilan.filter((i) => i.meta.type === this.state.current_user.meta.penguji.type && i.meta.komponen === 'pelaksanaan');
            komponen.keterampilan.hasil = current_peserta.meta.paket.komponen.keterampilan.filter((i) => i.meta.type === this.state.current_user.meta.penguji.type && i.meta.komponen === 'hasil');
            this.setState({komponen});
        }
        this.setState({current_peserta});
    }
    async loadSchedules(){
        this.setState({loading:true,komponen : {keterampilan : {persiapan : [],pelaksanaan : [],hasil : []}}});
        try {
            let current_url = window.location.href;
            current_url = current_url.split('/');
            if (current_url.length >= 4) {
                if (typeof current_url[current_url.length - 1] !== 'undefined') {
                    current_url = current_url[current_url.length - 1];
                    try {
                        let response = await getSchedules(this.state.token,{id:current_url});
                        if (response.data.params === 0) {
                            showErrorMessage(response.data.message);
                        } else if (response.data.params.length === 0) {
                            showErrorMessage('Tidak dapat menemukan data ujian');
                        } else {
                            let current_schedule = response.data.params[0];
                            let peserta = current_schedule.meta.peserta.filter((i) => i.meta.penguji[this.state.current_user.meta.penguji.type].id === this.state.current_user.value);
                            this.setState({current_schedule,peserta});
                        }
                    } catch (er) {
                        showErrorMessage(er.response.data.message);
                    }
                }
            }
        } catch (er1) {
            showErrorMessage('Tidak dapat mengambil id ujian');
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
        this.loadSchedules();
    }
    render(){
        const indikatorTable = ({data}) => <ScheduleIndikatorTable ujian={this.state.current_schedule}
                                                                   peserta={this.state.current_peserta}
                                                                   token={this.state.token}
                                                                   komponen={data}
                                                                   data={data.meta.indikator}/>;
        return (
            <>
                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <section className="content" style={{paddingTop:'20px'}}>
                        <Select
                            isLoading={this.state.loading}
                            isDisabled={this.state.loading}
                            isClearable
                            onChange={this.handleSelectPeserta}
                            value={this.state.current_peserta}
                            options={this.state.peserta}/>

                        {this.state.current_peserta === null ? null :
                            <>
                                {this.state.komponen.keterampilan.persiapan.length === 0 ? null :
                                    <div className="card mt-3">
                                        <div className="card-header">
                                            <h3 className="card-title">Persiapan</h3>
                                            <div className="card-tools">
                                                <button type="button" className="btn btn-tool" data-card-widget="collapse"><i className="fas fa-minus"/></button>
                                            </div>
                                        </div>
                                        <div className="card-body p-0">
                                            <DataTable
                                                customStyles={compactGrid} dense={true} striped={true}
                                                expandableRows expandableRowsComponent={indikatorTable}
                                                columns={this.state.columns.komponen} data={this.state.komponen.keterampilan.persiapan}
                                                persistTableHead progressPending={this.state.loading}/>
                                        </div>
                                    </div>
                                }
                                {this.state.komponen.keterampilan.pelaksanaan.length === 0 ? null :
                                    <div className="card mt-3">
                                        <div className="card-header">
                                            <h3 className="card-title">Pelaksanaan</h3>
                                            <div className="card-tools">
                                                <button type="button" className="btn btn-tool" data-card-widget="collapse"><i className="fas fa-minus"/></button>
                                            </div>
                                        </div>
                                        <div className="card-body p-0">
                                            <DataTable
                                                customStyles={compactGrid} dense={true} striped={true}
                                                expandableRows expandableRowsComponent={indikatorTable}
                                                columns={this.state.columns.komponen} data={this.state.komponen.keterampilan.pelaksanaan}
                                                persistTableHead progressPending={this.state.loading}/>
                                        </div>
                                    </div>
                                }
                                {this.state.komponen.keterampilan.hasil.length === 0 ? null :
                                    <div className="card mt-3">
                                        <div className="card-header">
                                            <h3 className="card-title">Hasil</h3>
                                            <div className="card-tools">
                                                <button type="button" className="btn btn-tool" data-card-widget="collapse"><i className="fas fa-minus"/></button>
                                            </div>
                                        </div>
                                        <div className="card-body p-0">
                                            <DataTable
                                                customStyles={compactGrid} dense={true} striped={true}
                                                expandableRows expandableRowsComponent={indikatorTable}
                                                columns={this.state.columns.komponen} data={this.state.komponen.keterampilan.hasil}
                                                persistTableHead progressPending={this.state.loading}/>
                                        </div>
                                    </div>
                                }
                            </>
                        }
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('mulai-ujian-page')){
    ReactDOM.render(<StartSchedulePage/>, document.getElementById('mulai-ujian-page'));
}