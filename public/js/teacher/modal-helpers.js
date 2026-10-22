/**
 * Shared, generic modal/interaction behavior for the teacher portal.
 * Additive only -- every existing open/close trigger (data-bs-toggle,
 * onclick handlers, fetch calls) keeps working exactly as before. This
 * file only layers extra UX on top: tooltips, a "discard unsaved
 * changes?" guard, and a consistent loading/disabled state on submit
 * buttons so forms can't be double-submitted.
 */

/**
 * Initializes Bootstrap tooltips for every [data-bs-toggle="tooltip"] under
 * `root` that doesn't already have one. Safe to call repeatedly (e.g. after
 * a table row is re-rendered via innerHTML, which wipes any tooltip
 * instances that were attached to the old nodes).
 */
function initTeacherTooltips(root){
    (root || document).querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el){
        if(!bootstrap.Tooltip.getInstance(el)) new bootstrap.Tooltip(el)
    })
}

document.addEventListener('DOMContentLoaded', function(){
    initTeacherTooltips(document)

    // role="dialog"/aria-modal aren't added by Bootstrap automatically --
    // set them once here instead of hand-editing every modal's markup.
    document.querySelectorAll('.modal').forEach(function(modalEl){
        modalEl.setAttribute('role', 'dialog')
        modalEl.setAttribute('aria-modal', 'true')
    })
})

/**
 * Tracks whether the user has typed/changed anything inside the given
 * form selectors since the last "clean" point (modal opened, or a save
 * just succeeded). Programmatic population (el.value = ...) does NOT
 * fire input/change events, so pre-filling a form for Edit never causes
 * a false "unsaved changes" prompt -- only genuine user interaction does.
 *
 * Call guard.markClean() right after a successful save, and
 * guard.setSubmitting(true/false) around an in-flight request so ESC/
 * backdrop-click can't interrupt a save that's already underway.
 */
function watchUnsavedChanges(modalEl, formSelectors){
    let dirty = false
    let submitting = false

    function markDirty(){ dirty = true }
    function markClean(){ dirty = false }

    formSelectors.forEach(function(sel){
        const form = modalEl.querySelector(sel)
        if(!form) return
        form.addEventListener('input', markDirty)
        form.addEventListener('change', markDirty)
    })

    modalEl.addEventListener('shown.bs.modal', markClean)

    modalEl.addEventListener('hide.bs.modal', function(e){
        if(submitting){
            e.preventDefault()
            return
        }
        if(dirty){
            e.preventDefault()
            Swal.fire({
                icon: 'warning',
                title: 'Discard unsaved changes?',
                text: 'Changes you made here haven\'t been saved yet.',
                showCancelButton: true,
                confirmButtonText: 'Discard changes',
                confirmButtonColor: 'var(--danger-strong)',
                cancelButtonText: 'Keep editing',
                cancelButtonColor: 'var(--primary)'
            }).then(function(result){
                if(result.isConfirmed){
                    dirty = false
                    bootstrap.Modal.getInstance(modalEl).hide()
                }
            })
        }
    })

    return {
        markClean: markClean,
        setSubmitting: function(value){ submitting = value }
    }
}

/**
 * Puts a submit button into/out of a loading state (disabled + spinner)
 * around an in-flight request, so double-clicking Save can't fire the
 * request twice. Disabling is deferred to the next tick -- disabling a
 * submit button synchronously inside its own form's submit handler would
 * drop that button's name=value pair from the actual request (the
 * browser serializes the submission after the submit event finishes).
 */
function setButtonLoading(btn, loading, loadingText){
    if(!btn) return
    if(loading){
        // Captured fresh every time (not cached) -- this button's label
        // can legitimately change between calls, e.g. "Add" vs "Update".
        btn.dataset.originalText = btn.innerHTML
        btn.innerHTML = '<span class="spinner-border spinner-border-sm btn-spinner me-1" role="status" aria-hidden="true"></span>' + (loadingText || 'Saving...')
        setTimeout(function(){ btn.disabled = true }, 0)
    }else{
        btn.disabled = false
        if(btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText
    }
}
