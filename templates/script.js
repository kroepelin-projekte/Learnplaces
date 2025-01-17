(function() {
  const accordion_containers = Array.from(document.querySelectorAll('.accordion-container'));
  accordion_containers.forEach((accordion_container) => {
    const accordion_toggle = accordion_container.querySelector('.accordion-toggle');
    const accordion_content = accordion_container.querySelector('.accordion-content');
    accordion_toggle.addEventListener('click', () => {
      accordion_content.classList.toggle('in');
    });
  });
})();