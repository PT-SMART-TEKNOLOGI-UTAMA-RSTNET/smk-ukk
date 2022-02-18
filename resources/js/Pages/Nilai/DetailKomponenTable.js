import React from 'react';
import DataTable from "react-data-table-component";
import {compactGrid} from '../../Components/DataTableStyles';
import Parser from 'html-react-parser';

export default class DetailKomponenTable extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            data_komponen : []
        };
    }
    componentDidMount(){
        let data_komponen = [];
        let datanya = this.props.data;
        if (datanya !== null) {
            switch(this.props.jenis){
                case 'Keterampilan' :
                    datanya.persiapan.map((item,index)=>{
                        data_komponen.push({
                            nomor : '1.' + item.meta.nomor,
                            komponen : 'Persiapan',
                            sub_komponen : item.label,
                            indikator : item.meta.nilai.jml_indikator,
                            capaian : item.meta.nilai.jml_capaian,
                            nilai : item.meta.nilai.nilai_akhir
                        });
                    });
                    datanya.pelaksanaan.map((item,index)=>{
                        data_komponen.push({
                            nomor : '2.' + item.meta.nomor,
                            komponen : 'Persiapan',
                            sub_komponen : item.label,
                            indikator : item.meta.nilai.jml_indikator,
                            capaian : item.meta.nilai.jml_capaian,
                            nilai : item.meta.nilai.nilai_akhir
                        });
                    });
                    datanya.hasil.map((item,index)=>{
                        data_komponen.push({
                            nomor : '3.' + item.meta.nomor,
                            komponen : 'Persiapan',
                            sub_komponen : item.label,
                            indikator : item.meta.nilai.jml_indikator,
                            capaian : item.meta.nilai.jml_capaian,
                            nilai : item.meta.nilai.nilai_akhir
                        });
                    });
                    break;
                case 'Pengetahuan' :
                    datanya.soal.map((item,index)=>{
                        data_komponen.push({
                            nomor : item.meta.nomor,
                            komponen : 'Soal',
                            sub_komponen : Parser(item.label),
                            indikator : item.meta.nilai.jml_indikator,
                            capaian : item.meta.nilai.jml_capaian,
                            nilai : item.meta.nilai.nilai_akhir
                        });
                    });
                    break;
                case 'Sikap' :
                    datanya.sikap.map((item,index)=>{
                        data_komponen.push({
                            nomor : item.meta.nomor,
                            komponen : 'Sikap',
                            sub_komponen : item.label,
                            indikator : item.meta.nilai.jml_indikator,
                            capaian : item.meta.nilai.jml_capaian,
                            nilai : item.meta.nilai.nilai_akhir
                        });
                    });
                    break;
            }

            this.setState({data_komponen});
        }
    }
    render() {
        const columns = [
            { name : '#', grow : 0, width : '70px', center : true, selector : row => row.nomor },
            { name : 'Komponen', selector : row => row.komponen, width : '120px' },
            { name : 'Sub Komponen', wrap : true, selector : row => row.sub_komponen },
            { name : 'I', width : '50px', grow : 0, cell : row => row.indikator, center : true },
            { name : 'C', width : '50px', grow : 0, cell : row => row.capaian, center : true },
            { name : 'NA', width : '70px', grow : 0, cell : row => row.nilai, center : true },
        ];
        return (
            <DataTable
                customStyles={compactGrid}
                dense={true} striped={true} columns={columns} data={this.state.data_komponen}
                persistTableHead progressPending={this.state.loading}/>
        );
    }
}