<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Онлайн-магазин' ?></title>
</head>
<body>
    <header>
        <nav>
            <a href="/">Главная</a> |
            <a href="/product">Все товары</a> |
            <a href="/product/create">Добавить товар</a>
        </nav>
    </header>
    <main>
        <?= $content ?? '' ?>
    </main>
</body>
</html>
