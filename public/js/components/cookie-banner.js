/**
 * Cookie Consent Banner Component
 */
(function () {
    'use strict';

    const COOKIE_CONSENT_KEY = 'cookie_consent';
    const banner = document.getElementById('cookie-banner');
    const acceptBtn = document.getElementById('cookie-accept');
    const declineBtn = document.getElementById('cookie-decline');

    function getConsent() {
        return localStorage.getItem(COOKIE_CONSENT_KEY);
    }

    function setConsent(value) {
        localStorage.setItem(COOKIE_CONSENT_KEY, value);
    }

    function hideBanner() {
        if (banner) {
            banner.style.display = 'none';
        }
    }

    function showBanner() {
        if (banner) {
            banner.style.display = 'block';
        }
    }

    function init() {
        const consent = getConsent();

        if (consent === null) {
            showBanner();
        }

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                setConsent('accepted');
                hideBanner();
            });
        }

        if (declineBtn) {
            declineBtn.addEventListener('click', function () {
                setConsent('declined');
                hideBanner();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
