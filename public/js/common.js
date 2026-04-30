window.route = function (key, replacements = {}) {
    let url = window.Routes[key];
    for (const [k, v] of Object.entries(replacements)) {
        url = url.replace(`__${k.toUpperCase()}__`, v);
    }
    return url;
};

window.Laravel = {
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
};

function csrftoken() {
    return fetch(MAINURL+'/csrf-token', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        window.Laravel.csrfToken = data.token;
        return data.token;
    });
}