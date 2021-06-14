export function getFileExtension(fileName) {
    return /(?<=\.)[a-z]+$/.exec(fileName)[0];
}

export function setErrorMessage(msg) {
    document.getElementById('msg-container').innerText = msg;
}