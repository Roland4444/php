window.allCustomers = function (url) {
    $('#all_customers').on('click', function (event) {
        if (event.preventDefault) {
            event.preventDefault();
        } else {
            event.returnValue = false;
        }
        let select = document.getElementById('customer');
        select.innerHTML = "";

        $.post(url, {},
            function (data) {
                for (let i = 0; i < data.length; i++) {
                    let item = data[i];

                    let opt = document.createElement('option');
                    opt.value = item['id'];
                    opt.innerHTML = item['name'];
                    select.appendChild(opt);
                }
            }, 'json');
    });
};