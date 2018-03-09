<?php 

/**
* 
*/
class SendController extends Controller
{
	
	public function actionSend(){
		require_once('protected/extensions/swiftMailer/lib/classes/Swift.php');
		Yii::registerAutoloader(array('Swift','autoload'));
		require_once('protected/extensions/swiftMailer/lib/swift_init.php');
        require_once('protected/extensions/swiftMailer/lib/swift_required.php');

        $transport = Swift_SmtpTransport::newInstance('smtp.ym.163.com', 25)
		  ->setUsername('join@dsjyw.net')
		  ->setPassword('hellojoin');

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance('Wonderful Subject')
		  ->setFrom(array('join@dsjyw.net' => 'Hello'))
	      ->setTo(array('710358590@qq.com' => 'Recipient Name'))
	      ->setBody('111111');

		$result = $mailer->send($message);
	}
}