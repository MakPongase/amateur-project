window.addEventListener('scroll', function() {
    var navbar = document.querySelector('nav');
    navbar.classList.toggle('scrolled', window.scrollY > 0);
});