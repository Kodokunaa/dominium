<?php
$page_title = 'Create Listing';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold text-black mb-4">Create New Listing</h1>
        <p class="text-sm text-gray-600 mb-8">New listings are reviewed by an administrator before they appear publicly.</p>

        <form action="<?= url('listings/create') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Title</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="e.g., Luxury Apartment in Downtown" value="<?= esc(dominium_form_value('title')) ?>">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                    <select id="province" name="province" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" data-selected="<?= esc(dominium_form_value('province')) ?>">
                        <option value="">Loading provinces...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <select id="city" name="city" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                        <option value="">Select a province first</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <?php include APP_ROOT . '/app/views/partials/listing-category-select.php'; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price per Night (₱)</label>
                    <input type="number" name="price" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="99.00" value="<?= esc(dominium_form_value('price')) ?>">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                    <input type="number" name="bedrooms" min="1" value="<?= esc(dominium_form_value('bedrooms', '1')) ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Capacity</label>
                    <input type="number" name="guests" min="1" value="<?= esc(dominium_form_value('guests', '1')) ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" required rows="6" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="Describe your property, amenities, and what makes it special..."><?= esc(dominium_form_value('description')) ?></textarea>
            </div>

            <?php include APP_ROOT . '/app/views/partials/psgc-location.php'; ?>

            <div class="space-y-6 border-t border-gray-200 pt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail <span class="text-gray-400 font-normal">(listing card & hero)</span></label>
                    <p class="text-xs text-gray-600 mb-3">Shown on search results and as the main photo. Upload, drag & drop, or paste a URL. Defaults if empty.</p>

                    <input type="file" id="thumbnail_image_input" name="thumbnail_image" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,image/bmp,image/tiff,image/x-icon,image/vnd.microsoft.icon,image/avif" class="hidden" tabindex="-1">

                    <button type="button" id="thumbnail_dropzone" class="relative w-full min-h-[160px] rounded border-2 border-dashed border-gray-300 bg-gray-50 hover:border-gray-400 hover:bg-gray-100/80 transition flex flex-col items-center justify-center gap-2 px-6 py-8 cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                        <span class="text-gray-500 text-sm pointer-events-none">
                            <span class="font-medium text-black">Drop thumbnail here</span>
                            <span class="block mt-1">or click to choose</span>
                        </span>
                        <span class="text-xs text-gray-400 pointer-events-none">JPG, PNG, WebP, GIF, SVG, BMP, TIFF, ICO, AVIF · max 5 MB</span>
                        <img id="thumbnail_preview" src="" alt="" class="hidden max-h-40 max-w-full rounded object-contain mt-2 shadow-sm border border-gray-200 bg-white p-1">
                        <span id="thumbnail_filename" class="hidden text-xs text-gray-600 font-medium mt-1"></span>
                    </button>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail URL <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="url" name="thumbnail" id="thumbnail_url" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="https://example.com/photo.jpg" value="<?= esc(dominium_form_value('thumbnail')) ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery <span class="text-gray-400 font-normal">(optional)</span></label>
                    <p class="text-xs text-gray-600 mb-3">Additional photos on the listing page. Drag & drop multiple images, use the file picker, and/or add one HTTPS URL per line below.</p>

                    <input type="file" id="gallery_images_input" name="gallery_images[]" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,image/bmp,image/tiff,image/x-icon,image/vnd.microsoft.icon,image/avif" multiple class="hidden" tabindex="-1">

                    <button type="button" id="gallery_dropzone" class="relative w-full min-h-[120px] rounded border-2 border-dashed border-gray-200 bg-white hover:border-gray-400 transition flex flex-col items-center justify-center gap-1 px-6 py-6 cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                        <span class="text-sm text-gray-600 pointer-events-none">Drop gallery images here or click to add files</span>
                        <span id="gallery_count" class="text-xs text-gray-500 pointer-events-none">No files selected</span>
                    </button>
                    
                    <div id="gallery_previews" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 hidden">
                        <!-- Gallery previews will be inserted here -->
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gallery image URLs <span class="text-gray-400 font-normal">(one per line)</span></label>
                        <textarea name="gallery_urls" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black text-sm" placeholder="https://example.com/a.jpg&#10;https://example.com/b.jpg"><?= esc(dominium_form_value('gallery_urls')) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 px-6 py-4 bg-black text-white font-semibold rounded hover:bg-gray-800 transition text-lg">
                    Publish Listing
                </button>
                <a href="<?= url('my-listings') ?>" class="flex-1 px-6 py-4 border border-gray-300 text-black font-semibold rounded hover:bg-gray-50 transition text-lg text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<script>
