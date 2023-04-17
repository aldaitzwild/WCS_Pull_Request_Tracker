let toastLive = document.getElementById('liveToast');

if (toastLive) {
    toastLive.classList.add('show');

    setTimeout(() => {
        toastLive.classList.add('toast-fade-out');
        setTimeout(() => {
            toastLive.remove();
        }, 1000);
    }, 3000);
}