$(() => {
    $('.confirm').click((e) => {
        e.preventDefault();
        /* global bootbox */
        bootbox.confirm('Подтвердить действие?', (agree) => {
            if (agree) {
                window.location = $(e.target).parent('a').attr('href');
            }
        });
    });

    $.datepicker.regional.ru = {
        closeText: 'Закрыть',
        prevText: '&#x3c;Пред',
        nextText: 'След&#x3e;',
        currentText: 'Сегодня',
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
        'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
        'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
        dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        weekHeader: 'Не',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        showOtherMonths: true,
        selectOtherMonths: true,
        yearSuffix: '',
    };
    $.datepicker.setDefaults($.datepicker.regional.ru);
});

window.setYesterday = () => {
    const today = new Date();
    today.setDate(today.getDate() - 1);
    let month = today.getMonth() + 1;
    if (month < 10) {
        month = `0${month}`;
    }
    let day = today.getDate();
    if (day < 10) {
        day = `0${day}`;
    }
    const fmt = `${today.getFullYear()}-${month}-${day}`;
    $('#date').val(fmt);
};

window.setToday = () => {
    const today = new Date();
    let month = today.getMonth() + 1;
    if (month < 10) {
        month = `0${month}`;
    }
    let day = today.getDate();
    if (day < 10) {
        day = `0${day}`;
    }
    const fmt = `${today.getFullYear()}-${month}-${day}`;
    $('#date').val(fmt);
};
