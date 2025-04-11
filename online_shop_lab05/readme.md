Подготовка среды
Поднимаю быстренько LAMP

mysql --version
mysql  Ver 8.0.41-0ubuntu0.24.04.1 for Linux on x86_64 ((Ubuntu))

php -v
PHP 8.3.6 (cli)

apachectl -V
Server version: Apache/2.4.58 (Ubuntu)

Создаю базу данных под названием online_shop
Создайте таблицу product со следующей структурой

```sql
CREATE DATABASE online_shop;
```
Query OK

```sql
CREATE TABLE products (
    id VARCHAR(32) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100),
    phone VARCHAR(20),
    region VARCHAR(100),
    is_bargain BOOLEAN DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```
Query OK

```sql
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(32),
    image_path VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```
Query OK


