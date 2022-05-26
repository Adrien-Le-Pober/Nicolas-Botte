//MENU TOGGLE

const menuToggle = document.querySelector('.toggle');
const showcase = document.querySelector('.showcase');

menuToggle.addEventListener('click', () => {
  menuToggle.classList.toggle('active');
  showcase.classList.toggle('active');
})

function disableToggleOnResize() {
  if (window.innerWidth > 992) {
    menuToggle.classList.remove('active');
    showcase.classList.remove('active');
  }
}

window.addEventListener('resize', disableToggleOnResize);

