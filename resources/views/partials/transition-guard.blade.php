{{-- Runs before pagereveal: a page transition the browser cancels (Back, a fast second click, a form redirect) is not an error. --}}
<script>
    addEventListener('pagereveal', function (e) {
        var vt = e.viewTransition;
        if (vt) [vt.ready, vt.finished, vt.updateCallbackDone].forEach(function (p) { p && p.catch(function () {}); });
    });
</script>
