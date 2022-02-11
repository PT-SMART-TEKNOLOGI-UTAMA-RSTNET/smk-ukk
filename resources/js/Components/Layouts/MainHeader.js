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