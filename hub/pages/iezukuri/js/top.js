(function () {
  const selector = document.querySelector('[data-iez-life-selector]');
  if (!selector) return;

  const buttons = selector.querySelectorAll('[data-life-target]');
  const panels = document.querySelectorAll('[data-life-panel]');

  function activate(key) {
    buttons.forEach((button) => {
      button.classList.toggle('is-active', button.dataset.lifeTarget === key);
    });

    panels.forEach((panel) => {
      panel.classList.toggle('is-active', panel.dataset.lifePanel === key);
    });
  }

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      activate(button.dataset.lifeTarget);
    });
  });
})();
