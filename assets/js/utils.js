export function getFileExtension(fileName) {
    return /(?<=\.)[a-z0-9]+$/i.exec(fileName)[0];
}

import domElements from './domElements'
export function setErrorMessage(msg) {
    if(msg instanceof Array) {
        domElements.msgContainer.innerText = getDomListFromArray(msg);
    }
    domElements.msgContainer.innerText = msg;
}

export function getDomListFromArray(array) {
    let domList = '<ul>';
    array.forEach(el => {
    domList += `<li>${el}</li>`;
    });
    domList += '</ul>';
    return domList;
}