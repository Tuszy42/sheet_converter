import { getFileExtension } from './utils';
import domElements from "./domElements";
import { fadeOut, fadeIn } from "./animation";
import prettyBytes from "pretty-bytes";
import axios from "axios";

class UploadStateHandler {
    sourceType;
    sizeLimit; //in megabytes
    _file;

    set file(file) {
        if(file){
            domElements.fileInfo.name.innerText = file.name;
            domElements.fileInfo.size.innerText = prettyBytes(file.size);
            fadeOut(domElements.inputHolder, 0.5).then(() => {
                    fadeIn(domElements.fileInfoHolder, 0.5);
                }
            );
        } else {
            fadeOut(domElements.fileInfoHolder, 0.5).then(() => {
                fadeIn(domElements.inputHolder, 0.5);
            });
            domElements.form(this.sourceType).reset();
        }
        this._file = file;
    }

    get file(){
        return this._file;
    }

    set loading(loading) {
    }

    setFile(file) {
        if (this.sourceType === undefined) {
            throw new Error('No sourcetype set');
        }
        /*const ext = getFileExtension(file.name);
        if(ext !== this.sourceType) {
            throw new Error(`The file has to be of type '${this.sourceType}', is '${ext}' instead`);
        }*/
        if(file.size/1000/1000 > this.sizeLimit) {
            throw new Error(`The file size (${file.size/1000/1000}mb) exceeds the limit (${this.sizeLimit}mb)`);
        }
        this.file = file;
    }

    setSourceType(type) {
        this.sourceType = type;
    }

    async upload() {
        const form = domElements.form(this.sourceType);
        const formData = new FormData(form);
        try {
            const response = await axios.post(
                form.getAttribute('action'),
                formData,
                {
                    headers: {
                        'content-type': 'multipart/form-data',
                    },
                },
            );
            const url = window.URL.createObjectURL(new Blob([response.data], { type: response.headers['content-type'] }));
            const link = document.createElement('a');
            link.href = url;
            link.download = response.headers['content-disposition'].split('filename=')[1];
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (err) {
            console.log(err)
        }
    }
}

let _handler;

export default {
    getStateHandler() {
        if (_handler === undefined) {
            _handler = new UploadStateHandler();
        }
        return _handler
    }
}