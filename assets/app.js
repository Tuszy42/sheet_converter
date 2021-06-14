// custom css
import './styles/_all.scss'
//vendor imports
import '@fortawesome/fontawesome-free/js/brands'
import '@fortawesome/fontawesome-free/js/solid'
import '@fortawesome/fontawesome-free/js/fontawesome'

import uploadState from "./js/uploadState";
import config from './js/config';

uploadState.getStateHandler().sizeLimit = config.UPLOAD_FILESIZE_LIMIT;

import './js/fileUpload';