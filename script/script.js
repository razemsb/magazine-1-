const modal = document.getElementById('modal');
const openModalBtn = document.getElementById('openModal');
const closeBtn = document.getElementsByClassName('close')[0];
openModalBtn.onclick = function() {
  modal.style.display = 'block';
}
closeBtn.onclick = function() {
  modal.style.display = 'none';
}
window.onclick = function(event) {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
}
function OpenModal(productId) {
    document.body.style.overflow = 'hidden';
    fetch('get_product_details.php?id=' + productId)
      .then(response => response.json())
      .then(data => {
        document.getElementById('modalTitle').innerText = data.title;
        document.getElementById('modalBody').innerHTML = `
          <p><strong>Описание:</strong> ${data.description}</p>
          <p><strong>Версия:</strong> ${data.version}</p>
          <p><strong>Цена:</strong> ${data.price} ₽</p>
          <p><strong>Категория:</strong> ${data.category}</p>
          ${data.image_path ? `<img src="${data.image_path}" class="img-fluid" alt="${data.title}">` : ''}
        `;
        document.getElementById('productModal').style.display = 'block';
      });
  }
  function closeModal() {
    document.body.style.overflow = '';
    document.getElementById('productModal').style.display = 'none';
  }
  setTimeout(function() {
    document.getElementById('error').style.opacity = '0';
    setTimeout(function() {
        document.getElementById('error').remove();
    }, 500);
}, 5000);
function searchProducts() {
  let searchInput = document.getElementById('searchInput');
  let searchResults = document.getElementById('searchResults');

  let xhr = new XMLHttpRequest();
  xhr.open('GET', 'search.php?q=' + encodeURIComponent(searchInput.value));
  xhr.onload = function () {
      if (xhr.status === 200) {
          searchResults.innerHTML = xhr.responseText;
      } else {
          searchResults.innerHTML = 'Error ' + xhr.status;
      }
  };
  xhr.send();
}