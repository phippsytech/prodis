Accounts

This area is for managing accounts for NDISmate.


accounts database stores 
NDIS Provider Number
Provider Name
email
cname -> [subdomain].ndismate.app
subdomain can be a max of 63 characters


manage subscriptions
manage billing
manage support requests (ticket system)

env database stores environment data for each client.  Data in the environment database is only available locally



To use NDISmate, providers set up an account
That account:
Sets up a unique database for provider data
Generates unique access codes and keypair to protect access

needs a CNAME.  
By default, this is a CNAME record providername.ndismate.app

Clients may choose to use their own custom CNAME from their own domain

We would need to set up SSL certificates

this CNAME will be added to the list of origins accessing the app and be used 
to load the settings specific to the provider.

eg:
crm.crystelcare.com.au -> crystelcare.ndismate.app
create mysql database and user

Database name is lowercase a-z0-9.  prefixed with p followed by 7 characters
p2a4h6z8 <-- this will be a hashid derived from the id for the provider
use Hashids\Hashids;
function generateHash($input, $salt) {
    // Initialize the Hashids library with the desired configuration
    $hashids = new Hashids($salt, 7, 'abcdefghijklmnopqrstuvwxyz0123456789');

    // Encode the input to generate the hash
    $hash = $hashids->encode($input);

    return $hash;
}



usernames are lowercase a-z0-9.  prefixed with u followed by 22 characters

function generateUsername() {
    // Generate 16 bytes (128 bits) of random data
    $randomBytes = random_bytes(16);

    // Convert the random bytes to a hexadecimal string
    $hexString = bin2hex($randomBytes);

    // Convert the hexadecimal string to a decimal (base-10) number
    $decimalValue = gmp_init($hexString, 16);

    // Convert the decimal number to a base-36 string
    $username = gmp_strval($decimalValue, 36);

    return $username;
}

passwords are uppercase A-Z, lowercase a-z 0-9 - _ hyphen and underscore
function generatePassword() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_';
    $passwordLength = 22;  // Given 64 characters, this length provides approximately 128 bits of entropy
    $password = '';

    for ($i = 0; $i < $passwordLength; $i++) {
        $randomIndex = random_int(0, 63);  // Generate a random index between 0 and 63
        $password .= $characters[$randomIndex];
    }

    return $password;
}

create mongodb collection and user
create environment file
create ssl certificate
create cname
manage database backups
manage subscription
manage billing
manage support requests (ticket system)