<?php
namespace NDISmate\CORE;

class EncryptionManager
{
    private $encryptionKey;

    public function __construct(string $encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    /**
     * Decrypts the given encrypted data using the provided encryption key.
     *
     * @param string $encryptedData The encrypted data.
     * @param string $encryptionKey The encryption key.
     * @return string The decrypted data.
     */
    public function decryptData(string $encryptedData): string
    {
        // Extract the initialization vector (IV) from the beginning of the encrypted data
        $iv = substr($encryptedData, 0, openssl_cipher_iv_length('aes-256-cbc'));
        // Extract the actual encrypted data (excluding the IV)
        $encryptedData = substr($encryptedData, openssl_cipher_iv_length('aes-256-cbc'));
        // Decrypt the data using the encryption key and IV
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
    }

    /**
     * Encrypts the given data using the provided encryption key.
     *
     * @param string $data The data to encrypt.
     * @param string $encryptionKey The encryption key.
     * @return string The encrypted data.
     */
    public function encryptData(string $data): string
    {
        // Generate a random initialization vector (IV) for the encryption
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        // Encrypt the data using the encryption key and IV
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        // Concatenate the IV and the encrypted data
        return $iv . $encryptedData;
    }
}
