import React from 'react';
import DataTable from "react-data-table-component";
import {compactGrid} from '../../../../Components/DataTableStyles';

export default class IndikatorTable extends React.Component{
    constructor(props){
        super(props);
    }
    render(){
        const columns = [
            { name : '#', width : '50px', cell : row => row.nomor, center : true },
            { name : 'Kriteria Unjuk Kerja / Indikator Penilaian', cell : row => row.indikator },
        ];
        return (
            <DataTable
                customStyles={compactGrid}
                dense={true} striped={true} columns={columns}
                data={this.props.data}
                persistTableHead />
        );
    }
}