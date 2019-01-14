export default function init() {
    $('.js-searchbox-content').on('click', (e) => {
        e.stopPropagation();
        $(e.currentTarget).closest('.js-searchbox-form').addClass('searchbox-form--active');
    });

    $(document).on('click', () => {
        $('.js-searchbox-form').removeClass('searchbox-form--active');
    });

    $('.js-searchbox-back').on('click', (e) => {
        $(e.currentTarget).closest('.js-searchbox-form').removeClass('searchbox-form--active');
    });
}
