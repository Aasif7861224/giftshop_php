// assets/js/app.js
(function(){
  const toasts = document.querySelectorAll('.toast');
  toasts.forEach(t => new bootstrap.Toast(t).show());
})();