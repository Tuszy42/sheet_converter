export function fadeOut(domElement, time = 1) {
    return new Promise((resolve) => {
        if(!domElement.style.opacity) {
            domElement.style.opacity = 1;
        }
        domElement.style.transition = `opacity ${time}s`;
        domElement.addEventListener('transitionend', event => {
            domElement.style.display = 'none';
            domElement.style.transition = 'initial';
            resolve();
        });
        domElement.style.opacity = 0;
    });
}

export function fadeIn(domElement, time = 1) {
    return new Promise((resolve) => {
        if(!domElement.style.opacity) {
            domElement.style.opacity = 0;
        }
        if(domElement.style.display === 'none'){
            if(domElement.classList.contains('row')){
                domElement.style.display = 'flex';
            } else {
                domElement.style.display = 'block';
            }
        }
        domElement.style.transition = `opacity ${time}s`;
        domElement.addEventListener('transitionend', event => {
            domElement.style.transition = 'initial';
            resolve();
        });
        domElement.style.opacity = 1;
    });
}