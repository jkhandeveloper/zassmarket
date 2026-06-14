

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const submitButtons = (form) => Array.from(form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"]'));

const resetSubmitState = (form) => {
    form.dataset.submitting = 'false';
    form.removeAttribute('aria-busy');

    submitButtons(form).forEach((button) => {
        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
            delete button.dataset.originalHtml;
        }

        if (button.dataset.originalValue) {
            button.value = button.dataset.originalValue;
            delete button.dataset.originalValue;
        }

        button.disabled = false;
        button.classList.remove('is-loading');
        button.removeAttribute('aria-disabled');
    });
};

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || form.dataset.noSubmitLock === 'true') {
        return;
    }

    if (form.dataset.submitting === 'true') {
        event.preventDefault();

        return;
    }

    form.dataset.submitting = 'true';
    form.setAttribute('aria-busy', 'true');

    const submitter = event.submitter instanceof HTMLElement ? event.submitter : null;
    const buttons = submitButtons(form);

    buttons.forEach((button) => {
        button.classList.add('is-loading');
        button.setAttribute('aria-disabled', 'true');

        if (button instanceof HTMLButtonElement) {
            button.dataset.originalHtml = button.innerHTML;

            const hasVisibleText = button.textContent.trim().length > 0;
            button.innerHTML = hasVisibleText
                ? '<span class="zm-submit-spinner" aria-hidden="true"></span><span>Loading...</span>'
                : '<span class="zm-submit-spinner" aria-hidden="true"></span><span class="sr-only">Loading</span>';
        }

        if (button instanceof HTMLInputElement) {
            button.dataset.originalValue = button.value;
            button.value = 'Loading...';
        }

        button.disabled = true;
    });

    if (submitter && ! buttons.includes(submitter)) {
        submitter.setAttribute('aria-disabled', 'true');
    }
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submitting="true"]').forEach(resetSubmitState);
});
