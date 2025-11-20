import './bootstrap';
import '../sass/app.scss';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap; // Make bootstrap globally available

// Import and set jQuery globally
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

import select2 from "select2"
select2(); 

// Import DataTable and initialize it with jQuery
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5'
import 'datatables.net-responsive-bs5'
$.fn.DataTable = DataTable;
