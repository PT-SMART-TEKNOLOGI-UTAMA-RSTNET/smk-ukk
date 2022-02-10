import React from 'react';

export default class BreadCrumbs extends React.Component{
    constructor(props){
        super(props);
        this.state = {
            breadcrumbs : []
        };
    }
    componentWillReceiveProps(props){
        if (props.breadcrumbs !== null){
            this.setState({breadcrumbs:props.breadcrumbs});
        }
    }
    render(){
        return (
            <>
                <section className="content-header">
                    <div className="container-fluid">
                        <div className="row mb-2">
                            <div className="col-sm-6">
                            </div>
                            <div className="col-sm-6">
                                <ol className="breadcrumb float-sm-right">
                                    <li className="breadcrumb-item"><a href={window.origin}>Home</a></li>
                                    {this.state.breadcrumbs.map((item,index)=>
                                        <li key={index} className="breadcrumb-item"><a href={item.url}>{item.label}</a></li>
                                    )}
                                    <li className="breadcrumb-item active">{this.props.title}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>
            </>
        );
    }
}