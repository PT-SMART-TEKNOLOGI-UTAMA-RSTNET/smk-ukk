import React from 'react';

export default class MainFooter extends React.Component{
    constructor(props){
        super(props);
    }
    render(){
        return (
            <footer className="main-footer">
                <div className="float-right d-none d-sm-block">
                    <b>Version</b> 20220209
                </div>
                <strong>Copyright &copy; 2022 <a href="https://rst.net.id">RSTNET</a>.</strong> All rights reserved.
            </footer>
        )
    }
}