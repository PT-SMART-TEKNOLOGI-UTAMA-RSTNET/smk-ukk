import React from 'react';
import DataTable from "react-data-table-component";
import {compactGrid} from '../../Components/DataTableStyles';
import DetailKomponenTable from "./DetailKomponenTable";

export default class DetailNilaiTable extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            datanya : []
        };
        this.renderX = this.renderX.bind(this);
    }
    renderX(data){
        let response = 0;
        if (typeof data.value !== 'undefined'){
            if (typeof data.value.nilai !== 'undefined'){
                if (typeof data.value.nilai.konversi !== 'undefined') {
                    response = data.value.nilai.konversi;
                }
            }
        }
        return response;
    }
    render() {
        const detailKomponen = ({data}) => <DetailKomponenTable peserta={this.props.parent} parent={data} jenis={data.label} data={data.value.komponen}/>;
        const columns = [
            { name : 'Komponen', selector : row => row.label },
            { name : 'Nilai', grow : 0, width : '70px', center : true, selector : row => this.renderX(row) }
        ];
        return (
            <DataTable
                customStyles={compactGrid}
                expandableRows expandableRowsComponent={detailKomponen}
                dense={true} striped={true} columns={columns} data={this.props.data}
                persistTableHead progressPending={this.state.loading}/>
        );
    }
}