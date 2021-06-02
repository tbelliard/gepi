/* *********************************************
 *
 *		Classe pour gérer les cookies
 *		avec prototype
 *		http://codeinthehole.com/archives/5-Javascript-cookie-objects-using-Prototype-and-JSON.html
 *
 *********************************************** */
const Cookies = Class.create({
    initialize: function (path, domain) {
        this.path = path || '/';
        this.domain = domain || null;
    },
    // Sets a cookie
    set: function (key, value, days) {
        let setExpiration;
        if (typeof key != 'string') {
            throw "Invalid key";
        }
        if (typeof value != 'string' && typeof value != 'number') {
            throw "Invalid value";
        }
        if (days && typeof days != 'number') {
            throw "Invalid expiration time";
        }
        const setValue = key + '=' + escape(String(value));
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            setExpiration = "; expires=" + date.toGMTString();
        } else {
            setExpiration = "";
        }
        const setPath = '; path=' + ('/');
        const setDomain = (this.domain) ? '; domain=' + escape(this.domain) : '';
        var cookieString = setValue + setExpiration + setPath + setDomain;
        document.cookie = cookieString;
    },
    // Returns a cookie value or false
    get: function (key) {
        const keyEquals = key + "=";
        let value = false;
        document.cookie.split(';').invoke('strip').each(function (s) {
            if (s.startsWith(keyEquals)) {
                value = unescape(s.substring(keyEquals.length, s.length));
                throw $break;
            }
        });
        return value;
    },
    // Clears a cookie
    clear: function (key) {
        this.set(key, '');
    },
    // Clears all cookies
    clearAll: function () {
        document.cookie.split(';').collect(function (s) {
            return s.split('=').first().strip();
        }).each(function (key) {
            this.clear(key);
        }.bind(this));
    }
});
