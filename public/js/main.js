var navBar = document.getElementById('navBar');
const addClass = navBar.classList;

window.addEventListener('scroll' , function() {
    var ScrollValue = window.scrollY;

    
    if (ScrollValue > 70) {
        addClass.add('fixed-top');
    }
    else {
        addClass.remove('fixed-top');
    }
})












document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', event => {
    event.preventDefault(); // Prevent the default anchor behavior
    const targetId = link.getAttribute('href').substring(1); // Get the target section ID
    const targetSection = document.getElementById(targetId);

    if (targetSection) {
      targetSection.scrollIntoView({ behavior: 'smooth' }); // Scroll smoothly to the section
    }
  });
});


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
  


 const btnDetails = document.querySelectorAll('.btnProjectDetail').forEach(elDetails => {
    elDetails.addEventListener('click' , function() {
      document.getElementById('detailsBottomSheet').classList.add('activeSheet');

      if(elDetails) {
       var title =  elDetails.getAttribute('data-title');
       var picUrl = elDetails.getAttribute('data-imgUrl');
       var description = elDetails.getAttribute('data-description');
       var GithubLink = elDetails.getAttribute('data-Githublink');
       var LiveDemo = elDetails.getAttribute('data-liveDemoLink');

       document.getElementById('sheetPic').src = picUrl;
       document.getElementById('sheetTitle').innerText = title;
       document.getElementById('sheetDescription').innerText = description;

       document.getElementById('btnGithub').setAttribute(
        'href', GithubLink
       );
       document.getElementById('btnLDemo').setAttribute(
        'href', LiveDemo
       );
      }
    });
  });

  function detailSheetCloser() {
    document.getElementById('detailsBottomSheet').classList.remove('activeSheet');
  }

