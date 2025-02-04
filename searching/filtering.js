function toggleFilters(filterType) {
  const hiddenFilters = document.getElementById('hidden-filters-' + filterType);
  const toggleButton = document.getElementById('toggle-filters-' + filterType);

  if (hiddenFilters.style.display === 'none') {
      hiddenFilters.style.display = 'block';
      toggleButton.innerText = 'ซ่อน'; 
  } else {
      hiddenFilters.style.display = 'none';
      toggleButton.innerText = 'แสดง';
  }
}

function openFilterDrawer() {
  document.querySelector('.filter-container').classList.add('active');
  document.querySelector('.drawer-overlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeFilterDrawer() {
  // Don't clear the form - just close the drawer
  document.querySelector('.filter-container').classList.remove('active');
  document.querySelector('.drawer-overlay').classList.remove('active');
  document.body.style.overflow = '';
}

function applyFilters() {
  // Submit the search form
  document.getElementById('search').submit();
  closeFilterDrawer();
}

// Close drawer when clicking outside
document.querySelector('.drawer-overlay').addEventListener('click', closeFilterDrawer);