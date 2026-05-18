(function () {
    // Header : toggle du menu burger
    var burger = document.getElementById('header-burger');
    var nav = document.getElementById('header-nav');
    var actions = document.getElementById('header-actions');

    burger.addEventListener('click', function () {
        var isOpen = burger.getAttribute('aria-expanded') === 'true';
        burger.setAttribute('aria-expanded', String(!isOpen));
        nav.classList.toggle('header__nav--open');
        actions.classList.toggle('header__actions--open');
    });

    // Page book-edit.php : preview de l'image uploadée
    var photoInput = document.getElementById('photo');

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('book-edit-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}());
