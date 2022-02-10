import React from 'react';

export default class MainHeader extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            show_dropdown : false, keyword : '',
        };
        this.handleShowDropDown = this.handleShowDropDown.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.handleSubmitSearch = this.handleSubmitSearch.bind(this);
    }
    handleInput(e){
        let keyword = e.target.value;
        this.setState({keyword});
    }
    handleSubmitSearch(e){
        e.preventDefault();
        this.props.handleSearch(this.state.keyword);
    }
    handleShowDropDown(){
        let show_dropdown = ! this.state.show_dropdown;
        this.setState({show_dropdown});
    }
    render(){
        return (
            <nav className="main-header navbar navbar-expand navbar-white navbar-light">
                <ul className="navbar-nav">
                    <li className="nav-item">
                        <a className="nav-link" data-widget="pushmenu" href="#" role="button"><i className="fas fa-bars"/></a>
                    </li>
                </ul>

                {
                    ! this.props.searching ? null :
                        <form onSubmit={this.handleSubmitSearch} className="form-inline ml-3">
                            <div className="input-group input-group-sm">
                                <input value={this.state.keyword} onChange={this.handleInput} className="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search"/>
                                <div className="input-group-append">
                                    <button className="btn btn-navbar" type="submit">
                                        <i className="fas fa-search"/>
                                    </button>
                                </div>
                            </div>
                        </form>
                }


                {this.props.current_user === null ? null :
                    <ul className="navbar-nav ml-auto">
                        <li className="nav-item dropdown">
                            <a onClick={this.handleShowDropDown} className="nav-link" data-toggle="dropdown" href="#">
                                <i className="far fa-comments"/>
                                <span className="badge badge-danger navbar-badge">3</span>
                            </a>
                            <div style={{display:this.state.show_dropdown ? 'block' : 'none'}} className="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <a href="#" className="dropdown-item">
                                    <div className="media">
                                        <img src="../../dist/img/user1-128x128.jpg" alt="User Avatar" className="img-size-50 mr-3 img-circle"/>
                                        <div className="media-body">
                                            <h3 className="dropdown-item-title">
                                                Brad Diesel
                                                <span className="float-right text-sm text-danger"><i className="fas fa-star"/></span>
                                            </h3>
                                            <p className="text-sm">Call me whenever you can...</p>
                                            <p className="text-sm text-muted"><i className="far fa-clock mr-1"/> 4 Hours Ago</p>
                                        </div>
                                    </div>
                                </a>
                                <div className="dropdown-divider"/>
                                <a href="#" className="dropdown-item dropdown-footer">See All Messages</a>
                            </div>
                        </li>
                        <li className="nav-item">
                            <a className="nav-link" href={window.origin + '/logout'}>
                                <i className="fas fa-sign-out-alt"/>
                            </a>
                        </li>
                    </ul>
                }

            </nav>
        );
    }
}