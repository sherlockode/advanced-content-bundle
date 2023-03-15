let notifAlert = function (message) {
    alert(message);
};

let notifConfirm = function (message, callback) {
    if (confirm(message)) {
      callback();
    }
};

export {notifAlert, notifConfirm};
