// Auto-uppercases text typed into any text-like input/textarea inside the
// teacher module (DepEd records are conventionally entered in all caps).
// Attached on document with capturing so it works for fields rendered later
// (modal tabs, dynamically added rows) without needing to re-bind anything.
(function(){
    var SKIP_TYPES = [
        'email', 'password', 'number', 'date', 'file', 'hidden',
        'checkbox', 'radio', 'range', 'color', 'month', 'week',
        'time', 'datetime-local', 'url', 'submit', 'button', 'image', 'reset'
    ];

    document.addEventListener('input', function(e){
        var el = e.target;
        if(!el.closest || !el.closest('.teacher-portal')) return;
        if(el.tagName !== 'INPUT' && el.tagName !== 'TEXTAREA') return;
        if(el.tagName === 'INPUT' && SKIP_TYPES.indexOf(el.type) !== -1) return;
        if(el.classList.contains('no-uppercase')) return;

        var upper = el.value.toUpperCase();
        if(upper === el.value) return;

        var start = el.selectionStart;
        var end = el.selectionEnd;
        el.value = upper;
        if(start !== null && end !== null){
            el.setSelectionRange(start, end);
        }
    }, true);
})();
