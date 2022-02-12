import React from 'react';
import DataTable from "react-data-table-component";

import {showErrorMessage} from "../../Components/Alerts";
import {compactGrid} from '../../Components/DataTableStyles';
import {saveCapaianKeterampilan} from "../../Services/Master/ScheduleService";

export default class ScheduleIndikatorTable extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            loading : false,
            komponen : props.komponen,
            indikator : props.data
        };
        this.renderTombolYa = this.renderTombolYa.bind(this);
        this.renderTombolTidak = this.renderTombolTidak.bind(this);
    }
    componentDidMount(){
    }
    async handleSubmitIndikator(event,data){
        let indikator = this.state.indikator;
        let indikatorIndex = indikator.findIndex((e) => e.id === data.id);
        if (indikatorIndex >= 0) {
            let valueNya = event.currentTarget.attributes['data-value'].value;
            if (valueNya === 'yes'){
                indikator[indikatorIndex].capaian = { nilai : 1 };
            } else {
                indikator[indikatorIndex].capaian = { nilai : 0 };
            }
            this.setState({indikator});
            try {
                let formData = new FormData();
                formData.append('_method','put');
                formData.append('nilai', valueNya);
                formData.append('indikator', data.id);
                formData.append('peserta', this.props.peserta.value);
                formData.append('ujian', this.props.ujian.value);
                let response = await saveCapaianKeterampilan(this.props.token,formData);
                if (response.data.params === null) {
                    showErrorMessage(response.data.message);
                } else {
                    indikator[indikatorIndex].capaian = response.data.params;
                    this.setState({indikator});
                }
            } catch (err) {
                showErrorMessage(err.response.data.message);
            }
        }
    }
    renderTombolYa(data){
        let className = 'btn btn-sm btn-block btn-outline-secondary';
        if (data.capaian !== null) {
            if (data.capaian.nilai === 1){
                className = 'btn btn-sm btn-block btn-success';
            }
        }
        return <button data-value="yes" onClick={(e)=>this.handleSubmitIndikator(e,data)} className={className}><i className="fas fa-check"/></button>
    }
    renderTombolTidak(data){
        let className = 'btn btn-sm btn-block btn-outline-secondary';
        if (data.capaian !== null) {
            if (data.capaian.nilai === 0){
                className = 'btn btn-sm btn-block btn-danger';
            }
        }
        return <button data-value="no" onClick={(e)=>this.handleSubmitIndikator(e,data)} className={className}><i className="fas fa-xmark"/></button>
    }
    render(){
        const columns = [
            { name : 'No.', cell : row => this.state.komponen.meta.nomor + '.' + row.nomor, selector : row => row.nomor, width : '70px', center : true },
            { name : 'Kriteria Unjuk Kerja', selector : row => row.indikator },
            { name : 'YA', cell : row => this.renderTombolYa(row), width : '100px', center : true },
            { name : 'TIDAK', cell : row => this.renderTombolTidak(row), width : '100px', center : true },
        ];
        return (
            <DataTable
                customStyles={compactGrid} dense={true} striped={true}
                columns={columns} data={this.state.indikator}
                persistTableHead progressPending={this.state.loading}/>
        );
    }
}