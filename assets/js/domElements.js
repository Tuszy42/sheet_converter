export default {
    inputHolder: document.getElementById('input-holder'),
    fileInfoHolder: document.getElementById('file-info-holder'),
    fileInfo: {
        name: document.getElementById('file-name'),
        size: document.getElementById('file-size'),
    },
    form(type) { return document.getElementById(`form_${type}`)},
}