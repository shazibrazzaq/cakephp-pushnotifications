# cakephp-pushnotifications

How to install
--------------

Copy the component in app/Controller/Component folder

For the controller you want to use it, add component like this
```
var $components = array('ApplePush');
```
In your app/Config/bootstrap.php

you need to do these settings

```
Configure::write('PushNotifications.mode', 'live'); //for live = live, OR test for test
Configure::write('PushNotifications.passphrase', 'the passpharase you set while creating the certificate permission file.'); 
Configure::write('PushNotifications.certificateFilePath', '//the path of the certificate file'); // e.g. WWW_ROOT . 'utility_files' . DS . 'apns-prod.pem' 
Configure::write('PushNotifications.trustCertificatePath', '//path to trust certificate file'); // e.g. WWW_ROOT . 'utility_files' . DS . 'entrust_2048_ca.cer' 
```
you can get this certficate from curl -O https://www.entrust.net/downloads/binary/entrust_2048_ca.cer

Example of your controller function where you want to send notifiation

```
function test_push_notification(){
    $data['device_token'] = 'device token of your device';
    $data['message'] = 'Hello this is testing from cakephp';
    $data['tag'] = "1";
    $result = $this->ApplePush->sendNotification($data);
    if (isset($result['error'])) {
        echo $result['error'];
    } else {
    	echo "Notification sent successfully!";
    }
} 
```

Some Common commands for creating the permission certificate

Development
---------------

Export private key with name: apns-dev-key
Export certificate with name: apns-dev-cert

Use these commands one by one...

```
openssl pkcs12 -clcerts -nokeys -out apns-dev-cert.pem -in apns-dev-cert.p12
openssl pkcs12 -nocerts -out apns-dev-key.pem -in apns-dev-key.p12
openssl rsa -in apns-dev-key.pem -out apns-dev-key-noenc.pem
cat apns-dev-cert.pem apns-dev-key-noenc.pem > apns-dev.pem
```

Production
------------------

Export private key with name: apns-prod-key
Export certificate with name: apns-prod-cert

Use these commands one by one...

```
openssl pkcs12 -clcerts -nokeys -out apns-prod-cert.pem -in apns-prod-cert.p12
openssl pkcs12 -nocerts -out apns-prod-key.pem -in apns-prod-key.p12
openssl rsa -in apns-prod-key.pem -out apns-prod-key-noenc.pem
cat apns-prod-cert.pem apns-prod-key-noenc.pem > apns-prod.pem
```


If you need any help you can contact me at shazib@techniques.com.pk



