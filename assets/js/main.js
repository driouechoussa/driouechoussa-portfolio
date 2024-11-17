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


var certification_cover = document.querySelectorAll('.cert_cover');
var certification_preview = document.getElementById('cert_preview');

certification_cover.forEach(el => {
    el.addEventListener('click' , function() {
        certification_preview.style.visibility = 'visible';
    });

});

function closeCertPreview() {
    certification_preview.style.visibility = 'hidden';
}


function showLoading() {
    const loadingElement = document.createElement('div');
    loadingElement.id = 'loading';
    loadingElement.innerHTML = '<div class="spinner"></div>'; // Spinner inside the div
    document.body.appendChild(loadingElement);
  }
  
  // Function to hide the loading screen after 5 seconds
  function hideLoading() {
    const loadingElement = document.getElementById('loading');
    if (loadingElement) {
      setTimeout(() => {
        loadingElement.style.display = 'none';
      }, 1500); 
    }
  }
  
  // Show loading screen immediately on page load
  window.addEventListener('DOMContentLoaded', showLoading);
  
  // Hide loading screen after 5 seconds when content is fully loaded
  window.addEventListener('load', hideLoading);
  
  


