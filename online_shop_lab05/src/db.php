/**
 * Class Database
 *
 * This class provides methods for interacting with a MySQL database using PDO.
 * It includes functionality for executing queries, inserting products and their images,
 * and deleting products along with their associated image files.
 */
<?php
class Database {
    protected $pdo;

    public function __construct() {
        $config = require __DIR__ . '/../config/config.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function getPdo(): PDO {
        return $this->pdo;
    }

    /**
     * Executes a prepared query with parameters.
     *
     * @param string $sql SQL query.
     * @param array  $params Associative array of parameters.
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Inserts a record about a product into the products table.
     *
     * @param array $productData Product data.
     * @return bool
     */
    public function insertProduct(array $productData): bool {
        $sql = "INSERT INTO products (id, name, description, price, category, phone, region, is_bargain, created_at)
                VALUES (:id, :name, :description, :price, :category, :phone, :region, :is_bargain, :created_at)";
        $params = [
            ':id'          => $productData['id'],
            ':name'        => $productData['name'],
            ':description' => $productData['description'],
            ':price'       => $productData['price'],
            ':category'    => $productData['category'],
            ':phone'       => $productData['phone'],
            ':region'      => $productData['region'],
            ':is_bargain'  => $productData['is_bargain'] ? 1 : 0,
            ':created_at'  => $productData['created_at']
        ];

        $this->query($sql, $params);
        return true;
    }

    /**
     * Inserts image records associated with a product into the product_images table.
     *
     * @param string $productId Product ID.
     * @param array  $images Array of image paths.
     * @return bool
     */
    public function insertProductImages(string $productId, array $images): bool {
        $sql = "INSERT INTO product_images (product_id, image_path) VALUES (:product_id, :image_path)";
        foreach ($images as $imagePath) {
            $params = [
                ':product_id' => $productId,
                ':image_path' => $imagePath
            ];
            $this->query($sql, $params);
        }
        return true;
    }
    
    /**
     * Deletes a product by ID, including the deletion of image files.
     *
     * @param string $productId
     * @return bool
     */
    public function deleteProduct(string $productId): bool {
        // Получаем пути изображений для товара из product_images
        $sql = "SELECT image_path FROM product_images WHERE product_id = :product_id";
        $stmt = $this->query($sql, [':product_id' => $productId]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Для каждого изображения удаляем файл из файловой системы
        foreach ($images as $imagePath) {
            $filePath = __DIR__ . '/../../storage/' . $imagePath;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // Удаляем товар (ON DELETE CASCADE удалит записи из product_images)
        $sql = "DELETE FROM products WHERE id = :id";
        $this->query($sql, [':id' => $productId]);
        return true;
    }
}
