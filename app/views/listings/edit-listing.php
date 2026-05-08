<?php
$page_title = 'Edit Listing';
$listing_gallery_lines = implode("\n", dominium_listing_gallery_paths((int) $listing['id']));
$category_selected = $listing['category'];
$thumb_val = $listing['thumbnail'] ?? $listing['image'] ?? '';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold text-black mb-8">Edit Listing</h1>

        <form action="<?= url('listings/' . $listing['id'] . '/edit') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Title</label>
                    <input type="text" name="title" required value="<?= esc(dominium_form_value('title', $listing['title'])) ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                    <select id="province" name="province" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" data-selected="<?= esc(dominium_form_value('province', $listing['province'] ?? '')) ?>">
                        <option value="">Loading provinces...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <select id="city" name="city" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" data-selected="<?= esc(dominium_form_value('city', $listing['city'])) ?>">
                        <option value="">Select a province first</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <?php include APP_ROOT . '/app/views/partials/listing-category-select.php'; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price per Night (₱)</label>
                    <input type="number" name="price" step="0.01" required value="<?= esc(dominium_form_value('price', $listing['price'])) ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                    <input type="number" name="bedrooms" min="1" value="<?= esc(dominium_form_value('bedrooms', $listing['bedrooms'])) ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Capacity</label>
                    <input type="number" name="guests" min="1" value="<?= esc(dominium_form_value('guests', $listing['guests'])) ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" required rows="6" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black"><?= esc(dominium_form_value('description', $listing['description'])) ?></textarea>
            </div>

            <?php include APP_ROOT . '/app/views/partials/psgc-location.php'; ?>

            <div class="space-y-6 border-t border-gray-200 pt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                    <p class="text-xs text-gray-600 mb-3">Upload a new file to replace, or edit the URL. Used on cards and as the main hero photo.</p>

                    <input type="file" id="thumbnail_image_input" name="thumbnail_image" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,image/bmp,image/tiff,image/x-icon,image/vnd.microsoft.icon,image/avif" class="hidden" tabindex="-1">

                    <button type="button" id="thumbnail_dropzone" class="relative w-full min-h-[140px] rounded border-2 border-dashed border-gray-300 bg-gray-50 hover:border-gray-400 transition flex flex-col items-center justify-center gap-2 px-6 py-6 cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                        <span class="text-gray-500 text-sm pointer-events-none">Drop new thumbnail or click to upload</span>
                        <img id="thumbnail_preview" src="" alt="" class="hidden max-h-36 max-w-full rounded object-contain mt-1 shadow-sm border border-gray-200 bg-white p-1">
                        <span id="thumbnail_filename" class="hidden text-xs text-gray-600 font-medium"></span>
                    </button>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail URL / path</label>
                        <input type="text" name="thumbnail" id="thumbnail_url" value="<?= esc(dominium_form_value('thumbnail', $thumb_val)) ?>" placeholder="https://... or uploads/listings/..." class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery</label>
                    <p class="text-xs text-gray-600 mb-3">URLs below replace the saved gallery. Append new uploads when you pick files. Edit lines to remove photos.</p>

                    <input type="file" id="gallery_images_input" name="gallery_images[]" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,image/bmp,image/tiff,image/x-icon,image/vnd.microsoft.icon,image/avif" multiple class="hidden" tabindex="-1">

                    <button type="button" id="gallery_dropzone" class="relative w-full min-h-[100px] rounded border-2 border-dashed border-gray-200 bg-white hover:border-gray-400 transition flex flex-col items-center justify-center gap-1 px-6 py-5 cursor-pointer text-center focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">
                        <span class="text-sm text-gray-600 pointer-events-none">Add more gallery images (drop or click)</span>
                        <span id="gallery_count" class="text-xs text-gray-500 pointer-events-none">New files: none</span>
                    </button>
                    
                    <div id="gallery_previews" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 hidden">
                        <!-- Gallery previews will be inserted here -->
                    </div>

                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gallery URLs / paths <span class="text-gray-400 font-normal">(one per line)</span></label>
                        <textarea name="gallery_urls" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black text-sm font-mono"><?= esc(dominium_form_value('gallery_urls', $listing_gallery_lines)) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row">
                <button type="submit" class="flex-1 px-6 py-4 bg-black text-white font-semibold rounded hover:bg-gray-800 transition text-lg">
                    Save Changes
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
    dz.addEventListener('dragover', function (e) { e.preventDefault(); dz.classList.add('border-black', 'bg-white'); });
    dz.addEventListener('dragleave', function (e) { e.preventDefault(); dz.classList.remove('border-black', 'bg-white'); });
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

(function () {
    const dz = document.getElementById('gallery_dropzone');
    const input = document.getElementById('gallery_images_input');
    const countEl = document.getElementById('gallery_count');
    if (!dz || !input || !countEl) return;
    const dt = new DataTransfer();
    function syncInput() {
        input.files = dt.files;
        const n = dt.files.length;
        countEl.textContent = 'New files: ' + (n === 0 ? 'none' : (n + ' image(s)'));
    }
    function addFiles(fileList) {
        for (let i = 0; i < fileList.length; i++) {
            const f = fileList[i];
            if (f.type.startsWith('image/')) dt.items.add(f);
        }
        syncInput();
    }
    dz.addEventListener('click', function () { input.click(); });
    dz.addEventListener('dragover', function (e) { e.preventDefault(); dz.classList.add('border-black', 'bg-gray-50'); });
    dz.addEventListener('dragleave', function (e) { e.preventDefault(); dz.classList.remove('border-black', 'bg-gray-50'); });
    dz.addEventListener('drop', function (e) {
        e.preventDefault();
        dz.classList.remove('border-black', 'bg-gray-50');
        if (e.dataTransfer.files && e.dataTransfer.files.length) addFiles(e.dataTransfer.files);
    });
    input.addEventListener('change', function () {
        if (this.files && this.files.length) addFiles(this.files);
    });
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
            countEl.textContent = 'New files: none';
        } else {
            previewsContainer.classList.remove('hidden');
            countEl.textContent = `${galleryFiles.length} new file${galleryFiles.length !== 1 ? 's' : ''} selected`;
            
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
