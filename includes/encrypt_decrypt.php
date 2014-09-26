<?php
//////////////////////////////////////////////////////////////////////////////////////////
// rsa crypto function expects -
// 1 - input_string_to_be_encrypted/decrypted
// 2 - 1 for encrypt, 0 for decrypt
// 3 - the fully qualified pem file name
// returns -
// an encrypted/decrypted string
// or
// false on falure
//////////////////////////////////////////////////////////////////////////////////////////
function encrypt_decrypt ($input_string, $encrypt, $pem_file) {
	require_once('../../Crypt/RSA.php');
	if (!isset($input_string)) return (false);
	if (!isset($encrypt)) return (false);
	if (!isset($pem_file)) return (false);

	$rsa = new Crypt_RSA();
	$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
	$rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);
	$rsa->loadKey(file_get_contents($pem_file));
	if ($encrypt){
		$return = base64_encode($rsa->encrypt($input_string));
		unset($rsa);
		return ($return);
	}else{
		$rsa->loadKey($rsa->getPublicKey());
		$return = $rsa->decrypt(base64_decode($input_string));
		unset($rsa);
		return ($return);
	}
}
?>