(function () {
    const dz = document.getElementById('thumbnail_dropzone');
    const input = document.getElementById('thumbnail_image_input');
    const preview = document.getElementById('thumbnail_preview');
    const filenameEl = document.getElementById('thumbnail_filename');
    const urlField = document.getElementById('thumbnail_url');
    if (!dz || !input) return;

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function () {
            preview.src = reader.result;
            preview.classList.remove('hidden');
            filenameEl.textContent = file.name;
            filenameEl.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function clearFile() {
        input.value = '';
        preview.src = '';
        preview.classList.add('hidden');
        filenameEl.textContent = '';
        filenameEl.classList.add('hidden');
    }

    dz.addEventListener('click', function (e) {
        if (e.target.closest('#thumbnail_preview')) return;
        input.click();
    });

    dz.addEventListener('dragover', function (e) {
        e.preventDefault();
        dz.classList.add('border-black', 'bg-white');
    });
    dz.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dz.classList.remove('border-black', 'bg-white');
    });
    dz.addEventListener('drop', function (e) {
        e.preventDefault();
        dz.classList.remove('border-black', 'bg-white');
        const file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (!file) return;
        try {
            input.files = e.dataTransfer.files;
        } catch (err) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
        }
        if (urlField) urlField.value = '';
        showPreview(file);
    });

    input.addEventListener('change', function () {
        const file = input.files && input.files[0];
        if (file) {
            if (urlField) urlField.value = '';
            showPreview(file);
        }
    });

    if (urlField) {
        urlField.addEventListener('input', function () {
            if (urlField.value.trim()) clearFile();
        });
    }
})();

// Gallery Preview Functionality
(function () {
    const dz = document.getElementById('gallery_dropzone');
    const input = document.getElementById('gallery_images_input');
    const previewsContainer = document.getElementById('gallery_previews');
    const countEl = document.getElementById('gallery_count');
    if (!dz || !input || !previewsContainer || !countEl) return;

    let galleryFiles = [];

    function createPreviewElement(file, index) {
        const div = document.createElement('div');
        div.className = 'relative group';
        div.innerHTML = `
            <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                <img src="" alt="${file.name}" class="w-full h-full object-cover">
            </div>
            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-xs hover:bg-red-600">
                ×
            </button>
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-75 text-white text-xs p-1 truncate">
                ${file.name}
            </div>
        `;
        
        const img = div.querySelector('img');
        const removeBtn = div.querySelector('button');
        
        // Load image preview
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function () {
                img.src = reader.result;
            };
            reader.readAsDataURL(file);
        }
        
        // Remove image functionality
        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            removeImage(index);
        });
        
        return div;
    }

    function updateGalleryDisplay() {
        previewsContainer.innerHTML = '';
        
        if (galleryFiles.length === 0) {
            previewsContainer.classList.add('hidden');
            countEl.textContent = 'No files selected';
        } else {
            previewsContainer.classList.remove('hidden');
            countEl.textContent = `${galleryFiles.length} file${galleryFiles.length !== 1 ? 's' : ''} selected`;
            
            galleryFiles.forEach((file, index) => {
                previewsContainer.appendChild(createPreviewElement(file, index));
            });
        }
    }

    function removeImage(index) {
        galleryFiles.splice(index, 1);
        
        // Update the file input
        const dt = new DataTransfer();
        galleryFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
        
        updateGalleryDisplay();
    }

    function addFiles(files) {
        const newFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
        galleryFiles = [...galleryFiles, ...newFiles];
        updateGalleryDisplay();
    }

    dz.addEventListener('click', function (e) {
        if (e.target.closest('#gallery_previews')) return;
        // Prevent triggering during form submission
        if (e.target.tagName === 'BUTTON' && e.target.type === 'submit') return;
        input.click();
    });

    dz.addEventListener('dragover', function (e) {
        e.preventDefault();
        dz.classList.add('border-black', 'bg-white');
    });

    dz.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dz.classList.remove('border-black', 'bg-white');
    });

    dz.addEventListener('drop', function (e) {
        e.preventDefault();
        dz.classList.remove('border-black', 'bg-white');
        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            addFiles(files);
            
            // Update file input
            const dt = new DataTransfer();
            galleryFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }
    });

    input.addEventListener('change', function () {
        const files = input.files;
        if (files && files.length > 0) {
            addFiles(files);
        }
    });
})();
</script>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
