(function () {
    'use strict';

    var POLL_INTERVAL_MS = 15000;

    function formatViews(value) {
        return Number(value || 0).toLocaleString();
    }

    function collectPostIds() {
        var ids = [];
        document.querySelectorAll('[data-post-id]').forEach(function (el) {
            var id = el.getAttribute('data-post-id');
            if (id && ids.indexOf(id) === -1) {
                ids.push(id);
            }
        });
        return ids;
    }

    function updateViewElements(posts) {
        document.querySelectorAll('[data-post-id]').forEach(function (el) {
            var id = el.getAttribute('data-post-id');
            if (!id || typeof posts[id] === 'undefined') {
                return;
            }

            var nextValue = posts[id];
            var currentValue = parseInt(el.getAttribute('data-views') || el.textContent.replace(/,/g, ''), 10) || 0;

            if (currentValue !== nextValue) {
                el.textContent = formatViews(nextValue);
                el.setAttribute('data-views', String(nextValue));
                el.classList.add('views-updated');
                window.setTimeout(function () {
                    el.classList.remove('views-updated');
                }, 1200);
            }
        });
    }

    function updateTotalViews(total) {
        var el = document.getElementById('total-views-stat');
        if (!el || typeof total === 'undefined') {
            return;
        }

        var current = parseInt(el.getAttribute('data-views') || el.textContent.replace(/,/g, ''), 10) || 0;
        if (current !== total) {
            el.textContent = formatViews(total);
            el.setAttribute('data-views', String(total));
            el.classList.add('views-updated');
            window.setTimeout(function () {
                el.classList.remove('views-updated');
            }, 1200);
        }
    }

    function refreshViews() {
        var ids = collectPostIds();
        var url = 'api/post-views.php';
        if (ids.length) {
            url += '?ids=' + encodeURIComponent(ids.join(','));
        }

        fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        })
            .then(function (res) {
                if (!res.ok) {
                    throw new Error('Request failed');
                }
                return res.json();
            })
            .then(function (data) {
                if (!data.success) {
                    return;
                }
                updateViewElements(data.posts || {});
                updateTotalViews(data.total_views);
            })
            .catch(function () {
                /* silent fail — keep showing last known counts */
            });
    }

    function initShareMenus() {
        document.addEventListener('click', function (event) {
            var trigger = event.target.closest('.share-trigger');
            var copyBtn = event.target.closest('.share-copy');

            if (copyBtn) {
                event.preventDefault();
                var url = copyBtn.getAttribute('data-copy-url');
                if (!url) {
                    return;
                }

                var finish = function (message) {
                    var original = copyBtn.textContent;
                    copyBtn.textContent = message;
                    window.setTimeout(function () {
                        copyBtn.textContent = original;
                    }, 1600);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        finish('Copied!');
                    }).catch(function () {
                        finish('Copy failed');
                    });
                } else {
                    var input = document.createElement('input');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    try {
                        document.execCommand('copy');
                        finish('Copied!');
                    } catch (e) {
                        finish('Copy failed');
                    }
                    document.body.removeChild(input);
                }

                return;
            }

            document.querySelectorAll('.share-wrap.is-open').forEach(function (wrap) {
                if (!wrap.contains(event.target)) {
                    wrap.classList.remove('is-open');
                    var menu = wrap.querySelector('.share-menu');
                    var btn = wrap.querySelector('.share-trigger');
                    if (menu) {
                        menu.hidden = true;
                    }
                    if (btn) {
                        btn.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            if (!trigger) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            var wrap = trigger.closest('.share-wrap');
            var menu = wrap ? wrap.querySelector('.share-menu') : null;
            if (!wrap || !menu) {
                return;
            }

            var isOpen = wrap.classList.contains('is-open');
            document.querySelectorAll('.share-wrap.is-open').forEach(function (openWrap) {
                openWrap.classList.remove('is-open');
                var openMenu = openWrap.querySelector('.share-menu');
                var openBtn = openWrap.querySelector('.share-trigger');
                if (openMenu) {
                    openMenu.hidden = true;
                }
                if (openBtn) {
                    openBtn.setAttribute('aria-expanded', 'false');
                }
            });

            if (!isOpen) {
                wrap.classList.add('is-open');
                menu.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    }

    function initLiveViews() {
        if (!document.querySelector('[data-post-id], #total-views-stat')) {
            return;
        }

        refreshViews();
        window.setInterval(refreshViews, POLL_INTERVAL_MS);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initShareMenus();
        initLiveViews();
    });
})();
