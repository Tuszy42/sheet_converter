export default {
    inputHolder: document.getElementById('input-holder'),
    fileInfoHolder: document.getElementById('file-info-holder'),
    fileInfo: {
        name: document.getElementById('file-name'),
        size: document.getElementById('file-size'),
    },
    msgContainer: document.getElementById('msg-container'),
    uploadSpinner: document.getElementById('upload-spinner'),
    form(type) { return document.getElementById(`form_${type}`)},
}