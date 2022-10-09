

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import './styles/home.scss';
import './styles/register.scss';
// start the Stimulus application
import './bootstrap';
import './components/searchOffers.jsx';
import './components/Offers.jsx';

const $ = require('jquery');

global.$ = global.jQuery = $;

