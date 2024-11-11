var navBar = document.getElementById('navBar');
const addClass = navBar.classList;

window.addEventListener('scroll' , function() {
    var ScrollValue = window.scrollY;
    console.log(ScrollValue);
    
    if (ScrollValue > 70) {
        addClass.add('fixed-top');
    }
    else {
        addClass.remove('fixed-top');
    }
})