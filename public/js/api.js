const api = (() => {
    const BASE = '/api';

    async function request(method, path, body = null) {
        const options = {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        };
        if (body !== null) {
            options.body = JSON.stringify(body);
        }

        const response = await fetch(`${BASE}${path}`, options);
        const json = await response.json();

        if (json.error && json.error.message) {
            const err = new Error(json.error.message);
            err.code = json.error.code;
            err.details = json.error.details;
            err.status = response.status;
            throw err;
        }

        return { data: json.data, meta: json.meta };
    }

    return {
        get:    (path, params = {}) => {
            const qs = Object.keys(params).length ? '?' + new URLSearchParams(params).toString() : '';
            return request('GET', path + qs);
        },
        post:   (path, body) => request('POST', path, body),
        put:    (path, body) => request('PUT', path, body),
        delete: (path) => request('DELETE', path),
    };
})();

window.api = api;
