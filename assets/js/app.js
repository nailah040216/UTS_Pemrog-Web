document.addEventListener('DOMContentLoaded', () => {
  // 1. Validasi form interaktif (cegah spasi kosong & tampilkan pesan validasi)
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', event => {
      form.querySelectorAll('input[required], textarea[required]').forEach(field => {
        if (field.type !== 'password' && field.value.trim() === '') {
          field.value = '';
        }
      });

      if (!form.checkValidity()) {
        event.preventDefault();
        form.reportValidity();
      }
    });
  });

  // 2. Pencegahan angka negatif secara real-time pada input stok
  document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', () => {
      if (input.value !== '' && Number(input.value) < 0) {
        input.value = '0';
      }
    });
  });

  // 3. Auto-hide & klik untuk menutup notifikasi flash (.alert)
  const alertBox = document.querySelector('.alert');
  if (alertBox) {
    alertBox.style.cursor = 'pointer';
    alertBox.title = 'Klik untuk menutup';
    alertBox.addEventListener('click', () => alertBox.remove());

    setTimeout(() => {
      alertBox.style.transition = 'opacity 0.4s ease';
      alertBox.style.opacity = '0';
      setTimeout(() => alertBox.remove(), 400);
    }, 4000);
  }

  // 4. Live preview gambar sampul saat admin mengisi URL gambar
  const addImageInput = document.querySelector('.manage-layout form input[name="image_url"]');
  if (addImageInput) {
    const previewImg = document.createElement('img');
    previewImg.alt = 'Pratinjau sampul';
    previewImg.style.cssText = 'display:none; width:100px; height:140px; object-fit:cover; border-radius:8px; margin-top:8px; border:1px solid #ddd;';
    addImageInput.parentElement.appendChild(previewImg);

    addImageInput.addEventListener('input', () => {
      const url = addImageInput.value.trim();
      if (url) {
        previewImg.src = url;
        previewImg.style.display = 'block';
        previewImg.onerror = () => { previewImg.style.display = 'none'; };
      } else {
        previewImg.style.display = 'none';
      }
    });
  }
});