document.addEventListener('focusin', (e) => {
    const input = e.target;
    if (input instanceof HTMLInputElement && input.dataset.clearOnFocus === '1') {
        if (input.dataset.originalValue === undefined) {
            input.dataset.originalValue = input.value;
        }
        input.dataset.userTyped = '0';
        input.value = '';
    }
});

document.addEventListener('input', (e) => {
    const input = e.target;
    if (input instanceof HTMLInputElement && input.dataset.clearOnFocus === '1') {
        input.dataset.userTyped = '1';
    }
});

document.addEventListener('blur', (e) => {
    const input = e.target;
    if (input instanceof HTMLInputElement && input.dataset.clearOnFocus === '1') {
        const typed = input.dataset.userTyped === '1';
        if (input.value === '' && !typed && input.dataset.originalValue !== undefined) {
            input.value = input.dataset.originalValue;
        }
        delete input.dataset.originalValue;
        delete input.dataset.userTyped;
    }
}, true);
