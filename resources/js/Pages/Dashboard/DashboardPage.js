import React from 'react';
import ReactDOM from 'react-dom';
import MainHeader from "../../Components/Layouts/MainHeader";
import MainSideBar from "../../Components/Layouts/MainSideBar";
import {currentUser} from "../../Services/AuthService";
import {showErrorMessage} from "../../Components/Alerts";
import MainFooter from "../../Components/Layouts/MainFooter";
import BreadCrumbs from "../../Components/Layouts/BreadCrumbs";

export default class DashboardPage extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            current_user : null,
            breadcrumbs : [],
            loading : false
        };
        this.loadMe = this.loadMe.bind(this);
    }
    componentDidMount(){
        this.loadMe();
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
    }
    render(){
        return (
            <>
                <MainHeader current_user={this.state.current_user}/>
                <MainSideBar current_user={this.state.current_user}/>

                <div className="content-wrapper">
                    <BreadCrumbs title="Dashboard" breadcrumbs={this.state.breadcrumbs}/>
                    <section className="content">
                        <div className="card">
                            <div className="card-header">
                                <h3 className="card-title">Siswa</h3>
                                <div className="card-tools">
                                    <a onClick={this.toggleCreate} href="#" className="btn btn-primary btn-sm mr-1" title="Tambah Data">
                                        <i className="fas fa-plus-circle"/> Tambah Data
                                    </a>
                                    <a onClick={this.toggleUpload} href="#" className="btn btn-primary btn-sm mr-1" title="Import Data">
                                        <i className="fas fa-upload"/> Import Data
                                    </a>
                                    <a href={window.origin+'/master/siswa/download'} target="_blank" className="btn btn-primary btn-sm" title="Export Data">
                                        <i className="fas fa-download"/> Export Data
                                    </a>
                                </div>
                            </div>
                            TABELNYA
                        </div>
                    </section>
                </div>

                <MainFooter/>
            </>
        );
    }
}

if (document.getElementById('dashboard-page')){
    ReactDOM.render(<DashboardPage/>, document.getElementById('dashboard-page'));
}