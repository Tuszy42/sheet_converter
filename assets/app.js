// custom css
import './styles/_all.scss'

import './js/configAxios'
import uploadState from "./js/uploadState";
import config from './js/config';

uploadState.getStateHandler().sizeLimit = config.UPLOAD_FILESIZE_LIMIT;

import './js/fileUpload';