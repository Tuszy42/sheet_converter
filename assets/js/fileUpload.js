import stateHandler from './uploadState';
import{ setErrorMessage } from './utils'

const uploadState = stateHandler.getStateHandler();

/**
 * Register listeners
 */
// trigger file selection screen on button press
document.querySelectorAll('.file-upload>.button').forEach(button => {
    button.addEventListener('click', event => {
        const btnCorrespondingInputName = button.getAttribute('data-activator-for')
        const input = document.querySelector(`input[type='file'][data-convert-from='${btnCorrespondingInputName}']`);
        input.click();
    });
});
// inspect selected file before upload
document.getElementsByClassName('file-upload__input').forEach(input => {
    input.addEventListener('change', event => {
        try {
            const [selectedFile] = event.target.files;
            uploadState.setSourceType(input.getAttribute('data-convert-from'));
            uploadState.setFile(selectedFile);
        } catch (e) {
            setErrorMessage(e);
        }
    });
});
document.getElementById('btn-cancel').addEventListener('click', () => uploadState.file=null);
document.getElementById('btn-convert').addEventListener('click', () => uploadState.upload());