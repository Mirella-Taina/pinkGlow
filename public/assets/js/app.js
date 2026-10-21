// Pink Glow — comportamentos básicos do app (filtros, busca, voucher)
document.addEventListener('DOMContentLoaded', () => {
  console.log('Pink Glow carregado.');

  const pills = document.querySelectorAll('.category-pill');
  const items = document.querySelectorAll('.product-item');
  const searchInput = document.getElementById('searchInput');
  let activeCategory = 'all';

  function applyFilters() {
    const query = (searchInput?.value || '').trim().toLowerCase();
    items.forEach(item => {
      const matchCategory = activeCategory === 'all' || item.getAttribute('data-category') === activeCategory;
      const matchSearch = !query || (item.getAttribute('data-name') || '').includes(query);
      item.style.display = matchCategory && matchSearch ? 'flex' : 'none';
    });
  }

  pills.forEach(pill => {
    pill.addEventListener('click', () => {
      pills.forEach(p => {
        p.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
        p.classList.add('bg-surface-container', 'text-on-surface-variant');
      });
      pill.classList.remove('bg-surface-container', 'text-on-surface-variant');
      pill.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
      activeCategory = pill.getAttribute('data-category');
      applyFilters();
    });
  });

  if (searchInput) searchInput.addEventListener('input', applyFilters);

  // Botão "Adicionar à sacola" — feedback visual no submit do formulário
  document.querySelectorAll('button[data-add-cart]').forEach(btn => {
    btn.addEventListener('click', function () {
      this.style.transform = 'scale(1.1)';
      setTimeout(() => { this.style.transform = ''; }, 300);
    });
  });

  // Copiar cupom de boas-vindas
  const voucherBtn = document.getElementById('voucherButton');
  const copyIcon = document.getElementById('copyIcon');
  if (voucherBtn) {
    voucherBtn.addEventListener('click', () => {
      navigator.clipboard.writeText('GLOWVIP').then(() => {
        if (copyIcon) copyIcon.textContent = 'check';
        voucherBtn.classList.add('bg-secondary-fixed');
        setTimeout(() => {
          if (copyIcon) copyIcon.textContent = 'content_copy';
          voucherBtn.classList.remove('bg-secondary-fixed');
        }, 1800);
      });
    });
  }
});
