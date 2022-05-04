const $ = require("jquery");
global.$ = global.jQuery = $;
import "bootstrap";


import "bootstrap/dist/css/bootstrap.css";
import('bootstrap-icons/font/bootstrap-icons.css');
// start the Stimulus application
import "./bootstrap";

require("../assets/js/customfileinput")
require("../assets/js/bootstrap-icons-add-aria")
require('../assets/js/alt_for_img')
require('../assets/js/flash_message')


require('../assets/js/glightbox')
require('../assets/js/aos')

import "./styles/app.scss";
require('../assets/styles/main.scss')
require('../assets/styles/variables.css')
import "./jssite/main.js"

require('../assets/js/tippy')