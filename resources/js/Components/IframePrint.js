import React from 'react';

export default class IframePrint extends React.Component{
    constructor(props){
        super(props);
    }
    render() {
        return (
            this.props.print_url === null ?
                <div className="text-center text-right"><i className="fas fa-triangle-exclamation"/><br/>Data tidak ditemukan</div>
                :
                <iframe id="iframe-print" src={this.props.print_url} height={500} width="100%" style={{border:'solid 1px gray'}} />
        );
    }
}