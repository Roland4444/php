window.Menu = (id, _items) => {
    let target = 0;
    let active = 0;
    const divMenu = document.createElement('div');
    divMenu.setAttribute('class', 'menu');

    function createList(items) {
        const ul = document.createElement('ul');
        if (target > 0) {
            ul.setAttribute('id', `list${target}`);
        }

        items.forEach((item) => {
            const li = document.createElement('li');
            li.setAttribute('id', `li${target}`);
            const a = document.createElement('a');
            a.setAttribute('href', item.href ? item.href : '#');
            a.appendChild(document.createTextNode(item.name));
            li.appendChild(a);
            ul.appendChild(li);
            if (item.active) {
                active = target;
                a.setAttribute('class', 'active');
            }
            target += 1;
            if (!item.href) {
                li.setAttribute('class', 'folder');
                a.setAttribute('data-target', `list${target}`);
                const subUl = createList(item.submenu);
                ul.appendChild(subUl);
            }
        });
        return ul;
    }

    const openActiveMenu = () => {
        let activeId = $(`#li${active}`).parent('ul').attr('id');
        while (activeId) {
            const el = $(`#${activeId}`);
            el.addClass('visible');
            activeId = el.parent('ul').attr('id');
        }
    };

    const setHandlerMenu = () => {
        $('.menu ul li a').on('click', (event) => {
            target = event.target.getAttribute('data-target');
            if (target) {
                $(`#${target}`).toggleClass('visible');
                event.preventDefault();
            }
        });
        openActiveMenu();
    };

    $(document).ready(() => {
        setHandlerMenu();
    });

    const parentUl = createList(_items);
    divMenu.appendChild(parentUl);
    document.getElementById(id).appendChild(divMenu);
};

/* function activateMenu(item) {
    console.log(item);
    if (item.length === 0) {
        return;
    }
    const n = item.closest('ul');
    n.addClass('active').collapse('show');

    if (!n.hasClass('left-nav')) {
        activateMenu($(n.parent('ul')));
    }
}

$(document).ready(() => {
    activateMenu($('a.current'));
}); */
