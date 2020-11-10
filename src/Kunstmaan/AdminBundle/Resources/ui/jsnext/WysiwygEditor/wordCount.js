const CLASSES = {
    closeToLimit: 'limit-close',
    limitExceeded: 'limit-exceeded',
};

export function wordCountConfig({ elementId, maxCharacters }) {
    return {
        onUpdate: (stats) => {
            const container = document.getElementById(`counter-for-${elementId}`);
            const charactersLabel = container.getAttribute('data-characters-label') || 'Characters';
            const isLimitExceeded = stats.characters > maxCharacters;
            const isCloseToLimit = !isLimitExceeded && stats.characters > maxCharacters * 0.8;

            // Display the number of characters. When the limit is exceeded,
            // display how many characters should be removed.
            if (isLimitExceeded) {
                container.textContent = `${charactersLabel}: -${stats.characters - maxCharacters}/${maxCharacters}`;
            } else {
                container.textContent = `${charactersLabel}: ${stats.characters}/${maxCharacters}`;
            }

            // If the content length is close to the character limit, add a CSS class to warn the user.
            container.classList.toggle(CLASSES.closeToLimit, isCloseToLimit);

            // If the character limit is exceeded, add a CSS class that makes the content's color red.
            container.classList.toggle(CLASSES.limitExceeded, isLimitExceeded);
        },
    };
}
