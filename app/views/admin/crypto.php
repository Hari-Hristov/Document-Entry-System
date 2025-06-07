<?php
namespace Admin;

class Crypto
{
    private string $encryptionMethod = 'AES-256-CBC';
    private ?string $key = null;
    private ?string $iv = null;
    private int $requiredKeys = 2; // Минимален брой ключове за отключване
    private int $totalKeys = 3;    // Общо ключове, разделени

    private array $keyParts = [];  // Събрани ключове от потребители

    public function __construct(int $requiredKeys = 2, int $totalKeys = 3)
    {
        $this->requiredKeys = $requiredKeys;
        $this->totalKeys = $totalKeys;
    }

    /**
     * Разделя главния ключ на N части (тук опростено чрез XOR).
     * В реален случай - Shamir Secret Sharing.
     */
    public function splitKey(string $masterKey): array
    {
        $keyParts = [];

        // Генерираме N-1 случайни части
        for ($i = 0; $i < $this->totalKeys - 1; $i++) {
            $keyParts[$i] = random_bytes(strlen($masterKey));
        }

        // Последната част е XOR между masterKey и всички други части
        $lastPart = $masterKey;
        foreach ($keyParts as $part) {
            $lastPart = $lastPart ^ $part; // XOR побитов оператор
        }
        $keyParts[$this->totalKeys - 1] = $lastPart;

        return $keyParts;
    }

    /**
     * Добавя част от ключа.
     * @param string $part - ключова част
     */
    public function addKeyPart(string $part): void
    {
        if (count($this->keyParts) < $this->totalKeys) {
            $this->keyParts[] = $part;
        }
    }

    /**
     * Проверява дали имаме достатъчно части, за да съберем ключа.
     */
    public function canRecoverKey(): bool
    {
        return count($this->keyParts) >= $this->requiredKeys;
    }

    /**
     * Възстановява ключа, ако има достатъчно части.
     * @return string|null - възстановен ключ или null
     */
    public function recoverKey(): ?string
    {
        if (!$this->canRecoverKey()) {
            return null;
        }

        // Оптимистично XOR всички налични части
        $recovered = $this->keyParts[0];
        for ($i = 1; $i < count($this->keyParts); $i++) {
            $recovered = $recovered ^ $this->keyParts[$i];
        }

        $this->key = $recovered;
        return $this->key;
    }

    /**
     * Шифрова съдържанието на файл.
     * @param string $inputFile - път до файла за криптиране
     * @param string $outputFile - път за съхранение на криптирания файл
     * @param string $masterKey - главен ключ (секретен)
     * @return bool
     */
    public function encryptFile(string $inputFile, string $outputFile, string $masterKey): bool
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->encryptionMethod));
        $this->key = $masterKey;

        $plaintext = file_get_contents($inputFile);
        if ($plaintext === false) {
            return false;
        }

        $ciphertext = openssl_encrypt($plaintext, $this->encryptionMethod, $this->key, OPENSSL_RAW_DATA, $this->iv);
        if ($ciphertext === false) {
            return false;
        }

        // Записваме IV + криптирания текст
        return file_put_contents($outputFile, $this->iv . $ciphertext) !== false;
    }

    /**
     * Дешифрова файл.
     * @param string $inputFile - криптиран файл
     * @param string $outputFile - място за съхранение на разшифрован файл
     * @param string $recoveredKey - възстановен ключ
     * @return bool
     */
    public function decryptFile(string $inputFile, string $outputFile, string $recoveredKey): bool
    {
        $data = file_get_contents($inputFile);
        if ($data === false) {
            return false;
        }

        $ivLen = openssl_cipher_iv_length($this->encryptionMethod);
        $this->iv = substr($data, 0, $ivLen);
        $ciphertext = substr($data, $ivLen);

        $plaintext = openssl_decrypt($ciphertext, $this->encryptionMethod, $recoveredKey, OPENSSL_RAW_DATA, $this->iv);
        if ($plaintext === false) {
            return false;
        }

        return file_put_contents($outputFile, $plaintext) !== false;
    }

    /**
     * Изчиства натрупаните ключове (например при рестарт).
     */
    public function reset(): void
    {
        $this->keyParts = [];
        $this->key = null;
        $this->iv = null;
    }
}
