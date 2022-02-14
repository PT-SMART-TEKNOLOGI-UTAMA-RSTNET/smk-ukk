import React from 'react';
import DataTable from "react-data-table-component";
import {compactGrid} from '../../../../Components/DataTableStyles';

export default class IndikatorTable extends React.Component{
    constructor(props){
        super(props);
        this.state = {};
    }
    render(){
        const columns = [
            { name : '#', selector : row => row.meta.nomor, grow : 0, width : '50px', center : true },
            { name : 'Jawaban', selector : row => row.label },
            { name : 'Benar', grow : 0, width : '70px', center : true, selector : row =>
                    this.props.komponen.meta.answer.id === row.value ?
                        <i className="fas fa-circle-check text-success"/>
                        :
                        <i className="fas fa-times text-danger"/>
            },
        ];
        return (
            <DataTable
                customStyles={compactGrid}
                dense={true} striped={true} columns={columns} data={this.props.data}
                persistTableHead />
        );
    }
}