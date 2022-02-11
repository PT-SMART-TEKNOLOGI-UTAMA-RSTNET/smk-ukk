import React from 'react';

export default class MainSideBar extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            token : localStorage.getItem('access_token'),
            menus : [],
            avatar : null,
            current_url : ''
        };
        this.generateMenu = this.generateMenu.bind(this);
    }
    componentDidMount(){
        let current_url = window.location.href;
        current_url = current_url.split('/');
        if (current_url.length >= 4){
            current_url = current_url[current_url.length - 1];
            current_url = current_url.replaceAll('#','');
            current_url = current_url.replaceAll('?','');
            this.setState({current_url});
            console.log(current_url);
        }
    }
    componentWillReceiveProps(props){
        if (props.current_user !== null){
            this.setState({avatar:props.current_user.meta.avatar});
            this.generateMenu(props.current_user);
        }
    }
    generateMenu(current_user){
        let menus = this.state.menus;
        switch (current_user.meta.level){
            default :
                menus = [];
                break;
            case 'admin' :
                menus = [
                    { value : 'Paket Soal', icon : 'fa-boxes-stacked', url : window.origin + '/master/packages', route_name : 'packages', childrens : [] },
                    { value : 'Jadwal Ujian', icon : 'fa-calendar-check', url : window.origin + '/master/schedules', route_name : 'schedules', childrens : [] },
                    { value : 'Nilai', icon : 'fa-tasks', url : window.origin + '', route_name : 'configs', childrens : [
                            { value : 'Data Nilai', icon : 'fa-blender-phone', url : window.origin + '/nilai', route_name : 'configs/app' },
                        ] }
                ];
                break;
        }
        this.setState({menus});
    }
    render(){
        return (
            <aside className="main-sidebar sidebar-dark-primary elevation-4">
                <a href={window.origin} className="brand-link text-sm">
                    <img src={window.origin+'/uploads/images/logo.png'} className="brand-image img-circle elevation-3" style={{opacity: .8}}/>
                    <span className="brand-text font-weight-light">UJI KOMPETENSI</span>
                </a>
                <div className="sidebar">
                    {this.props.current_user === null ? null :
                        <div className="user-panel mt-3 pb-3 mb-3 d-flex">
                            <div className="image">
                                <img src={this.state.avatar} className="img-circle elevation-2" alt="User Image"/>
                            </div>
                            <div className="info">
                                <a href="#" className="d-block">{this.props.current_user === null ? '' : this.props.current_user.label }</a>
                            </div>
                        </div>
                    }

                    <nav className="mt-2">
                        <ul className="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li className="nav-item">
                                <a href={window.origin} className={this.state.current_url === '' ? 'nav-link active' : 'nav-link' }>
                                    <i className="nav-icon fas fa-home"/>
                                    <p>Beranda</p>
                                </a>
                            </li>
                            {this.state.menus.map((item,index)=>
                                <li key={item.icon + '-' + index}
                                    className={
                                        item.childrens.findIndex((e) => e.route_name === this.state.current_url) >= 0 ?
                                            item.childrens.length === 0 ? 'menu-open nav-item' : 'menu-open nav-item has-treeview'
                                            :
                                            '' + item.childrens.length === 0 ? 'nav-item' : 'nav-item has-treeview'
                                    }>
                                    <a href={item.childrens.length === 0 ? item.url : '#'}
                                       className={
                                           item.route_name === this.state.current_url ? 'nav-link active' : 'nav-link'
                                       }>
                                        <i className={'nav-icon fas ' + item.icon}/>
                                        <p>
                                            { item.value }
                                            { item.childrens.length === 0 ? '' : <i className="fas fa-angle-left right"/> }
                                        </p>
                                    </a>
                                    {
                                        item.childrens.length === 0 ? '' :
                                            <ul className="nav nav-treeview">
                                                {item.childrens.map((children,indexChild) =>
                                                    <li className="nav-item" key={indexChild + '-' + children.value}>
                                                        <a href={children.url} className={children.route_name === this.state.current_url ? 'nav-link active' : 'nav-link'}>
                                                            <i className={'nav-icon fas ' + children.icon }/>
                                                            <p>{children.value}</p>
                                                        </a>
                                                    </li>
                                                )}
                                            </ul>
                                    }
                                </li>
                            )}
                        </ul>
                    </nav>
                </div>
            </aside>
        );
    }
}