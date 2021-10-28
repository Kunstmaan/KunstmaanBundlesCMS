function renderViewSelectOptions(select, data) {
    const OPTION_NAMES = Object.keys(data);
    select.innerHTML = '';

    OPTION_NAMES.forEach((name, i) => {
        const option = document.createElement('option');
        option.value = name;
        option.textContent = name;

        if (i === 0) {
            option.selected = true;
        }

        select.appendChild(option);
    });

    select.disabled = OPTION_NAMES.length === 1;
}

export { renderViewSelectOptions };
