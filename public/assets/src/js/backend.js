import $ from 'jquery';
window.$ = window.jQuery = $;
global.$ = global.jQuery = $;

import 'bootstrap';
import 'popper.js';
import 'webpack-jquery-ui';
import 'html5sortable';
import 'jquery-minicolors';
import 'jquery-form';
import 'jquery-datetimepicker'
import 'bootstrap-touchspin'

require('underscore');
require('rrssb');
require('./attendize.js');

import humane from 'humane-js';
console.log(humane);
window.humane = humane;