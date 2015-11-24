<?php

/**
 * Apple Push Notifications Component
 *
 * A component that handles push notification for APPLE.
 *
 * PHP version 5
 *
 * @package		Apple Push Component
 * @author		Shazib Razzaq <shazib@techniques.com.pk>
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link		//comming soon
 */
App::import('Component', 'Controller');

/**
 * ApplePushComponent
 *
 * @package		ApplePushComponent
 */
class ApplePushComponent extends Component {

    /**
     * Default Push Notification mode to use: Test or Live
     *
     * @var string
     * @access public
     */
    public $mode = 'Test';
    
    /**
     * Default Push Notification passphrase to use: You set for permission file
     *
     * @var string
     * @access public
     */
    public $passphrase = '12345';
    
    
    /**
     * Default Push Notification certificate File path
     *
     * @var string
     * @access public
     */
    public $certificateFilePath = 'ck.pem';
    
    /**
     * Default trust certificate File path
     *
     * @var string
     * @access public
     */
    public $trustCertificatePath = 'entrust_2048_ca.cer';
   
    /**
     * Default Push Notification tag
     *
     * @var integer
     * @access public
     */
    
    public $tag = 1;
   

    /**
     * Controller startup. Sets options from
     * APP/Config/bootstrap.php.
     *
     * @param Controller $controller Instantiating controller
     * @return void
     * @throws CakeException
     */
    public function startup(Controller $controller) {
        $this->Controller = $controller;

        // if mode is set in bootstrap.php, use it. otherwise, Test.
        $mode = Configure::read('PushNotifications.mode');
        if ($mode) {
            $this->mode = $mode;
        }
        
        // if passphrase is set in bootstrap.php, use it. otherwise, chirpshow.
        $passphrase = Configure::read('PushNotifications.passphrase');
        if ($passphrase) {
            $this->passphrase = $passphrase;
        }
        
        // if certificateFilePath is set in bootstrap.php, use it. otherwise, ck.pem.
        $certificateFilePath = Configure::read('PushNotifications.certificateFilePath');
        if ($certificateFilePath) {
            $this->certificateFilePath = $certificateFilePath;
        }
        
        // if trustCertificatePath is set in bootstrap.php, use it. otherwise, ck.pem.
        $trustCertificatePath = Configure::read('PushNotifications.trustCertificatePath');
        if ($trustCertificatePath) {
            $this->trustCertificatePath = $trustCertificatePath;
        }
        
    }
    
    public function sendNotification($data) {
        $result = array();
        // $data MUST contain device_token to which notification is sent.
        if (!isset($data['device_token']) || empty($data['device_token'])) {
            return $result['error'] = 'Device token field is missing.';
        }
        
        // $data MUST contain message for the notification.
        if (!isset($data['message']) || empty($data['message'])) {
            return $result['error'] = 'Message field is missing.';
        }
        
        //check if notification tag is set
        if(!isset($data['tag']) && !empty($data['tag'])) {
            $this->tag  = $data['tag'];
        }
        
       
        
        if($this->mode == 'test') {
            $url = 'ssl://gateway.sandbox.push.apple.com:2195';
        } else {
            $url = 'ssl://gateway.push.apple.com:2195';
        }
        
        $ctx = stream_context_create();
        
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->certificateFilePath);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        stream_context_set_option($ctx, 'ssl', 'CAfile', $this->trustCertificatePath);
                
        // Open a connection to the APNS server
        $fp = stream_socket_client(
            $url, $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp) {
            return $result['error'] = "Failed to connect: $err $errstr" . PHP_EOL;
        }
        
        // Create the payload body
        $body['aps'] = array(
            'alert' => $data['message'],
            'tag' => $this->tag,
            'sound' => 'default',
            'badge' => 1
            );
        
        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $data['device_token']) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $res = fwrite($fp, $msg, strlen($msg));

        if (!$res)
            return $result['error'] = 'Message not delivered' . PHP_EOL;
        

        // Close the connection to the server
        fclose($fp);
        
    }

    
}