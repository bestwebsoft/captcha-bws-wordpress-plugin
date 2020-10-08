import "react-app-polyfill/ie11";
import "react-app-polyfill/stable";
import React from "react";
import ReactDOM from "react-dom";
import App from "./components/App.js";


const cptch_containers = document.querySelectorAll('#cptch_slide_captcha_container');

for ( let item of cptch_containers ) {
	ReactDOM.render( <App/>, item );
}