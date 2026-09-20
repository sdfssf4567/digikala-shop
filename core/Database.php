<?php
/**
 * ===============================================
 * کلاس اتصال به دیتابیس (الگوی Singleton)
 * استفاده از PDO با کوئری‌های آماده (Prepared Statement)
 * برای جلوگیری کامل از حمله SQL Injection
 * ===============================================
 */

class Database
{
    /** نمونه یکتای کلاس */
    private static ?Database $instance = null;

    /** اتصال PDO */
    private PDO $pdo;

    /**
     * سازنده خصوصی - جلوگیری از ساخت نمونه بیرون از کلاس
     */
    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            // خطاهای PDO به صورت Exception نمایش داده می‌شوند
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // نتایج به صورت آرایه انجمنی برگردانده می‌شوند
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // شبیه‌سازی کوئری آماده غیرفعال تا از تزریق SQL جلوگیری شود
            PDO::ATTR_EMULATE_PREPARES   => false,
            // اتصال پایدار برای بهبود سرعت
            PDO::ATTR_PERSISTENT         => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // در صورت خطای اتصال، پیام مناسب نمایش داده می‌شود
            if (DEBUG) {
                die('خطا در اتصال به دیتابیس: ' . htmlspecialchars($e->getMessage()));
            }
            die('خطا در اتصال به دیتابیس! لطفاً فایل config/config.php را بررسی کنید و مطمئن شوید MySQL در XAMPP اجرا شده است.');
        }
    }

    /**
     * دریافت نمونه یکتا از کلاس دیتابیس
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * دریافت اتصال خام PDO (برای تراکنش‌ها)
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * اجرای کوئری با پارامترهای امن
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * دریافت یک ردیف از نتیجه کوئری
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /**
     * دریافت همه ردیف‌های نتیجه کوئری
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * دریافت مقدار تک ستون (مثلاً برای COUNT)
     */
    public function fetchColumn(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * شناسه آخرین رکورد درج شده
     */
    public function lastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * شروع تراکنش (برای عملیات چند مرحله‌ای مثل ثبت سفارش)
     */
    public function beginTransaction(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    /**
     * تأیید تراکنش
     */
    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    /**
     * برگشت تراکنش در صورت بروز خطا
     */
    public function rollBack(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    /**
     * جلوگیری از کلون شدن کلاس
     */
    private function __clone() {}
}
