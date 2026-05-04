(function () {
    var burger = document.getElementById('header-burger');
    var nav = document.getElementById('header-nav');
    var actions = document.getElementById('header-actions');

    burger.addEventListener('click', function () {
        var isOpen = burger.getAttribute('aria-expanded') === 'true';
        burger.setAttribute('aria-expanded', String(!isOpen));
        nav.classList.toggle('header__nav--open');
        actions.classList.toggle('header__actions--open');
    });
}());
