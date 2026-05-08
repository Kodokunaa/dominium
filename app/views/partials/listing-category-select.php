<?php
$__cat_sel = isset($category_selected) ? (string) $category_selected : dominium_form_value('category');
?>
<select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
    <option value="">Select a category</option>
    <?php foreach (dominium_listing_categories() as $cat): ?>
        <option value="<?= esc($cat) ?>" <?= $__cat_sel === $cat ? 'selected' : '' ?>><?= esc($cat) ?></option>
    <?php endforeach; ?>
</select>
