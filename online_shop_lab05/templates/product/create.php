<?php
include __DIR__ . '/../../src/helpers.php';
ob_start();
?>
<h1>Добавить товар</h1>
<form action="/product/create" method="post" enctype="multipart/form-data">
    <label for="images">Фото товара (до 3 файлов):</label><br>
    <input type="file" name="images[]" id="images" multiple required><br><br>

    <label for="name">Название:</label><br>
    <input type="text" name="name" id="name" required><br><br>

    <label for="description">Описание:</label><br>
    <textarea name="description" id="description" required></textarea><br><br>

    <label for="price">Цена:</label><br>
    <input type="number" name="price" id="price" required><br><br>

    <label for="category">Категория:</label><br>
    <input type="text" name="category" id="category" required><br><br>

    <label for="phone">Номер телефона продавца:</label><br>
    <input type="tel" name="phone" id="phone" placeholder="+373xxxxxxxx" required><br><br>

    <label for="region">Регион:</label><br>
    <select name="region" id="region" required>
        <option value="" disabled selected>Выберите регион</option>
        <?php foreach ($regions as $region): ?>
            <option value="<?= htmlspecialchars($region) ?>"><?= htmlspecialchars($region) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>
        <input type="checkbox" name="is_bargain" value="1">
        Возможен торг
    </label><br><br>

    <button type="submit">Добавить товар</button>
</form>
<?php
$content = ob_get_clean();
$title = "Добавить товар";
include __DIR__ . '/../layout.php';
?>
